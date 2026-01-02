<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

if (defined('EXTRACT_STUDENT_INFO_INCLUDED')) {
    try {
        if (!class_exists('Database')) {
            require_once __DIR__ . '/../includes/database.php';
        }
        if (!class_exists('AppConfig')) {
            require_once __DIR__ . '/../includes/app_config.php';
        }
    } catch (Exception $e) {
        error_log('Configuration error in extract-student-info.php: ' . $e->getMessage());
    }
} else {
    if (!headers_sent()) {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Content-Type');
    }

    try {
        if (!class_exists('Database')) {
            require_once __DIR__ . '/../includes/database.php';
        }
        if (!class_exists('AppConfig')) {
            require_once __DIR__ . '/../includes/app_config.php';
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Configuration error: ' . $e->getMessage()]);
        exit;
    }

    $response = ['status' => 'error', 'message' => 'Invalid request'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_FILES['studentIdImage']) || $_FILES['studentIdImage']['error'] !== UPLOAD_ERR_OK) {
            $response['message'] = 'Student ID image is required';
            echo json_encode($response);
            exit;
        }
        
        if (!isset($_FILES['corFile']) || $_FILES['corFile']['error'] !== UPLOAD_ERR_OK) {
            $response['message'] = 'Certificate of Registration (COR) is required';
            echo json_encode($response);
            exit;
        }
        
        try {
        $studentIdFile = $_FILES['studentIdImage'];
        $corFile = $_FILES['corFile'];
        
        $allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/heic', 'image/heif', 'image/webp'];
        $allowedCorTypes = array_merge($allowedImageTypes, ['application/pdf']);
        
        if (!in_array($studentIdFile['type'], $allowedImageTypes)) {
            $response['message'] = 'Invalid Student ID format. Allowed: JPG, PNG, HEIC, WEBP';
            echo json_encode($response);
            exit;
        }
        
        if (!in_array($corFile['type'], $allowedCorTypes)) {
            $response['message'] = 'Invalid COR format. Allowed: PDF, JPG, PNG, HEIC, WEBP';
            echo json_encode($response);
            exit;
        }
        
        if ($studentIdFile['size'] > 5 * 1024 * 1024) {
            $response['message'] = 'Student ID file size must be less than 5MB';
            echo json_encode($response);
            exit;
        }
        
        if ($corFile['size'] > 10 * 1024 * 1024) {
            $response['message'] = 'COR file size must be less than 10MB';
            echo json_encode($response);
            exit;
        }
        
        $uploadDir = '../uploads/temp/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $tempId = uniqid('temp_', true);
        
        $studentIdExt = pathinfo($studentIdFile['name'], PATHINFO_EXTENSION);
        $studentIdPath = $uploadDir . $tempId . '_studentid.' . $studentIdExt;
        
        $corExt = pathinfo($corFile['name'], PATHINFO_EXTENSION);
        $corPath = $uploadDir . $tempId . '_cor.' . $corExt;
        
        if (!move_uploaded_file($studentIdFile['tmp_name'], $studentIdPath)) {
            $response['message'] = 'Failed to upload Student ID';
            echo json_encode($response);
            exit;
        }
        
        if (!move_uploaded_file($corFile['tmp_name'], $corPath)) {
            unlink($studentIdPath);
            $response['message'] = 'Failed to upload COR';
            echo json_encode($response);
            exit;
        }
        
        $validationResult = validateDocuments($studentIdPath, $corPath);
        
        if (!$validationResult['success']) {
            unlink($studentIdPath);
            unlink($corPath);
            $response['message'] = $validationResult['message'];
            echo json_encode($response);
            exit;
        }
        
        $extractedInfo = extractInformationFromCOR($corPath);
        
        if (!$extractedInfo['success']) {
            unlink($studentIdPath);
            unlink($corPath);
            $response['message'] = $extractedInfo['message'];
            echo json_encode($response);
            exit;
        }
        
        $response = [
            'status' => 'success',
            'message' => 'Documents uploaded successfully',
            'data' => [
                'tempId' => $tempId,
                'studentIdPath' => realpath($studentIdPath),
                'corPath' => realpath($corPath),
                'extractedInfo' => $extractedInfo['data']
            ]
        ];
        
        } catch (Exception $e) {
            $response['message'] = 'Processing failed: ' . $e->getMessage();
            error_log('Document extraction error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        }

        try {
            echo json_encode($response);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'JSON encoding failed: ' . $e->getMessage()]);
        }
    }
}

function validateDocuments($studentIdPath, $corPath) {
    $apiKey = AppConfig::get('OCR_SPACE_API_KEY', '');
    
    if (empty($apiKey)) {
        return [
            'success' => false,
            'message' => 'OCR service not configured'
        ];
    }
    
    $studentIdOcrText = performOCR($studentIdPath, $apiKey);
    $corOcrText = performOCR($corPath, $apiKey);
    
    if (empty($studentIdOcrText) || empty($corOcrText)) {
        return [
            'success' => false,
            'message' => 'Invalid document. Please upload valid and clear documents.'
        ];
    }
    
    $studentName = extractStudentName($studentIdOcrText);
    $hasSchoolName = validateSchoolName($corOcrText);
    
    if (empty($studentName) || !$hasSchoolName) {
        return [
            'success' => false,
            'message' => 'Invalid document. Please upload valid and clear documents.'
        ];
    }
    
    return [
        'success' => true
    ];
}

function extractStudentName($text) {
    $text = trim($text);
    if (empty($text)) {
        return '';
    }
    
    $patterns = [
        '/name\s*[:.\-\s]*([A-Z][a-z]+(?:\s+[A-Z][a-z]+)+)/i',
        '/student\s*name\s*[:.\-\s]*([A-Z][a-z]+(?:\s+[A-Z][a-z]+)+)/i',
        '/^([A-Z][a-z]+(?:\s+[A-Z][a-z]+)+)/m',
        '/([A-Z][a-z]+\s+[A-Z][a-z]+(?:\s+[A-Z][a-z]+)?)/',
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $text, $matches)) {
            $name = trim($matches[1]);
            if (strlen($name) >= 5 && preg_match('/^[A-Za-z\s]+$/', $name)) {
                $nameParts = preg_split('/\s+/', $name);
                if (count($nameParts) >= 2) {
                    error_log("Extracted Student Name: " . $name);
                    return $name;
                }
            }
        }
    }
    
    $lines = preg_split('/\n+/', $text);
    foreach ($lines as $line) {
        $line = trim($line);
        if (preg_match('/^[A-Z][a-z]+(?:\s+[A-Z][a-z]+)+$/', $line)) {
            $nameParts = preg_split('/\s+/', $line);
            if (count($nameParts) >= 2 && strlen($line) >= 5) {
                error_log("Extracted Student Name from line: " . $line);
                return $line;
            }
        }
    }
    
    error_log("Failed to extract Student Name from text");
    return '';
}

function validateSchoolName($text) {
    $primary = AppConfig::get('SCHOOL_NAME_PRIMARY', 'Laguna State Polytechnic University');
    $aliases = AppConfig::get('SCHOOL_NAME_ALIASES', '');
    
    $searchTerms = array_filter(array_map('trim', array_merge([$primary], explode(';', $aliases))));
    $textLower = strtolower($text);
    
    foreach ($searchTerms as $term) {
        if (!empty($term) && strpos($textLower, strtolower($term)) !== false) {
            error_log("Found school name match: " . $term);
            return true;
        }
    }
    
    error_log("School name validation failed. OCR text sample: " . substr($text, 0, 200));
    return false;
}

function extractInformationFromCOR($filePath) {
    $apiKey = AppConfig::get('OCR_SPACE_API_KEY', '');
    
    if (empty($apiKey)) {
        return [
            'success' => false,
            'message' => 'OCR service not configured'
        ];
    }
    
    $ocrText = '';
    $maxRetries = 2;
    $retryCount = 0;
    
    while (empty($ocrText) && $retryCount < $maxRetries) {
        if ($retryCount > 0) {
            error_log("OCR retry attempt {$retryCount} for file: {$filePath}");
            sleep(2);
        }
        $ocrText = performOCR($filePath, $apiKey);
        $retryCount++;
    }
    
    if (empty($ocrText)) {
        error_log("OCR failed after {$maxRetries} attempts for file: {$filePath}");
        return [
            'success' => false,
            'message' => 'Could not read text from COR. OCR service timeout or unavailable.'
        ];
    }
    
    $studentId = extractStudentId($ocrText);
    $course = extractCourse($ocrText);
    $yearLevel = extractYearLevel($ocrText);
    $gender = extractGender($ocrText);
    $academicYear = extractAcademicYear($ocrText);
    $semester = extractSemester($ocrText);
    
    error_log("=== EXTRACTION RESULTS ===");
    error_log("Student ID: " . ($studentId ?: 'NOT FOUND'));
    error_log("Course: " . ($course ?: 'NOT FOUND'));
    error_log("Year Level: " . ($yearLevel ?: 'NOT FOUND'));
    error_log("Gender: " . ($gender ?: 'NOT FOUND'));
    error_log("Academic Year: " . ($academicYear ?: 'NOT FOUND'));
    error_log("Semester: " . ($semester ?: 'NOT FOUND'));
    error_log("OCR Text Sample (first 800 chars): " . substr($ocrText, 0, 800));
    
    if (empty($academicYear) || empty($semester)) {
        error_log("=== DEBUGGING EXTRACTION ===");
        error_log("Full OCR Text Length: " . strlen($ocrText));
        error_log("OCR Text Sample (first 500 chars): " . substr($ocrText, 0, 500));
        
        if (preg_match('/a\.y\./i', $ocrText, $ayMatch)) {
            error_log("Found 'A.Y.' in text at position: " . strpos(strtolower($ocrText), 'a.y.'));
            preg_match('/a\.y\.\s*[:.\-\s,]*([0-9]{4}[\s\-]+[0-9]{4})/i', $ocrText, $ayYearMatch);
            if (!empty($ayYearMatch)) {
                error_log("A.Y. pattern matched: " . $ayYearMatch[0]);
            } else {
                error_log("A.Y. found but year pattern didn't match");
            }
        } else {
            error_log("'A.Y.' NOT found in OCR text");
        }
        
        if (preg_match('/first.*semester/i', $ocrText, $semMatch)) {
            error_log("Found 'first...semester' in text: " . substr($semMatch[0], 0, 50));
        } else {
            error_log("'first...semester' NOT found in OCR text");
        }
        
        if (preg_match('/semester/i', $ocrText)) {
            error_log("Found 'semester' word in text");
            preg_match_all('/semester[^,]*/i', $ocrText, $semesterMatches);
            error_log("Semester context matches: " . print_r($semesterMatches[0], true));
        }
    }
    
    return [
        'success' => true,
        'data' => [
            'studentId' => $studentId,
            'course' => $course,
            'yearLevel' => $yearLevel,
            'gender' => $gender,
            'academicYear' => $academicYear,
            'semester' => $semester,
            'ocrText' => substr($ocrText, 0, 500),
            'debug' => [
                'foundStudentId' => !empty($studentId),
                'foundCourse' => !empty($course),
                'foundYearLevel' => !empty($yearLevel),
                'foundGender' => !empty($gender),
                'foundAcademicYear' => !empty($academicYear),
                'foundSemester' => !empty($semester)
            ]
        ]
    ];
}

function performOCR($filePath, $apiKey) {
    $endpoint = 'https://api.ocr.space/parse/image';
    
    if (!file_exists($filePath)) {
        error_log("performOCR: File does not exist: {$filePath}");
        return '';
    }
    
    $realPath = realpath($filePath);
    if (!$realPath) {
        error_log("performOCR: Could not resolve realpath for: {$filePath}");
        return '';
    }
    
    $ext = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));
    $isPdf = ($ext === 'pdf');
    
    error_log("performOCR: Processing file: {$realPath} (exists: " . (file_exists($realPath) ? 'YES' : 'NO') . ")");
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $fileField = curl_file_create(
        $realPath,
        $isPdf ? 'application/pdf' : (mime_content_type($realPath) ?: 'image/jpeg'),
        basename($realPath)
    );
    
    $postFields = [
        'file' => $fileField,
        'language' => 'eng',
        'OCREngine' => '2',
        'scale' => 'true',
        'isOverlayRequired' => 'true',
        'isTable' => $isPdf ? 'true' : 'false'
    ];
    
    if ($isPdf) {
        $postFields['filetype'] = 'PDF';
    }
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['apikey: ' . $apiKey]);
    
    $response = curl_exec($ch);
    
    if ($response === false) {
        error_log('OCR API Error: ' . curl_error($ch));
        curl_close($ch);
        return '';
    }
    
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if (!is_array($data) || empty($data['ParsedResults'][0])) {
        error_log('OCR parsing failed: ' . print_r($data, true));
        return '';
    }
    
    $text = $data['ParsedResults'][0]['ParsedText'] ?? '';
    error_log('OCR extracted text: ' . substr($text, 0, 300));
    
    return $text;
}

function extractStudentId($text) {
    $excludePatterns = [
        '/202[0-9]-202[0-9]/',
        '/\b20[0-9]{2}\s*-\s*20[0-9]{2}\b/',
    ];
    
    foreach ($excludePatterns as $exclude) {
        $text = preg_replace($exclude, '', $text);
    }
    
    $patterns = [
        '/student\s*no\.?\s*[:.\s]*\**\s*([0-9]{4}[-\s]?[0-9]{4})\b/i',
        '/student\s*(?:id|no\.?|number|num)\s*[:.\s]*\**\s*([0-9]{4}[-\s.]?[0-9]{4})\b/i',
        '/\bstudent\s*no\.?\s*[:.\-\s]*([0-9]{4}[-\s]?[0-9]{4})\b/i',
        '/(?:^|\n)\s*([0-9]{4}[-\s][0-9]{4})\s*(?:\n|$)/m',
        '/student\s*(?:id|no\.?|number|num|#)[\s:.\-]*([0-9]{4}[-\s.]?[0-9]{4,5})/i',
        '/(?:id|no\.?|number|num)[\s:.\-]*([0-9]{4}[-\s.]?[0-9]{4,5})/i',
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $text, $matches)) {
            $id = trim($matches[1]);
            $id = preg_replace('/[\s._]+/', '-', $id);
            $id = preg_replace('/[^0-9-]/', '', $id);
            $id = preg_replace('/-+/', '-', $id);
            
            if (preg_match('/^(19|20)[0-9]{2}-(19|20)[0-9]{2}$/', $id)) {
                error_log("Skipping academic year pattern: " . $id);
                continue;
            }
            
            if (preg_match('/^\d{4}-?\d{4}$/', $id)) {
                if (strpos($id, '-') === false && strlen($id) === 8) {
                    $id = substr($id, 0, 4) . '-' . substr($id, 4);
                }
                
                $firstPart = substr($id, 0, 4);
                if ($firstPart >= '2020' && $firstPart <= '2030') {
                    error_log("Skipping likely academic year: " . $id);
                    continue;
                }
                
                error_log("Extracted Student ID (8-digit): " . $id);
                return $id;
            }
            
            if (preg_match('/^\d{4}-?\d{5}$/', $id)) {
                if (strpos($id, '-') === false && strlen($id) === 9) {
                    $id = substr($id, 0, 4) . '-' . substr($id, 4);
                }
                error_log("Extracted Student ID (9-digit): " . $id);
                return $id;
            }
        }
    }
    
    error_log("Failed to extract Student ID from text");
    return '';
}

function extractCourse($text) {
    $text = strtolower($text);
    
    if (preg_match('/\b(bsit|bs\s*it|bachelor.*information\s*technology)\b/i', $text)) {
        return 'BSIT';
    }
    
    if (preg_match('/\b(bscs|bs\s*cs|bachelor.*computer\s*science)\b/i', $text)) {
        return 'BSCS';
    }
    
    return 'BSIT';
}

function extractYearLevel($text) {
    $priorityPatterns = [
        '/year\s*level[\s:.\-]*[:*\s]*(first|second|third|fourth)\s*year/i',
        '/year\s*level[\s:.\-]*[:*\s]*([1-4])(?:st|nd|rd|th)?\s*year/i',
        '/year\s*level[\s:.\-]*[:*\s]*([1-4])/i',
    ];
    
    foreach ($priorityPatterns as $pattern) {
        if (preg_match($pattern, $text, $matches)) {
            $yearText = strtolower($matches[1] ?? '');
            
            if ($yearText === 'first' || $yearText === '1') {
                error_log("Extracted Year Level: 1 (priority match)");
                return '1';
            }
            if ($yearText === 'second' || $yearText === '2') {
                error_log("Extracted Year Level: 2 (priority match)");
                return '2';
            }
            if ($yearText === 'third' || $yearText === '3') {
                error_log("Extracted Year Level: 3 (priority match)");
                return '3';
            }
            if ($yearText === 'fourth' || $yearText === '4') {
                error_log("Extracted Year Level: 4 (priority match)");
                return '4';
            }
        }
    }
    
    $fallbackPatterns = [
        '/([1-4])(?:st|nd|rd|th)\s*(?:year|yr)/i',
        '/(?:first|second|third|fourth)\s*year/i',
    ];
    
    foreach ($fallbackPatterns as $pattern) {
        if (preg_match($pattern, $text, $matches)) {
            $yearText = strtolower($matches[0] ?? '');
            
            if (strpos($yearText, 'semester') !== false || strpos($yearText, 'sem') !== false) {
                error_log("Skipping semester pattern: " . $yearText);
                continue;
            }
            
            if (strpos($yearText, 'first') !== false || strpos($yearText, '1st') !== false) {
                error_log("Extracted Year Level: 1");
                return '1';
            }
            if (strpos($yearText, 'second') !== false || strpos($yearText, '2nd') !== false) {
                error_log("Extracted Year Level: 2");
                return '2';
            }
            if (strpos($yearText, 'third') !== false || strpos($yearText, '3rd') !== false) {
                error_log("Extracted Year Level: 3");
                return '3';
            }
            if (strpos($yearText, 'fourth') !== false || strpos($yearText, '4th') !== false) {
                error_log("Extracted Year Level: 4");
                return '4';
            }
        }
    }
    
    error_log("Failed to extract Year Level from text");
    return '';
}

function extractGender($text) {
    $text = strtolower($text);
    
    if (preg_match('/\b(male|m\b)/i', $text) && !preg_match('/female/i', $text)) {
        return 'male';
    }
    
    if (preg_match('/\b(female|f\b)/i', $text)) {
        return 'female';
    }
    
    return '';
}

function extractAcademicYear($text) {
    $patterns = [
        '/a\.y\.\s*[:.\-\s,]*([0-9]{4}[\s\-]+[0-9]{4})/i',
        '/academic\s*year\s*[:.\-\s]*([0-9]{4}[\s\-]+[0-9]{4})/i',
        '/ay\s*[:.\-\s]*([0-9]{4}[\s\-]+[0-9]{4})/i',
        '/([0-9]{4}[\s\-]+[0-9]{4})/',
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $text, $matches)) {
            $year = trim($matches[1]);
            $year = preg_replace('/\s+/', '-', $year);
            
            if (preg_match('/^(19|20)[0-9]{2}-(19|20)[0-9]{2}$/', $year)) {
                error_log("Extracted Academic Year: " . $year);
                return $year;
            }
        }
    }
    
    error_log("Failed to extract Academic Year from text. Text sample: " . substr($text, 0, 500));
    return '';
}

function extractSemester($text) {
    $textOriginal = $text;
    $textLower = strtolower($text);
    
    $patterns = [
        '/first\s*\(?\s*1st\s*\)?\s*semester/i',
        '/first\s*\(?\s*1\s*st\s*\)?\s*semester/i',
        '/first\s*\(?\s*ist\s*\)?\s*semester/i',
        '/second\s*\(?\s*2nd\s*\)?\s*semester/i',
        '/third\s*\(?\s*3rd\s*\)?\s*semester/i',
        '/fourth\s*\(?\s*4th\s*\)?\s*semester/i',
        '/first\s*\(?1st\)?\s*semester/i',
        '/second\s*\(?2nd\)?\s*semester/i',
        '/third\s*\(?3rd\)?\s*semester/i',
        '/fourth\s*\(?4th\)?\s*semester/i',
        '/(first|second|third|fourth)\s*\(?\s*(1st|2nd|3rd|4th|ist|1\s*st)\s*\)?\s*semester/i',
        '/semester\s*[:.\-\s,]*([0-9]+(?:st|nd|rd|th)?)/i',
        '/(first|second|third|fourth|1st|2nd|3rd|4th|ist)\s*semester/i',
        '/semester\s*[:.\-\s]*(first|second|third|fourth|1st|2nd|3rd|4th|ist)/i',
        '/(first|second|third|fourth|1st|2nd|3rd|4th|ist)\s*sem/i',
        '/sem\s*[:.\-\s]*([0-9]+)/i',
        '/sem\s*[:.\-\s]*(first|second|third|fourth|1st|2nd|3rd|4th|ist)/i',
        '/([1-4])\s*(?:st|nd|rd|th)?\s*semester/i',
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $textOriginal, $matches)) {
            $fullMatch = $matches[0] ?? '';
            $semesterText = strtolower(trim($matches[1] ?? $matches[2] ?? $fullMatch));
            
            if (preg_match('/first|1st|^1$/', $semesterText) || preg_match('/first\s*\(?\s*1st\s*\)?\s*semester/i', $textOriginal)) {
                error_log("Extracted Semester: First (1st) Semester from pattern: " . $pattern);
                return 'First (1st) Semester';
            }
            if (preg_match('/second|2nd|^2$/', $semesterText) || preg_match('/second\s*\(?\s*2nd\s*\)?\s*semester/i', $textOriginal)) {
                error_log("Extracted Semester: Second (2nd) Semester from pattern: " . $pattern);
                return 'Second (2nd) Semester';
            }
            if (preg_match('/third|3rd|^3$/', $semesterText) || preg_match('/third\s*\(?\s*3rd\s*\)?\s*semester/i', $textOriginal)) {
                error_log("Extracted Semester: Third (3rd) Semester from pattern: " . $pattern);
                return 'Third (3rd) Semester';
            }
            if (preg_match('/fourth|4th|^4$/', $semesterText) || preg_match('/fourth\s*\(?\s*4th\s*\)?\s*semester/i', $textOriginal)) {
                error_log("Extracted Semester: Fourth (4th) Semester from pattern: " . $pattern);
                return 'Fourth (4th) Semester';
            }
        }
    }
    
    if (preg_match('/first/i', $textLower) && preg_match('/semester/i', $textLower)) {
        if (preg_match('/1st|1\s*st|ist/i', $textLower)) {
            error_log("Found 'first' and 'semester' and '1st/ist' - returning First (1st) Semester");
            return 'First (1st) Semester';
        }
        error_log("Found 'first' and 'semester' but no '1st' - checking if it's first semester anyway");
        if (preg_match('/first.*semester|semester.*first/i', $textLower)) {
            error_log("Found 'first semester' pattern - returning First (1st) Semester");
            return 'First (1st) Semester';
        }
    }
    if (preg_match('/second/i', $textLower) && preg_match('/semester/i', $textLower)) {
        if (preg_match('/2nd|2\s*nd/i', $textLower)) {
            error_log("Found 'second' and 'semester' and '2nd' - returning Second (2nd) Semester");
            return 'Second (2nd) Semester';
        }
    }
    if (preg_match('/third/i', $textLower) && preg_match('/semester/i', $textLower)) {
        if (preg_match('/3rd|3\s*rd/i', $textLower)) {
            error_log("Found 'third' and 'semester' and '3rd' - returning Third (3rd) Semester");
            return 'Third (3rd) Semester';
        }
    }
    if (preg_match('/fourth/i', $textLower) && preg_match('/semester/i', $textLower)) {
        if (preg_match('/4th|4\s*th/i', $textLower)) {
            error_log("Found 'fourth' and 'semester' and '4th' - returning Fourth (4th) Semester");
            return 'Fourth (4th) Semester';
        }
    }
    
    error_log("Failed to extract Semester from text. Text sample: " . substr($text, 0, 500));
    return '';
}
?>
