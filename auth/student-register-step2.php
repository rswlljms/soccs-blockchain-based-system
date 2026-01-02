<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/database.php';
require_once '../includes/app_config.php';
require_once '../includes/email_config.php';
require_once '../includes/document_verification_service.php';

define('EXTRACT_STUDENT_INFO_INCLUDED', true);
require_once '../api/extract-student-info.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName'] ?? '');
    $middleName = trim($_POST['middleName'] ?? '') ?: null;
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $studentId = trim($_POST['studentId'] ?? '');
    $course = $_POST['course'] ?? 'BSIT';
    $yearLevel = $_POST['yearLevel'] ?? '';
    $section = strtoupper(trim($_POST['section'] ?? ''));
    $gender = $_POST['gender'] ?? '';
    
    $tempId = $_POST['tempId'] ?? '';
    $studentIdPath = $_POST['studentIdPath'] ?? '';
    $corPath = $_POST['corPath'] ?? '';
    
    if (empty($firstName) || empty($lastName) || empty($email) || 
        empty($studentId) || empty($yearLevel) || empty($section) || empty($gender)) {
        $response['message'] = 'All required fields must be filled';
        echo json_encode($response);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email address';
        echo json_encode($response);
        exit;
    }
    
    // If session data is missing or files don't exist, try to find the most recent temp files
    if (empty($tempId) || empty($studentIdPath) || empty($corPath) || 
        !file_exists($studentIdPath) || !file_exists($corPath)) {
        
        error_log("Session data missing or files not found. Attempting to recover from temp folder.");
        
        // Try to find temp files in the uploads/temp directory
        $tempDir = '../uploads/temp/';
        $foundFiles = false;
        
        if (is_dir($tempDir)) {
            $files = scandir($tempDir);
            $tempFiles = [];
            
            // Look for temp files (exclude . and ..)
            foreach ($files as $file) {
                if ($file === '.' || $file === '..' || $file === '.gitkeep') continue;
                if (strpos($file, 'temp_') === 0) {
                    $tempFiles[$file] = filemtime($tempDir . $file);
                }
            }
            
            // Sort by modification time (newest first)
            arsort($tempFiles);
            
            // Find the most recent studentid and cor files
            $studentIdFile = null;
            $corFileFound = null;
            
            foreach (array_keys($tempFiles) as $file) {
                if (strpos($file, '_studentid.') !== false && !$studentIdFile) {
                    $studentIdFile = $file;
                }
                if (strpos($file, '_cor.') !== false && !$corFileFound) {
                    $corFileFound = $file;
                }
                if ($studentIdFile && $corFileFound) break;
            }
            
            if ($studentIdFile && $corFileFound) {
                $studentIdPath = $tempDir . $studentIdFile;
                $corPath = $tempDir . $corFileFound;
                
                // Extract temp ID from filename
                preg_match('/temp_([^_]+)/', $studentIdFile, $matches);
                $tempId = $matches[1] ?? uniqid();
                
                $foundFiles = true;
                error_log("Recovered temp files - studentId: $studentIdFile, cor: $corFileFound");
            }
        }
        
        if (!$foundFiles) {
            $response['message'] = 'Session expired. Please start from step 1 and upload your documents again.';
            error_log("Could not recover temp files from uploads/temp/");
            echo json_encode($response);
            exit;
        }
    }
    
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $checkStudentsQuery = "SELECT id FROM students WHERE id = ? OR email = ?";
        $checkStudentsStmt = $conn->prepare($checkStudentsQuery);
        $checkStudentsStmt->execute([$studentId, $email]);
        
        if ($checkStudentsStmt->fetch()) {
            $response['message'] = 'Student ID or email already exists in the system';
            echo json_encode($response);
            exit;
        }
        
        $checkRegQuery = "SELECT id, approval_status FROM student_registrations WHERE id = ? OR email = ?";
        $checkRegStmt = $conn->prepare($checkRegQuery);
        $checkRegStmt->execute([$studentId, $email]);
        $existingReg = $checkRegStmt->fetch();
        
        if ($existingReg) {
            if ($existingReg['approval_status'] === 'pending') {
                $response['message'] = 'Registration is already pending. Please wait for approval.';
                echo json_encode($response);
                exit;
            } elseif ($existingReg['approval_status'] === 'approved') {
                $response['message'] = 'Registration already approved. Please check your email for login details.';
                echo json_encode($response);
                exit;
            }
        }
        
        $token = bin2hex(random_bytes(32));
        $tokenExpiresAt = (new DateTime('+24 hours'))->format('Y-m-d H:i:s');
        
        $uploadDir = '../uploads/student-ids/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $studentIdExt = pathinfo($studentIdPath, PATHINFO_EXTENSION);
        $finalStudentIdName = $studentId . '.' . $studentIdExt;
        $finalStudentIdPath = $uploadDir . $finalStudentIdName;
        
        if (!copy($studentIdPath, $finalStudentIdPath)) {
            $response['message'] = 'Failed to process student ID image';
            echo json_encode($response);
            exit;
        }
        
        $corUploadDir = '../uploads/documents/';
        if (!is_dir($corUploadDir)) {
            mkdir($corUploadDir, 0755, true);
        }
        
        $corExt = pathinfo($corPath, PATHINFO_EXTENSION);
        $finalCorName = uniqid() . '_COR_' . $studentId . '.' . $corExt;
        $finalCorPath = $corUploadDir . $finalCorName;
        
        if (!copy($corPath, $finalCorPath)) {
            unlink($finalStudentIdPath);
            $response['message'] = 'Failed to process COR file';
            echo json_encode($response);
            exit;
        }
        
        $academicYear = trim($_POST['academicYear'] ?? '');
        $semester = trim($_POST['semester'] ?? '');
        
        if (empty($academicYear)) $academicYear = null;
        if (empty($semester)) $semester = null;
        
        error_log("=== INITIAL VALUES FROM FORM ===");
        error_log("Academic Year: " . var_export($academicYear, true));
        error_log("Semester: " . var_export($semester, true));
        
        if (empty($academicYear) || empty($semester)) {
            error_log("=== EXTRACTING FROM COR FILE (fallback) ===");
            error_log("COR Path: " . $finalCorPath);
            error_log("COR File exists: " . (file_exists($finalCorPath) ? 'YES' : 'NO'));
            
            if (file_exists($finalCorPath)) {
                $extractedInfo = extractInformationFromCOR($finalCorPath);
                
                error_log("Extraction success: " . ($extractedInfo['success'] ? 'YES' : 'NO'));
                
                if ($extractedInfo['success'] && isset($extractedInfo['data'])) {
                    if (empty($academicYear) && !empty($extractedInfo['data']['academicYear'])) {
                        $academicYear = trim($extractedInfo['data']['academicYear']);
                        error_log("Extracted Academic Year: " . $academicYear);
                    }
                    if (empty($semester) && !empty($extractedInfo['data']['semester'])) {
                        $semester = trim($extractedInfo['data']['semester']);
                        error_log("Extracted Semester: " . $semester);
                    }
                } else {
                    error_log("Extraction failed or no data: " . ($extractedInfo['message'] ?? 'Unknown'));
                }
            } else {
                error_log("COR file not found at: " . $finalCorPath);
            }
        } else {
            error_log("=== USING EXTRACTED DATA FROM STEP 1 ===");
            error_log("Academic Year from form: " . $academicYear);
            error_log("Semester from form: " . $semester);
        }
        
        unlink($studentIdPath);
        unlink($corPath);
        
        error_log("=== FINAL VALUES TO BE SAVED ===");
        error_log("Academic Year: " . var_export($academicYear, true));
        error_log("Semester: " . var_export($semester, true));
        
        if (empty($academicYear) && empty($semester)) {
            error_log("WARNING: Both academic year and semester are empty!");
        }
        
        $isReRegistration = ($existingReg && $existingReg['approval_status'] === 'rejected');
        
        if ($isReRegistration) {
            $updateQuery = "UPDATE student_registrations SET 
                           first_name=?, middle_name=?, last_name=?, email=?, course=?, year_level=?, section=?, gender=?, 
                           student_id_image=?, cor_file=?, set_password_token=?, set_password_expires_at=?, 
                           academic_year=?, semester=?,
                           approval_status='pending',
                           rejected_at=NULL, rejection_reason=NULL, approved_at=NULL, approved_by=NULL
                           WHERE id=?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->execute([
                $firstName, $middleName, $lastName, $email, $course, $yearLevel, $section, $gender,
                'uploads/student-ids/' . $finalStudentIdName,
                'uploads/documents/' . $finalCorName,
                $token, $tokenExpiresAt, $academicYear, $semester, $studentId
            ]);
        } else {
            $insertQuery = "INSERT INTO student_registrations
                           (id, first_name, middle_name, last_name, email, course, year_level, section, gender, student_id_image, cor_file, set_password_token, set_password_expires_at, academic_year, semester, approval_status)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->execute([
                $studentId, $firstName, $middleName, $lastName, $email, $course, $yearLevel, $section, $gender,
                'uploads/student-ids/' . $finalStudentIdName,
                'uploads/documents/' . $finalCorName,
                $token, $tokenExpiresAt, $academicYear, $semester
            ]);
        }

        $verifier = new DocumentVerificationService();
        
        $startTime = time();
        $maxTime = 25;
        $verificationPassed = false;
        $verificationResult = null;
        $isTechnicalFailure = false;
        
        try {
            $verificationResult = $verifier->runVerification($studentId);
            
            if ((time() - $startTime) > $maxTime) {
                error_log("Verification timeout for student {$studentId} after {$maxTime}s");
                $isTechnicalFailure = true;
            } else {
                $verificationPassed = ($verificationResult['overall_result'] === 'valid');
                
                if (!$verificationPassed) {
                    $result = $verificationResult['overall_result'] ?? '';
                    $reason = $verificationResult['reason'] ?? '';
                    
                    if ($result === 'mismatch' && (
                        strpos(strtolower($reason), 'could not be read') !== false ||
                        strpos(strtolower($reason), 'unclear') !== false ||
                        strpos(strtolower($reason), 'insufficient readable text') !== false ||
                        strpos(strtolower($reason), 'ocr failed') !== false
                    )) {
                        error_log("Verification failed due to OCR issues (technical), but documents were validated in step 1");
                        $isTechnicalFailure = true;
                    }
                }
            }
            
        } catch (Exception $e) {
            error_log("Verification exception for student {$studentId}: " . $e->getMessage());
            $isTechnicalFailure = true;
            $verificationResult = [
                'overall_result' => 'error',
                'reason' => 'Technical error during verification: ' . $e->getMessage()
            ];
        }
        
        if ($verificationPassed || $isTechnicalFailure) {
            $insertStudentQuery = "INSERT INTO students 
                                  (id, first_name, middle_name, last_name, email, password, course, year_level, section, gender, academic_year, semester, is_active) 
                                  VALUES (?, ?, ?, ?, ?, '', ?, ?, ?, ?, ?, ?, 1)";
            $insertStudentStmt = $conn->prepare($insertStudentQuery);
            $result = $insertStudentStmt->execute([
                $studentId,
                $firstName,
                $middleName,
                $lastName,
                $email,
                $course,
                $yearLevel,
                $section,
                $gender,
                $academicYear,
                $semester
            ]);
            
            error_log("=== AFTER INSERT ===");
            error_log("Insert successful: " . ($result ? 'YES' : 'NO'));
            error_log("Values inserted - Academic Year: " . var_export($academicYear, true));
            error_log("Values inserted - Semester: " . var_export($semester, true));
            
            $checkQuery = "SELECT academic_year, semester FROM students WHERE id = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->execute([$studentId]);
            $saved = $checkStmt->fetch(PDO::FETCH_ASSOC);
            error_log("Saved in DB - Academic Year: " . var_export($saved['academic_year'] ?? 'NULL', true));
            error_log("Saved in DB - Semester: " . var_export($saved['semester'] ?? 'NULL', true));

            $approveQuery = "UPDATE student_registrations SET approval_status='approved', approved_at=NOW(), approved_by='System' WHERE id=?";
            $approveStmt = $conn->prepare($approveQuery);
            $approveStmt->execute([$studentId]);

            $emailService = new EmailService();
            $emailService->sendApprovalWithPasswordSetup($email, $firstName, $studentId, $token);
            
            $message = $isReRegistration ? 
                'Re-registration approved! Check your email to set your password.' : 
                'Registration approved! Check your email to set your password.';
            
            $response = [
                'status' => 'success',
                'message' => $message,
                'student_id' => $studentId
            ];
        } else {
            $rejectionReason = $verificationResult['reason'] ?? 'Document verification failed. Please ensure your documents are clear and match your information.';
            $rejectQuery = "UPDATE student_registrations SET approval_status='rejected', rejected_at=NOW(), rejection_reason=? WHERE id=?";
            $rejectStmt = $conn->prepare($rejectQuery);
            $rejectStmt->execute([$rejectionReason, $studentId]);

            $emailService = new EmailService();
            $emailService->sendRejectionNotification($email, $firstName, $studentId, $rejectionReason);
            
            $rejectionMessage = $isReRegistration ? 
                'Re-registration rejected: ' . $rejectionReason . ' You can try registering again with correct documents.' :
                'Registration rejected: ' . $rejectionReason . ' You can try registering again with correct documents.';
            
            $response = [
                'status' => 'error',
                'message' => $rejectionMessage,
                'student_id' => $studentId
            ];
        }
        
    } catch (Exception $e) {
        $response['message'] = 'Registration failed: ' . $e->getMessage();
        error_log('Student registration error: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
    }
}

echo json_encode($response);
?>
