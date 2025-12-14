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
    $firstName = trim($_POST['firstName'] ?? '');
    $middleName = trim($_POST['middleName'] ?? '');
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
    
    if (empty($tempId) || empty($studentIdPath) || empty($corPath)) {
        $response['message'] = 'Invalid session. Please start from step 1.';
        error_log("Missing session data - tempId: $tempId, studentIdPath: $studentIdPath, corPath: $corPath");
        echo json_encode($response);
        exit;
    }
    
    error_log("Checking file paths - studentIdPath: $studentIdPath, corPath: $corPath");
    error_log("studentIdPath exists: " . (file_exists($studentIdPath) ? 'YES' : 'NO'));
    error_log("corPath exists: " . (file_exists($corPath) ? 'YES' : 'NO'));
    
    if (!file_exists($studentIdPath) || !file_exists($corPath)) {
        $response['message'] = 'Uploaded documents not found. studentIdPath: ' . ($studentIdPath ?: 'empty') . ', corPath: ' . ($corPath ?: 'empty');
        echo json_encode($response);
        exit;
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
        
        unlink($studentIdPath);
        unlink($corPath);
        
        $isReRegistration = ($existingReg && $existingReg['approval_status'] === 'rejected');
        
        if ($isReRegistration) {
            $updateQuery = "UPDATE student_registrations SET 
                           first_name=?, middle_name=?, last_name=?, email=?, course=?, year_level=?, section=?, gender=?, 
                           student_id_image=?, cor_file=?, set_password_token=?, set_password_expires_at=?, 
                           approval_status='pending',
                           rejected_at=NULL, rejection_reason=NULL, approved_at=NULL, approved_by=NULL
                           WHERE id=?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->execute([
                $firstName, $middleName, $lastName, $email, $course, $yearLevel, $section, $gender,
                'uploads/student-ids/' . $finalStudentIdName,
                'uploads/documents/' . $finalCorName,
                $token, $tokenExpiresAt, $studentId
            ]);
        } else {
            $insertQuery = "INSERT INTO student_registrations
                           (id, first_name, middle_name, last_name, email, course, year_level, section, gender, student_id_image, cor_file, set_password_token, set_password_expires_at, approval_status)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->execute([
                $studentId, $firstName, $middleName, $lastName, $email, $course, $yearLevel, $section, $gender,
                'uploads/student-ids/' . $finalStudentIdName,
                'uploads/documents/' . $finalCorName,
                $token, $tokenExpiresAt
            ]);
        }

        $verifier = new DocumentVerificationService();
        
        $startTime = time();
        $maxTime = 15;
        
        try {
            $verificationResult = $verifier->runVerification($studentId);
            
            if ((time() - $startTime) > $maxTime) {
                throw new Exception("Verification timeout - documents may be unclear");
            }
            
        } catch (Exception $e) {
            $verificationResult = [
                'overall_result' => 'mismatch',
                'reason' => 'Document verification failed: ' . $e->getMessage()
            ];
        }
        
        if ($verificationResult['overall_result'] === 'valid') {
            $insertStudentQuery = "INSERT INTO students 
                                  (id, first_name, middle_name, last_name, email, password, course, year_level, section, gender, is_active) 
                                  VALUES (?, ?, ?, ?, ?, '', ?, ?, ?, ?, 1)";
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
                $gender
            ]);

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
            $rejectQuery = "UPDATE student_registrations SET approval_status='rejected', rejected_at=NOW(), rejection_reason=? WHERE id=?";
            $rejectStmt = $conn->prepare($rejectQuery);
            $rejectStmt->execute([$verificationResult['reason'], $studentId]);

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
