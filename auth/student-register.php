<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/database.php';
require_once '../includes/email_config.php';
require_once '../includes/document_verification_service.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'] ?? '';
    $middleName = $_POST['middleName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
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
    
    // Validate student ID image upload
    if (!isset($_FILES['studentIdImage']) || $_FILES['studentIdImage']['error'] !== UPLOAD_ERR_OK) {
        $response['message'] = 'Student ID image is required';
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
        
        // Handle file upload
        $imageFile = $_FILES['studentIdImage'];
        $allowedTypes = [
            'image/jpeg','image/jpg','image/png','image/heic','image/heif','image/webp','image/tiff','image/bmp','image/gif','application/pdf'
        ];
        
        if (!in_array($imageFile['type'], $allowedTypes)) {
            $response['message'] = 'Unsupported Student ID format. Allowed: JPG, PNG, WEBP, HEIC, TIFF, BMP, GIF, PDF';
            echo json_encode($response);
            exit;
        }
        
        if ($imageFile['size'] > 5 * 1024 * 1024) { // 5MB limit
            $response['message'] = 'Image file size must be less than 5MB';
            echo json_encode($response);
            exit;
        }
        
        $uploadDir = '../uploads/student-ids/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileExtension = pathinfo($imageFile['name'], PATHINFO_EXTENSION);
        $fileName = $studentId . '.' . $fileExtension;
        $filePath = $uploadDir . $fileName;
        
        if (!move_uploaded_file($imageFile['tmp_name'], $filePath)) {
            $response['message'] = 'Failed to upload student ID image';
            echo json_encode($response);
            exit;
        }
        
        $corFileName = null;
        if (isset($_FILES['corFile']) && $_FILES['corFile']['error'] === UPLOAD_ERR_OK) {
            $corFile = $_FILES['corFile'];
            $corAllowed = [
                'application/pdf','image/jpeg','image/jpg','image/png','image/heic','image/heif','image/webp','image/tiff','image/bmp','image/gif'
            ];
            if (!in_array($corFile['type'], $corAllowed)) {
                $response['message'] = 'Unsupported COR format. Allowed: PDF, JPG, PNG, WEBP, HEIC, TIFF, BMP, GIF';
                echo json_encode($response);
                exit;
            }
            $corUploadDir = '../uploads/documents/';
            if (!is_dir($corUploadDir)) {
                mkdir($corUploadDir, 0755, true);
            }
            
            $corExtension = pathinfo($corFile['name'], PATHINFO_EXTENSION);
            $corFileName = uniqid() . '_COR_' . $studentId . '.' . $corExtension;
            $corFilePath = $corUploadDir . $corFileName;
            
            if (!move_uploaded_file($corFile['tmp_name'], $corFilePath)) {
                $response['message'] = 'Failed to upload COR file';
                echo json_encode($response);
                exit;
            }
        }
        
        // Check if this is a re-registration (rejected student)
        $isReRegistration = ($existingReg && $existingReg['approval_status'] === 'rejected');
        
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
                'uploads/student-ids/' . $fileName,
                $corFileName ? 'uploads/documents/' . $corFileName : null,
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
                'uploads/student-ids/' . $fileName,
                $corFileName ? 'uploads/documents/' . $corFileName : null,
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
