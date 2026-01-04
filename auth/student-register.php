<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/database.php';
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
    $studentId = $_POST['studentId'] ?? '';
    $course = $_POST['course'] ?? 'BSIT';
    $yearLevel = $_POST['yearLevel'] ?? '';
    $section = $_POST['section'] ?? '';
    $age = $_POST['age'] ?? '';
    $gender = $_POST['gender'] ?? '';
    
    if (empty($firstName) || empty($lastName) || empty($email) || 
        empty($studentId) || empty($yearLevel) || empty($section) || empty($age) || empty($gender)) {
        $response['message'] = 'All fields are required';
        echo json_encode($response);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email address';
        echo json_encode($response);
        exit;
    }
    
    // password will be set later via email link
    
    // Accept single documentFile upload OR legacy dual fields (for backward compatibility)
    $hasDocument = isset($_FILES['documentFile']) && $_FILES['documentFile']['error'] === UPLOAD_ERR_OK;
    $hasStudentId = isset($_FILES['studentIdImage']) && $_FILES['studentIdImage']['error'] === UPLOAD_ERR_OK;
    $hasCor = isset($_FILES['corFile']) && $_FILES['corFile']['error'] === UPLOAD_ERR_OK;
    
    if (!$hasDocument && !$hasStudentId && !$hasCor) {
        $response['message'] = 'Please upload either Student ID image or Certificate of Registration (COR).';
        echo json_encode($response);
        exit;
    }
    
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Check for existing approved students (cannot re-register)
        $checkStudentsQuery = "SELECT id FROM students WHERE id = ? OR email = ?";
        $checkStudentsStmt = $conn->prepare($checkStudentsQuery);
        $checkStudentsStmt->execute([$studentId, $email]);
        
        if ($checkStudentsStmt->fetch()) {
            $response['message'] = 'Student ID or email already exists in the system';
            echo json_encode($response);
            exit;
        }
        
        // Check for existing registrations - allow re-registration if rejected
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
            // If rejected, allow re-registration by updating the existing record
        }
        
        // prepare set-password token
        $token = bin2hex(random_bytes(32));
        $tokenExpiresAt = (new DateTime('+24 hours'))->format('Y-m-d H:i:s');
        
        $allowedImageTypes = ['image/jpeg','image/jpg','image/png','image/heic','image/heif','image/webp','image/tiff','image/bmp','image/gif'];
        $allowedCorTypes = array_merge($allowedImageTypes, ['application/pdf']);
        
        $studentIdFileName = null;
        $corFileName = null;
        $fileToExtract = null;
        $studentIdUploadDir = '../uploads/student-ids/';
        $corUploadDir = '../uploads/documents/';
        
        // Handle single documentFile upload (new approach)
        if ($hasDocument) {
            $documentFile = $_FILES['documentFile'];
            $fileExtension = strtolower(pathinfo($documentFile['name'], PATHINFO_EXTENSION));
            $isPdf = $fileExtension === 'pdf';
            $fileSize = $documentFile['size'];
            
            // Validate file size (max 1MB)
            if ($fileSize > 1 * 1024 * 1024) {
                $response['message'] = 'File size must be less than 1MB. Please upload a smaller file.';
                echo json_encode($response);
                exit;
            }
            
            // Determine if it's Student ID or COR based on extension
            if ($isPdf) {
                // PDF is always COR
                if (!in_array($documentFile['type'], $allowedCorTypes)) {
                    $response['message'] = 'Unsupported COR format. Allowed: PDF, JPG, PNG, WEBP, HEIC, TIFF, BMP, GIF';
                    echo json_encode($response);
                    exit;
                }
                
                if (!is_dir($corUploadDir)) {
                    mkdir($corUploadDir, 0755, true);
                }
                
                $corFileName = uniqid() . '_COR_' . $studentId . '.' . $fileExtension;
                $corFilePath = $corUploadDir . $corFileName;
                
                if (!move_uploaded_file($documentFile['tmp_name'], $corFilePath)) {
                    $response['message'] = 'Failed to upload document';
                    echo json_encode($response);
                    exit;
                }
                
                $fileToExtract = $corFilePath;
            } else {
                // Image file - treat as Student ID
                if (!in_array($documentFile['type'], $allowedImageTypes)) {
                    $response['message'] = 'Unsupported file format. Allowed: PDF, JPG, PNG, WEBP, HEIC, TIFF, BMP, GIF';
                    echo json_encode($response);
                    exit;
                }
                
                if (!is_dir($studentIdUploadDir)) {
                    mkdir($studentIdUploadDir, 0755, true);
                }
                
                $studentIdFileName = $studentId . '.' . $fileExtension;
                $studentIdFilePath = $studentIdUploadDir . $studentIdFileName;
                
                if (!move_uploaded_file($documentFile['tmp_name'], $studentIdFilePath)) {
                    $response['message'] = 'Failed to upload document';
                    echo json_encode($response);
                    exit;
                }
                
                $fileToExtract = $studentIdFilePath;
            }
        }
        
        // Handle legacy dual field approach (for backward compatibility)
        if ($hasStudentId && !$hasDocument) {
            $studentIdFile = $_FILES['studentIdImage'];
            
            if (!in_array($studentIdFile['type'], $allowedImageTypes)) {
                $response['message'] = 'Unsupported Student ID format. Allowed: JPG, PNG, WEBP, HEIC, TIFF, BMP, GIF';
                echo json_encode($response);
                exit;
            }
            
            if ($studentIdFile['size'] > 1 * 1024 * 1024) {
                $response['message'] = 'File size must be less than 1MB. Please upload a smaller file.';
                echo json_encode($response);
                exit;
            }
            
            if (!is_dir($studentIdUploadDir)) {
                mkdir($studentIdUploadDir, 0755, true);
            }
            
            $studentIdExtension = pathinfo($studentIdFile['name'], PATHINFO_EXTENSION);
            $studentIdFileName = $studentId . '.' . $studentIdExtension;
            $studentIdFilePath = $studentIdUploadDir . $studentIdFileName;
            
            if (!move_uploaded_file($studentIdFile['tmp_name'], $studentIdFilePath)) {
                $response['message'] = 'Failed to upload student ID image';
                echo json_encode($response);
                exit;
            }
            
            $fileToExtract = $studentIdFilePath;
        }
        
        // Handle COR if provided (legacy - takes priority for extraction)
        if ($hasCor && !$hasDocument) {
            $corFile = $_FILES['corFile'];
            
            if (!in_array($corFile['type'], $allowedCorTypes)) {
                if ($studentIdFileName) unlink($studentIdUploadDir . $studentIdFileName);
                $response['message'] = 'Unsupported COR format. Allowed: PDF, JPG, PNG, WEBP, HEIC, TIFF, BMP, GIF';
                echo json_encode($response);
                exit;
            }
            
            if ($corFile['size'] > 1 * 1024 * 1024) {
                if ($studentIdFileName) unlink($studentIdUploadDir . $studentIdFileName);
                $response['message'] = 'File size must be less than 1MB. Please upload a smaller file.';
                echo json_encode($response);
                exit;
            }
            
            if (!is_dir($corUploadDir)) {
                mkdir($corUploadDir, 0755, true);
            }
            
            $corExtension = pathinfo($corFile['name'], PATHINFO_EXTENSION);
            $corFileName = uniqid() . '_COR_' . $studentId . '.' . $corExtension;
            $corFilePath = $corUploadDir . $corFileName;
            
            if (!move_uploaded_file($corFile['tmp_name'], $corFilePath)) {
                if ($studentIdFileName) unlink($studentIdUploadDir . $studentIdFileName);
                $response['message'] = 'Failed to upload COR file';
                echo json_encode($response);
                exit;
            }
            
            // COR takes priority for extraction
            $fileToExtract = $corFilePath;
        }
        
        // Extract name and student ID from whichever document is provided
        define('EXTRACT_STUDENT_INFO_INCLUDED', true);
        require_once '../api/extract-student-info.php';
        
        $apiKey = AppConfig::get('OCR_SPACE_API_KEY', '');
        $extractedName = '';
        $extractedStudentId = '';
        $isStudentIdUpload = !empty($studentIdFileName) && empty($corFileName); // Track if Student ID was uploaded (and not COR)
        
        if (!empty($apiKey) && $fileToExtract) {
            $ocrText = performOCR($fileToExtract, $apiKey);
            if (!empty($ocrText)) {
                $extractedName = extractStudentName($ocrText);
                $extractedStudentId = extractStudentId($ocrText);
            }
        }
        
        // Validate student ID matches entered student ID
        if (!empty($extractedStudentId)) {
            $normalizedEnteredId = normalizeStudentIdForMasterlist($studentId);
            $normalizedExtractedId = normalizeStudentIdForMasterlist($extractedStudentId);
            if ($normalizedEnteredId !== $normalizedExtractedId) {
                if ($studentIdFileName) unlink($studentIdUploadDir . $studentIdFileName);
                if ($corFileName) unlink($corUploadDir . $corFileName);
                $response['message'] = 'Student ID in document does not match the entered Student ID. Please check and try again.';
                echo json_encode($response);
                exit;
            }
        }
        
        // Validate student ID was extracted
        if (empty($extractedStudentId)) {
            if ($studentIdFileName) unlink($studentIdUploadDir . $studentIdFileName);
            if ($corFileName) unlink($corUploadDir . $corFileName);
            $response['message'] = 'Could not extract student ID from document. Please ensure the document is clear and readable.';
            echo json_encode($response);
            exit;
        }
        
        // Get name from masterlist (authoritative source) for validation and auto-fill
        $normalizedId = normalizeStudentIdForMasterlist($extractedStudentId);
        $stmt = $conn->prepare("SELECT student_id, name FROM masterlist WHERE student_id = ?");
        $stmt->execute([$normalizedId]);
        $masterlistEntry = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$masterlistEntry) {
            if ($studentIdFileName) unlink($studentIdUploadDir . $studentIdFileName);
            if ($corFileName) unlink($corUploadDir . $corFileName);
            $response['message'] = 'Student not found. Please ensure you are registered in the official student list.';
            echo json_encode($response);
            exit;
        }
        
        // For Student ID uploads: Only validate student ID exists in masterlist (no name check)
        // For COR uploads: Validate both student ID and name
        if ($isStudentIdUpload) {
            // Student ID uploads: Just check if student ID exists in masterlist
            // No name validation needed
            error_log("=== STUDENT ID VALIDATION (ID ONLY) ===");
            error_log("Student ID: " . $extractedStudentId);
            error_log("Found in masterlist: YES");
        } else {
            // COR uploads: Validate name matches masterlist
            if (empty($extractedName)) {
                if ($studentIdFileName) unlink($studentIdUploadDir . $studentIdFileName);
                if ($corFileName) unlink($corUploadDir . $corFileName);
                $response['message'] = 'Could not extract name from COR. Please ensure the document is clear and readable.';
                echo json_encode($response);
                exit;
            }
            
            $masterlistValidation = validateAgainstMasterlist($conn, $extractedStudentId, $extractedName, false);
            if (!$masterlistValidation['valid']) {
                if ($studentIdFileName) unlink($studentIdUploadDir . $studentIdFileName);
                if ($corFileName) unlink($corUploadDir . $corFileName);
                $response['message'] = $masterlistValidation['message'];
                echo json_encode($response);
                exit;
            }
        }
        
        // Use masterlist name for auto-filling (stored in database later)
        $masterlistName = $masterlistEntry['name'];
        $masterlistNameParts = preg_split('/\s+/', trim($masterlistName));
        $namePartsCount = count($masterlistNameParts);
        
        // Smart name extraction: Handle multi-word first names
        // Pattern: For names like "Christine Nicole Valdellon" or "Juan Carlos Dela Cruz"
        // - If 2 parts: First = first part, Last = last part
        // - If 3 parts: First = first 2 parts (e.g., "Christine Nicole"), Last = last part
        // - If 4+ parts: First = first 2 parts, Middle = middle parts, Last = last part
        if ($namePartsCount === 1) {
            $masterlistFirstName = $masterlistNameParts[0];
            $masterlistLastName = '';
            $middleName = null;
        } elseif ($namePartsCount === 2) {
            $masterlistFirstName = $masterlistNameParts[0];
            $masterlistLastName = $masterlistNameParts[1];
            $middleName = null;
        } elseif ($namePartsCount === 3) {
            // For 3 parts, include first 2 as first name (e.g., "Christine Nicole")
            $masterlistFirstName = $masterlistNameParts[0] . ' ' . $masterlistNameParts[1];
            $masterlistLastName = $masterlistNameParts[2];
            $middleName = null;
        } else {
            // For 4+ parts, first 2 parts as first name, rest as middle, last as last name
            $masterlistFirstName = $masterlistNameParts[0] . ' ' . $masterlistNameParts[1];
            $masterlistLastName = end($masterlistNameParts);
            $middleParts = array_slice($masterlistNameParts, 2, -1);
            $middleName = !empty($middleParts) ? implode(' ', $middleParts) : null;
        }
        
        // Update form data with masterlist name
        $firstName = trim($masterlistFirstName);
        $lastName = trim($masterlistLastName);
        
        // Check if this is a re-registration (rejected student)
        $isReRegistration = ($existingReg && $existingReg['approval_status'] === 'rejected');
        
        $studentIdImagePath = $studentIdFileName ? 'uploads/student-ids/' . $studentIdFileName : null;
        $corFilePath = $corFileName ? 'uploads/documents/' . $corFileName : null;
        
        if ($isReRegistration) {
            // Update existing rejected registration
            $updateQuery = "UPDATE student_registrations SET 
                           first_name=?, middle_name=?, last_name=?, email=?, course=?, year_level=?, section=?, age=?, gender=?, 
                           student_id_image=?, cor_file=?, set_password_token=?, set_password_expires_at=?, 
                           approval_status='pending',
                           rejected_at=NULL, rejection_reason=NULL, approved_at=NULL, approved_by=NULL
                           WHERE id=?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->execute([
                $firstName, $middleName, $lastName, $email, $course, $yearLevel, $section, $age, $gender,
                $studentIdImagePath,
                $corFilePath,
                $token, $tokenExpiresAt, $studentId
            ]);
        } else {
            // Insert new registration
            $insertQuery = "INSERT INTO student_registrations 
                           (id, first_name, middle_name, last_name, email, course, year_level, section, age, gender, student_id_image, cor_file, set_password_token, set_password_expires_at, approval_status) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->execute([
                $studentId, $firstName, $middleName, $lastName, $email, $course, $yearLevel, $section, $age, $gender,
                $studentIdImagePath,
                $corFilePath,
                $token, $tokenExpiresAt
            ]);
        }

        // Run document verification (but with timeout protection)
        $verifier = new DocumentVerificationService();
        
        // Set a timeout for the verification process
        $startTime = time();
        $maxTime = 15; // 15 seconds max
        
        try {
            $verificationResult = $verifier->runVerification($studentId);
            
            // Check if we took too long
            if ((time() - $startTime) > $maxTime) {
                throw new Exception("Verification timeout - documents may be unclear");
            }
            
        } catch (Exception $e) {
            // If verification fails or times out, reject the registration
            $verificationResult = [
                'overall_result' => 'mismatch',
                'reason' => 'Document verification failed: ' . $e->getMessage()
            ];
        }
        
        if ($verificationResult['overall_result'] === 'valid') {
            // Auto-approve and insert into students table
            $insertStudentQuery = "INSERT INTO students 
                                  (id, first_name, middle_name, last_name, email, password, course, year_level, section, age, gender, is_active) 
                                  VALUES (?, ?, ?, ?, ?, '', ?, ?, ?, ?, ?, 1)";
            $insertStudentStmt = $conn->prepare($insertStudentQuery);
            $insertStudentStmt->execute([
                $studentId,
                $firstName,
                $middleName,
                $lastName,
                $email,
                $course,
                $yearLevel,
                $section,
                $age,
                $gender
            ]);

            // Mark as approved in registrations table
            $approveQuery = "UPDATE student_registrations SET approval_status='approved', approved_at=NOW(), approved_by='System' WHERE id=?";
            $approveStmt = $conn->prepare($approveQuery);
            $approveStmt->execute([$studentId]);

            // Send approval email with password setup link
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
            // Reject registration
            $rejectQuery = "UPDATE student_registrations SET approval_status='rejected', rejected_at=NOW(), rejection_reason=? WHERE id=?";
            $rejectStmt = $conn->prepare($rejectQuery);
            $rejectStmt->execute([$verificationResult['reason'], $studentId]);

            // Send rejection email
            $emailService = new EmailService();
            $emailService->sendRejectionNotification($email, $firstName, $studentId, $verificationResult['reason']);
            
            $rejectionMessage = $isReRegistration ? 
                'Re-registration rejected: ' . $verificationResult['reason'] . ' You can try registering again with correct documents.' :
                'Registration rejected: ' . $verificationResult['reason'] . ' You can try registering again with correct documents.';
            
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
