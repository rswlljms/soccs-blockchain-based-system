<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    require_once '../includes/database.php';
    require_once '../includes/app_config.php';
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
}

try {
    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'JSON encoding failed: ' . $e->getMessage()]);
}

function extractInformationFromCOR($filePath) {
    $apiKey = AppConfig::get('OCR_SPACE_API_KEY', '');
    
    if (empty($apiKey)) {
        return [
            'success' => false,
            'message' => 'OCR service not configured'
        ];
    }
    
    $ocrText = performOCR($filePath, $apiKey);
    
    if (empty($ocrText)) {
        return [
            'success' => false,
            'message' => 'Could not read text from COR. Please upload a clearer image.'
        ];
    }
    
    $studentId = extractStudentId($ocrText);
    $course = extractCourse($ocrText);
    $yearLevel = extractYearLevel($ocrText);
    $gender = extractGender($ocrText);
    
    error_log("=== EXTRACTION RESULTS ===");
    error_log("Student ID: " . ($studentId ?: 'NOT FOUND'));
    error_log("Course: " . ($course ?: 'NOT FOUND'));
    error_log("Year Level: " . ($yearLevel ?: 'NOT FOUND'));
    error_log("Gender: " . ($gender ?: 'NOT FOUND'));
    error_log("OCR Text Sample: " . substr($ocrText, 0, 200));
    
    return [
        'success' => true,
        'data' => [
            'studentId' => $studentId,
            'course' => $course,
            'yearLevel' => $yearLevel,
            'gender' => $gender,
            'ocrText' => substr($ocrText, 0, 500),
            'debug' => [
                'foundStudentId' => !empty($studentId),
                'foundCourse' => !empty($course),
                'foundYearLevel' => !empty($yearLevel),
                'foundGender' => !empty($gender)
            ]
        ]
    ];
}

function performOCR($filePath, $apiKey) {
    $endpoint = 'https://api.ocr.space/parse/image';
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $isPdf = ($ext === 'pdf');
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    
    $fileField = curl_file_create(
        $filePath,
        $isPdf ? 'application/pdf' : (mime_content_type($filePath) ?: 'image/jpeg'),
        basename($filePath)
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
?>
