<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        ob_clean();
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Server error: ' . $error['message'] . ' in ' . basename($error['file']) . ' on line ' . $error['line']
        ]);
        ob_end_flush();
        exit;
    }
});

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    require_once __DIR__ . '/../includes/database.php';
    require_once __DIR__ . '/../includes/app_config.php';
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Configuration error: ' . $e->getMessage()
    ]);
    ob_end_flush();
    exit;
}

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['masterlistFile']) || $_FILES['masterlistFile']['error'] !== UPLOAD_ERR_OK) {
        $response['message'] = 'File upload failed';
        echo json_encode($response);
        exit;
    }
    
    try {
        $file = $_FILES['masterlistFile'];
        
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'image/heic', 'image/heif', 'image/webp'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            $response['message'] = 'Invalid file type. Allowed: PDF, JPG, PNG, HEIC, WEBP';
            echo json_encode($response);
            exit;
        }
        
        if ($file['size'] > 10 * 1024 * 1024) {
            $response['message'] = 'File size must be less than 10MB';
            echo json_encode($response);
            exit;
        }
        
        $uploadDir = '../uploads/temp/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $tempId = uniqid('masterlist_', true);
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filePath = $uploadDir . $tempId . '.' . $ext;
        
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            $response['message'] = 'Failed to save uploaded file';
            echo json_encode($response);
            exit;
        }
        
        try {
            $extractedStudents = extractMasterlistData($filePath);
        } catch (Exception $extractError) {
            unlink($filePath);
            throw $extractError;
        }
        
        unlink($filePath);
        
        $uploadedBy = isset($_SESSION['user']) ? $_SESSION['user'] : 'System';
        
        try {
            $savedCount = saveMasterlistToDatabase($extractedStudents, $uploadedBy);
            $response = [
                'status' => 'success',
                'message' => "Masterlist processed successfully. {$savedCount} student(s) saved to database.",
                'data' => $extractedStudents,
                'saved_count' => $savedCount
            ];
        } catch (PDOException $dbError) {
            error_log('Database save error (PDO): ' . $dbError->getMessage());
            $errorMsg = $dbError->getMessage();
            $response = [
                'status' => 'success',
                'message' => 'Masterlist processed successfully, but failed to save to database.',
                'data' => $extractedStudents,
                'saved_count' => 0,
                'warning' => strpos($errorMsg, "doesn't exist") !== false || strpos($errorMsg, "Table") !== false 
                    ? 'Masterlist table does not exist. Please run: sql/create_masterlist_table.sql'
                    : 'Database error: ' . $errorMsg
            ];
        } catch (Exception $dbError) {
            error_log('Database save error: ' . $dbError->getMessage());
            $response = [
                'status' => 'success',
                'message' => 'Masterlist processed successfully, but failed to save to database.',
                'data' => $extractedStudents,
                'saved_count' => 0,
                'warning' => $dbError->getMessage()
            ];
        }
        
    } catch (Exception $e) {
        error_log('Masterlist upload error: ' . $e->getMessage());
        $response = [
            'status' => 'error',
            'message' => 'Processing failed: ' . $e->getMessage()
        ];
    }
}

ob_clean();
$jsonResponse = json_encode($response);
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log('JSON encoding error: ' . json_last_error_msg());
    $jsonResponse = json_encode([
        'status' => 'error',
        'message' => 'Failed to encode response: ' . json_last_error_msg()
    ]);
}
echo $jsonResponse;
ob_end_flush();
exit;

function extractMasterlistData($filePath) {
    $apiKey = AppConfig::get('OCR_SPACE_API_KEY', '');
    
    if (empty($apiKey)) {
        throw new Exception('OCR service not configured');
    }
    
    $ocrText = performOCR($filePath, $apiKey);
    
    if (empty($ocrText)) {
        throw new Exception('Could not extract text from document. Please ensure the document is clear and readable.');
    }
    
    $students = parseMasterlistText($ocrText);
    
    return $students;
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
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
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
        'isOverlayRequired' => 'false',
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
    error_log('OCR extracted text length: ' . strlen($text));
    
    return $text;
}

function parseMasterlistText($text) {
    $students = [];
    $lines = preg_split('/\r?\n/', $text);
    
    $globalCourse = '';
    $globalSection = '';
    
    error_log('Total lines to parse: ' . count($lines));
    
    foreach ($lines as $lineIndex => $line) {
        $line = trim($line);
        if (empty($line)) {
            continue;
        }
        
        $courseFromLine = extractCourseFromLine($line);
        $sectionFromLine = extractSectionFromLine($line);
        
        if (!empty($courseFromLine)) {
            $globalCourse = $courseFromLine;
        }
        if (!empty($sectionFromLine)) {
            $globalSection = $sectionFromLine;
        }
        
        $student = extractStudentFromLine($line);
        
        if (!empty($student['studentId']) && !empty($student['name'])) {
            $studentId = normalizeStudentId($student['studentId']);
            $name = normalizeName($student['name']);
            
            if (strlen($studentId) >= 7 && strlen($name) >= 3) {
                $course = !empty($student['course']) ? $student['course'] : $globalCourse;
                $section = !empty($student['section']) ? $student['section'] : $globalSection;
                
                $students[] = [
                    'studentId' => $studentId,
                    'name' => $name,
                    'course' => $course,
                    'section' => $section
                ];
            }
        } elseif (!empty($student['studentId']) && empty($student['name'])) {
            $nextLine = isset($lines[$lineIndex + 1]) ? trim($lines[$lineIndex + 1]) : '';
            if (!empty($nextLine)) {
                $nextStudent = extractStudentFromLine($nextLine);
                if (!empty($nextStudent['name']) && empty($nextStudent['studentId'])) {
                    $studentId = normalizeStudentId($student['studentId']);
                    $name = normalizeName($nextStudent['name']);
                    
                    if (strlen($studentId) >= 7 && strlen($name) >= 3) {
                        $course = !empty($student['course']) ? $student['course'] : $globalCourse;
                        $section = !empty($student['section']) ? $student['section'] : $globalSection;
                        
                        $students[] = [
                            'studentId' => $studentId,
                            'name' => $name,
                            'course' => $course,
                            'section' => $section
                        ];
                    }
                }
            }
        } elseif (empty($student['studentId']) && !empty($student['name'])) {
            $prevLine = isset($lines[$lineIndex - 1]) ? trim($lines[$lineIndex - 1]) : '';
            if (!empty($prevLine)) {
                $prevStudent = extractStudentFromLine($prevLine);
                if (!empty($prevStudent['studentId']) && empty($prevStudent['name'])) {
                    $studentId = normalizeStudentId($prevStudent['studentId']);
                    $name = normalizeName($student['name']);
                    
                    if (strlen($studentId) >= 7 && strlen($name) >= 3) {
                        $course = !empty($prevStudent['course']) ? $prevStudent['course'] : $globalCourse;
                        $section = !empty($prevStudent['section']) ? $prevStudent['section'] : $globalSection;
                        
                        $students[] = [
                            'studentId' => $studentId,
                            'name' => $name,
                            'course' => $course,
                            'section' => $section
                        ];
                    }
                }
            }
        }
    }
    
    error_log('Students found after primary parsing: ' . count($students));
    
    $tableStudents = parseTableFormat($text, $globalCourse, $globalSection);
    error_log('Students found after table parsing: ' . count($tableStudents));
    if (count($tableStudents) > count($students)) {
        $students = $tableStudents;
    }
    
    if (count($students) < 5) {
        $alternativeStudents = tryAlternativeParsing($text);
        error_log('Students found after alternative parsing: ' . count($alternativeStudents));
        if (count($alternativeStudents) > count($students)) {
            $students = $alternativeStudents;
        }
    }
    
    $uniqueStudents = [];
    $seen = [];
    foreach ($students as $student) {
        if (empty($student['studentId']) || empty($student['name'])) {
            continue;
        }
        $key = $student['studentId'] . '|' . strtolower($student['name']);
        if (!isset($seen[$key])) {
            $seen[$key] = true;
            $uniqueStudents[] = $student;
        }
    }
    
    error_log('Final unique students count: ' . count($uniqueStudents));
    
    return $uniqueStudents;
}

function parseTableFormat($text, $globalCourse, $globalSection) {
    $students = [];
    $lines = preg_split('/\r?\n/', $text);
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) {
            continue;
        }
        
        if (preg_match('/^\d+\s+([A-Z][A-Za-zÀ-ÿ\s]+,\s*[A-Z][A-Za-zÀ-ÿ\s\.IVX]+)\s+([0-9]{4}[-\s]?[0-9]{4,5})/iu', $line, $matches)) {
            $name = trim($matches[1]);
            $studentId = normalizeStudentId($matches[2]);
            
            if (preg_match('/^\d{4}-?\d{4,5}$/', $studentId)) {
                $name = convertLastnameFirstnameFormat($name);
                $name = normalizeName($name);
                
                if (strlen($name) >= 5) {
                    $students[] = [
                        'studentId' => $studentId,
                        'name' => $name,
                        'course' => $globalCourse,
                        'section' => $globalSection
                    ];
                }
            }
        } elseif (preg_match('/^\d+\s+([A-Z][A-Za-zÀ-ÿ\s,\.IVX]+)\s+([0-9]{4}[-\s]?[0-9]{4,5})/iu', $line, $matches)) {
            $name = trim($matches[1]);
            $studentId = normalizeStudentId($matches[2]);
            
            if (preg_match('/^\d{4}-?\d{4,5}$/', $studentId)) {
                if (strpos($name, ',') !== false) {
                    $name = convertLastnameFirstnameFormat($name);
                }
                $name = normalizeName($name);
                
                if (strlen($name) >= 5) {
                    $students[] = [
                        'studentId' => $studentId,
                        'name' => $name,
                        'course' => $globalCourse,
                        'section' => $globalSection
                    ];
                }
            }
        } elseif (preg_match('/([A-Z][A-Za-zÀ-ÿ\s]+,\s*[A-Z][A-Za-zÀ-ÿ\s\.IVX]+)\s+([0-9]{4}[-\s]?[0-9]{4,5})/iu', $line, $matches)) {
            $name = trim($matches[1]);
            $studentId = normalizeStudentId($matches[2]);
            
            if (preg_match('/^\d{4}-?\d{4,5}$/', $studentId)) {
                $name = convertLastnameFirstnameFormat($name);
                $name = normalizeName($name);
                
                if (strlen($name) >= 5) {
                    $students[] = [
                        'studentId' => $studentId,
                        'name' => $name,
                        'course' => $globalCourse,
                        'section' => $globalSection
                    ];
                }
            }
        } elseif (preg_match('/([0-9]{4}[-\s]?[0-9]{4,5})\s+([A-Z][A-Za-zÀ-ÿ\s]+,\s*[A-Z][A-Za-zÀ-ÿ\s\.IVX]+)/iu', $line, $matches)) {
            $studentId = normalizeStudentId($matches[1]);
            $name = trim($matches[2]);
            
            if (preg_match('/^\d{4}-?\d{4,5}$/', $studentId)) {
                $name = convertLastnameFirstnameFormat($name);
                $name = normalizeName($name);
                
                if (strlen($name) >= 5) {
                    $students[] = [
                        'studentId' => $studentId,
                        'name' => $name,
                        'course' => $globalCourse,
                        'section' => $globalSection
                    ];
                }
            }
        }
    }
    
    return $students;
}

function convertLastnameFirstnameFormat($name) {
    if (preg_match('/^([A-Z][A-Za-zÀ-ÿ\sIVX]+),\s*([A-Z][A-Za-zÀ-ÿ\s\.IVX]+)$/u', $name, $matches)) {
        $lastname = trim($matches[1]);
        $firstname = trim($matches[2]);
        $firstname = preg_replace('/\.$/', '', $firstname);
        return trim($firstname . ' ' . $lastname);
    }
    return $name;
}

function extractStudentFromLine($line) {
    $student = ['studentId' => '', 'name' => '', 'course' => '', 'section' => ''];
    
    $studentIdPatterns = [
        '/\b([0-9]{4}[-\s]?[0-9]{4,5})\b/',
        '/student\s*(?:id|no\.?|number|num)[\s:.\-]*([0-9]{4}[-\s.]?[0-9]{4,5})/i',
        '/(?:^|\s)([0-9]{4}[-\s]?[0-9]{4,5})(?:\s|$)/',
        '/\b([0-9]{4}[-\s\.]?[0-9]{4,5})\b/',
        '/([0-9]{4}[-\s]?[0-9]{4,5})/',
    ];
    
    $namePatterns = [
        '/\b([A-Z][A-Za-zÀ-ÿ\s]+,\s*[A-Z][A-Za-zÀ-ÿ\s\.IVX]+)\b/u',
        '/\b([A-Z][a-zÀ-ÿ]+(?:\s+[A-Z][a-zÀ-ÿIVX]+)+)\b/u',
        '/\b([A-Z][a-zÀ-ÿ]+\s+[A-Z][a-zÀ-ÿIVX]+(?:\s+[A-Z][a-zÀ-ÿIVX]+)?)\b/u',
        '/([A-Z][a-zÀ-ÿ]+(?:\s+[A-Z][a-zÀ-ÿIVX]+)+)/u',
        '/\b([A-Z][A-Z\sÀ-ÿIVX]+[A-Z])\b/u',
        '/([A-Z][A-Z\sÀ-ÿIVX]{3,}[A-Z])/u',
        '/\b([A-Z][a-zA-ZÀ-ÿ]+\s+[A-Z][a-zA-ZÀ-ÿIVX]+(?:\s+[A-Z][a-zA-ZÀ-ÿIVX]+)?)\b/u',
    ];
    
    foreach ($studentIdPatterns as $pattern) {
        if (preg_match($pattern, $line, $matches)) {
            $id = trim($matches[1]);
            $id = preg_replace('/[\s._]+/', '-', $id);
            $id = preg_replace('/[^0-9-]/', '', $id);
            
            if (preg_match('/^\d{4}-?\d{4,5}$/', $id)) {
                $student['studentId'] = $id;
                break;
            }
        }
    }
    
    foreach ($namePatterns as $pattern) {
        if (preg_match($pattern, $line, $matches)) {
            $name = trim($matches[1]);
            
            if (strpos($name, ',') !== false) {
                $name = convertLastnameFirstnameFormat($name);
            }
            
            $nameParts = preg_split('/\s+/', $name);
            
            if (count($nameParts) >= 2 && strlen($name) >= 5) {
                $student['name'] = $name;
                break;
            }
        }
    }
    
    if (empty($student['studentId']) && empty($student['name'])) {
        $parts = preg_split('/\s{2,}|\t|(?<=\d)\s+(?=[A-Z])/', $line);
        if (count($parts) >= 2) {
            foreach ($parts as $part) {
                $part = trim($part);
                if (empty($part)) continue;
                
                $normalizedPart = preg_replace('/[\s._]+/', '-', $part);
                $normalizedPart = preg_replace('/[^0-9-]/', '', $normalizedPart);
                
                if (preg_match('/^\d{4}-?\d{4,5}$/', $normalizedPart)) {
                    $student['studentId'] = $normalizedPart;
                } elseif (preg_match('/^[A-Z][a-zA-Z\s]{3,}$/', $part) && strlen($part) >= 5) {
                    $nameParts = preg_split('/\s+/', $part);
                    if (count($nameParts) >= 2) {
                        $student['name'] = $part;
                    }
                }
            }
        }
        
        if (empty($student['studentId']) && empty($student['name'])) {
            if (preg_match('/([0-9]{4}[-\s]?[0-9]{4,5})\s+([A-Z][A-Za-z\s]{3,})/', $line, $matches)) {
                $student['studentId'] = preg_replace('/[\s._]+/', '-', preg_replace('/[^0-9-]/', '', $matches[1]));
                $student['name'] = trim($matches[2]);
            } elseif (preg_match('/([A-Z][A-Za-z\s]{3,})\s+([0-9]{4}[-\s]?[0-9]{4,5})/', $line, $matches)) {
                $student['name'] = trim($matches[1]);
                $student['studentId'] = preg_replace('/[\s._]+/', '-', preg_replace('/[^0-9-]/', '', $matches[2]));
            }
        }
    }
    
    $course = extractCourseFromLine($line);
    $section = extractSectionFromLine($line);
    
    if (!empty($course)) {
        $student['course'] = $course;
    }
    if (!empty($section)) {
        $student['section'] = $section;
    }
    
    return $student;
}

function extractCourseFromLine($line) {
    $lineLower = strtolower($line);
    
    if (preg_match('/\b(bsit|bs\s*it|bachelor.*information\s*technology)\b/i', $line)) {
        return 'BSIT';
    }
    
    if (preg_match('/\b(bscs|bs\s*cs|bachelor.*computer\s*science)\b/i', $line)) {
        return 'BSCS';
    }
    
    if (preg_match('/\b(bsis|bs\s*is|bachelor.*information\s*systems)\b/i', $line)) {
        return 'BSIS';
    }
    
    return '';
}

function extractSectionFromLine($line) {
    if (preg_match('/\bsection\s*[:.\-\s]*([A-Z])\b/i', $line, $matches)) {
        return strtoupper($matches[1]);
    }
    
    if (preg_match('/\b([A-Z])\s*(?:section|sec)\b/i', $line, $matches)) {
        return strtoupper($matches[1]);
    }
    
    if (preg_match('/\b(?:section|sec)\s*([A-Z])\b/i', $line, $matches)) {
        return strtoupper($matches[1]);
    }
    
    return '';
}

function tryAlternativeParsing($text) {
    $students = [];
    $lines = preg_split('/\r?\n/', $text);
    
    $globalCourse = '';
    $globalSection = '';
    
    foreach ($lines as $lineIndex => $line) {
        $line = trim($line);
        if (empty($line)) {
            continue;
        }
        
        $courseFromLine = extractCourseFromLine($line);
        $sectionFromLine = extractSectionFromLine($line);
        
        if (!empty($courseFromLine)) {
            $globalCourse = $courseFromLine;
        }
        if (!empty($sectionFromLine)) {
            $globalSection = $sectionFromLine;
        }
        
        $allIds = [];
        preg_match_all('/\b([0-9]{4}[-\s]?[0-9]{4,5})\b/', $line, $idMatches);
        if (!empty($idMatches[1])) {
            foreach ($idMatches[1] as $idMatch) {
                $normalizedId = normalizeStudentId($idMatch);
                if (preg_match('/^\d{4}-?\d{4,5}$/', $normalizedId)) {
                    $allIds[] = $normalizedId;
                }
            }
        }
        
        $allNames = [];
        preg_match_all('/\b([A-Z][A-Za-zÀ-ÿ\s]+,\s*[A-Z][A-Za-zÀ-ÿ\s\.IVX]+)\b/u', $line, $commaNameMatches);
        if (!empty($commaNameMatches[1])) {
            foreach ($commaNameMatches[1] as $nameMatch) {
                $convertedName = convertLastnameFirstnameFormat($nameMatch);
                $nameParts = preg_split('/\s+/', trim($convertedName));
                if (count($nameParts) >= 2 && strlen($convertedName) >= 5) {
                    $allNames[] = normalizeName($convertedName);
                }
            }
        }
        preg_match_all('/\b([A-Z][A-Za-zÀ-ÿ]+(?:\s+[A-Z][A-Za-zÀ-ÿIVX]+)+)\b/u', $line, $nameMatches);
        if (!empty($nameMatches[1])) {
            foreach ($nameMatches[1] as $nameMatch) {
                $nameParts = preg_split('/\s+/', trim($nameMatch));
                if (count($nameParts) >= 2 && strlen($nameMatch) >= 5) {
                    $allNames[] = normalizeName($nameMatch);
                }
            }
        }
        
        if (!empty($allIds) && !empty($allNames)) {
            foreach ($allIds as $studentId) {
                foreach ($allNames as $name) {
                    if (strlen($studentId) >= 7 && strlen($name) >= 3) {
                        $course = extractCourseFromLine($line);
                        $section = extractSectionFromLine($line);
                        
                        if (empty($course)) {
                            $course = $globalCourse;
                        }
                        if (empty($section)) {
                            $section = $globalSection;
                        }
                        
                        $students[] = [
                            'studentId' => $studentId,
                            'name' => $name,
                            'course' => $course,
                            'section' => $section
                        ];
                    }
                }
            }
        } elseif (!empty($allIds) && empty($allNames)) {
            $nextLine = isset($lines[$lineIndex + 1]) ? trim($lines[$lineIndex + 1]) : '';
            if (!empty($nextLine)) {
                $nextNameMatches = [];
                preg_match_all('/\b([A-Z][A-Za-zÀ-ÿ\s]+,\s*[A-Z][A-Za-zÀ-ÿ\s\.IVX]+)\b/u', $nextLine, $commaMatches);
                if (!empty($commaMatches[1])) {
                    foreach ($commaMatches[1] as $nameMatch) {
                        $convertedName = convertLastnameFirstnameFormat($nameMatch);
                        $nameParts = preg_split('/\s+/', trim($convertedName));
                        if (count($nameParts) >= 2 && strlen($convertedName) >= 5) {
                            $nextNameMatches[] = $convertedName;
                        }
                    }
                }
                preg_match_all('/\b([A-Z][A-Za-zÀ-ÿ]+(?:\s+[A-Z][A-Za-zÀ-ÿIVX]+)+)\b/u', $nextLine, $regularMatches);
                if (!empty($regularMatches[1])) {
                    foreach ($regularMatches[1] as $nameMatch) {
                        $nameParts = preg_split('/\s+/', trim($nameMatch));
                        if (count($nameParts) >= 2 && strlen($nameMatch) >= 5) {
                            $nextNameMatches[] = $nameMatch;
                        }
                    }
                }
                
                if (!empty($nextNameMatches)) {
                    foreach ($nextNameMatches as $nameMatch) {
                        foreach ($allIds as $studentId) {
                            if (strlen($studentId) >= 7) {
                                $course = extractCourseFromLine($line);
                                $section = extractSectionFromLine($line);
                                
                                if (empty($course)) {
                                    $course = $globalCourse;
                                }
                                if (empty($section)) {
                                    $section = $globalSection;
                                }
                                
                                $students[] = [
                                    'studentId' => $studentId,
                                    'name' => normalizeName($nameMatch),
                                    'course' => $course,
                                    'section' => $section
                                ];
                            }
                        }
                    }
                }
            }
        }
    }
    
    return $students;
}

function normalizeStudentId($id) {
    $id = preg_replace('/[\s._]+/', '-', $id);
    $id = preg_replace('/[^0-9-]/', '', $id);
    
    if (preg_match('/^\d{8,9}$/', $id)) {
        $id = substr($id, 0, 4) . '-' . substr($id, 4);
    }
    
    return $id;
}

function normalizeName($name) {
    $name = trim($name);
    $name = preg_replace('/\s+/', ' ', $name);
    
    $parts = preg_split('/\s+/', $name);
    $normalized = [];
    
    foreach ($parts as $part) {
        $part = trim($part);
        if (empty($part)) continue;
        
        if (preg_match('/^[IVX]+$/i', $part)) {
            $normalized[] = strtoupper($part);
        } elseif (preg_match('/^[A-Z]\.$/', $part)) {
            $normalized[] = strtoupper($part);
        } else {
            $normalized[] = ucwords(strtolower($part));
        }
    }
    
    return implode(' ', $normalized);
}

function saveMasterlistToDatabase($students, $uploadedBy) {
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        $savedCount = 0;
        $conn->beginTransaction();
        
        foreach ($students as $student) {
            if (empty($student['studentId']) || empty($student['name'])) {
                continue;
            }
            
            $studentId = normalizeStudentId($student['studentId']);
            $name = normalizeName($student['name']);
            $course = $student['course'] ?? '';
            $section = $student['section'] ?? '';
            
            $stmt = $conn->prepare("
                INSERT INTO masterlist (student_id, name, course, section, uploaded_by)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    name = VALUES(name),
                    course = VALUES(course),
                    section = VALUES(section),
                    uploaded_at = CURRENT_TIMESTAMP,
                    uploaded_by = VALUES(uploaded_by)
            ");
            
            $stmt->execute([$studentId, $name, $course, $section, $uploadedBy]);
            $savedCount++;
        }
        
        $conn->commit();
        return $savedCount;
    } catch (PDOException $e) {
        if (isset($conn) && $conn->inTransaction()) {
            try {
                $conn->rollBack();
            } catch (Exception $rollbackError) {
                error_log('Rollback error: ' . $rollbackError->getMessage());
            }
        }
        error_log('Error saving masterlist to database: ' . $e->getMessage());
        
        $errorMsg = $e->getMessage();
        if (stripos($errorMsg, "doesn't exist") !== false || 
            stripos($errorMsg, "Table") !== false ||
            stripos($errorMsg, "Unknown table") !== false) {
            throw new Exception('Masterlist table does not exist. Please run: sql/create_masterlist_table.sql');
        }
        
        throw new Exception('Database error: ' . $errorMsg);
    } catch (Exception $e) {
        if (isset($conn) && $conn->inTransaction()) {
            try {
                $conn->rollBack();
            } catch (Exception $rollbackError) {
                error_log('Rollback error: ' . $rollbackError->getMessage());
            }
        }
        error_log('Error saving masterlist to database: ' . $e->getMessage());
        throw $e;
    }
}

function getExistingStudentIds() {
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("SELECT id FROM students");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        return $result ?: [];
    } catch (Exception $e) {
        error_log('Error fetching existing student IDs: ' . $e->getMessage());
        return [];
    }
}


