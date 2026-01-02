<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
require_once '../includes/database.php';
require_once '../includes/email_config.php';
require_once '../includes/activity_logger.php';

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $studentId = $input['student_id'] ?? '';
    $action = $input['action'] ?? 'approve';
    $reason = $input['reason'] ?? '';
    
    if (empty($studentId)) {
        $response['message'] = 'Student ID is required';
        echo json_encode($response);
        exit;
    }
    
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Get student registration details
        $getQuery = "SELECT * FROM student_registrations WHERE id = ?";
        $getStmt = $conn->prepare($getQuery);
        $getStmt->execute([$studentId]);
        $student = $getStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$student) {
            $response['message'] = 'Student registration not found';
            echo json_encode($response);
            exit;
        }
        
        if ($student['approval_status'] !== 'pending') {
            $response['message'] = 'Student registration has already been processed';
            echo json_encode($response);
            exit;
        }
        
        if ($action === 'approve') {
            // Start transaction
            $conn->beginTransaction();
            
            try {
                // Update registration status
                $updateQuery = "UPDATE student_registrations 
                               SET approval_status = 'approved', 
                                   approved_at = NOW(), 
                                   approved_by = 'Admin'
                               WHERE id = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->execute([$studentId]);
                
                // Insert into students table
                $insertQuery = "INSERT INTO students 
                               (id, first_name, middle_name, last_name, email, password, 
                                course, year_level, section, age, gender, academic_year, semester, is_active) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->execute([
                    $student['id'],
                    $student['first_name'],
                    $student['middle_name'],
                    $student['last_name'],
                    $student['email'],
                    $student['password'],
                    $student['course'],
                    $student['year_level'],
                    $student['section'],
                    $student['age'],
                    $student['gender'],
                    $student['academic_year'] ?? null,
                    $student['semester'] ?? null
                ]);
                
                $conn->commit();
                
                // Send approval email
                $emailService = new EmailService();
                $emailService->sendApprovalNotification(
                    $student['email'], 
                    $student['first_name'], 
                    $student['id']
                );
                
                if (isset($_SESSION['user_id'])) {
                    $studentName = $student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . $student['last_name'];
                    logStudentActivity($_SESSION['user_id'], 'approve', 'Approved student registration: ' . $student['id'] . ' (' . trim($studentName) . ')');
                }
                
                $response = [
                    'success' => true,
                    'message' => 'Student approved successfully'
                ];
                
            } catch (Exception $e) {
                $conn->rollBack();
                throw $e;
            }
            
        } elseif ($action === 'reject') {
            if (empty($reason)) {
                $response['message'] = 'Rejection reason is required';
                echo json_encode($response);
                exit;
            }
            
            // Update registration status
            $updateQuery = "UPDATE student_registrations 
                           SET approval_status = 'rejected', 
                               rejected_at = NOW(), 
                               rejection_reason = ?
                           WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->execute([$reason, $studentId]);
            
            // Send rejection email
            $emailService = new EmailService();
            $emailService->sendRejectionNotification(
                $student['email'], 
                $student['first_name'], 
                $student['id'],
                $reason
            );
            
            if (isset($_SESSION['user_id'])) {
                $studentName = $student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . $student['last_name'];
                logStudentActivity($_SESSION['user_id'], 'reject', 'Rejected student registration: ' . $student['id'] . ' (' . trim($studentName) . ') - Reason: ' . $reason);
            }
            
            $response = [
                'success' => true,
                'message' => 'Student registration rejected successfully'
            ];
        } else {
            $response['message'] = 'Invalid action';
        }
        
    } catch (Exception $e) {
        $response['message'] = 'Failed to process student: ' . $e->getMessage();
        error_log('Approve student error: ' . $e->getMessage());
    }
}

echo json_encode($response);
?>