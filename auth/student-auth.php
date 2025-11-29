<?php
session_start();
header('Content-Type: application/json');

require_once '../includes/database.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $response['message'] = 'Email and password are required';
        echo json_encode($response);
        exit;
    }
    
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Check if student exists and is approved (allow login with email OR student ID)
        $query = "SELECT * FROM students WHERE (email = ? OR id = ?) AND is_active = 1";
        $stmt = $conn->prepare($query);
        $stmt->execute([$email, $email]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($student) {
            // Student exists, check password
            if (password_verify($password, $student['password'])) {
                // Create session
                $_SESSION['student'] = [
                    'id' => $student['id'],
                    'firstName' => $student['first_name'],
                    'middleName' => $student['middle_name'],
                    'lastName' => $student['last_name'],
                    'email' => $student['email'],
                    'yearLevel' => $student['year_level'],
                    'section' => $student['section'],
                    'course' => $student['course'],
                    'age' => $student['age'],
                    'gender' => $student['gender']
                ];
                
                $response = [
                    'status' => 'success',
                    'message' => 'Login successful',
                    'redirect' => '../pages/student-dashboard.php'
                ];
            } else {
                // Student exists but wrong password
                $response['message'] = 'Invalid Student ID/Email or password';
            }
        } else {
            // Student doesn't exist in approved students, check registration status
            $pendingQuery = "SELECT * FROM student_registrations WHERE (email = ? OR id = ?) AND approval_status = 'pending'";
            $pendingStmt = $conn->prepare($pendingQuery);
            $pendingStmt->execute([$email, $email]);
            $pendingStudent = $pendingStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($pendingStudent) {
                $response['message'] = 'Your registration is still pending approval. Please wait for admin approval.';
            } else {
                // Check if student registration was rejected
                $rejectedQuery = "SELECT * FROM student_registrations WHERE (email = ? OR id = ?) AND approval_status = 'rejected'";
                $rejectedStmt = $conn->prepare($rejectedQuery);
                $rejectedStmt->execute([$email, $email]);
                $rejectedStudent = $rejectedStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($rejectedStudent) {
                    $response['message'] = 'Your registration was rejected. Reason: ' . ($rejectedStudent['rejection_reason'] ?? 'No reason provided');
                } else {
                    $response['message'] = 'Student ID/Email not found. Please check your credentials or register first.';
                }
            }
        }
        
    } catch (Exception $e) {
        $response['message'] = 'Login failed. Please try again.';
        error_log('Student login error: ' . $e->getMessage());
    }
}

echo json_encode($response);
?>
