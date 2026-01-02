<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
require_once '../includes/database.php';
require_once '../includes/activity_logger.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if (!isset($_POST['student_id']) || !isset($_POST['amount']) || !isset($_POST['payment_date'])) {
        throw new Exception('Student ID, amount, and payment date are required');
    }
    
    $studentId = $_POST['student_id'];
    $amount = floatval($_POST['amount']);
    $paymentDate = $_POST['payment_date'];
    
    if ($amount <= 0) {
        throw new Exception('Amount must be greater than zero');
    }
    
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        throw new Exception('Student not found');
    }
    
    $pdo->beginTransaction();
    
    try {
        // Generate sequential control number
        $countStmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM students 
            WHERE membership_fee_status = 'paid' 
            AND id != ?
        ");
        $countStmt->execute([$studentId]);
        $count = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        $controlNumber = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        
        // Get admin name
        $adminName = 'ADMIN';
        if (isset($_SESSION['user_id'])) {
            $userStmt = $pdo->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
            $userStmt->execute([$_SESSION['user_id']]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $adminName = trim($user['first_name'] . ' ' . $user['last_name']);
                if (empty($adminName)) {
                    $adminName = $user['email'];
                }
            }
        }
        
        // Update student record
        $updateSql = "UPDATE students SET 
                      membership_fee_status = 'paid',
                      membership_fee_paid_at = ?,
                      membership_control_number = ?,
                      membership_processed_by = ?
                      WHERE id = ?";
        
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([$paymentDate, $controlNumber, $adminName, $studentId]);
        
        $pdo->commit();
        
        if (isset($_SESSION['user_id'])) {
            $studentName = $student['first_name'] . ' ' . $student['last_name'];
            logMembershipActivity(
                $_SESSION['user_id'], 
                'payment_recorded', 
                'Recorded membership payment for ' . $studentName . ' (ID: ' . $studentId . ') - Amount: ₱' . number_format($amount, 2) . ' - Control No: ' . $controlNumber
            );
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Payment recorded successfully',
            'control_number' => $controlNumber,
            'student' => [
                'id' => $student['id'],
                'full_name' => $student['first_name'] . ' ' . $student['last_name'],
                'course' => $student['course'],
                'year_level' => $student['year_level'],
                'membership_control_number' => $controlNumber
            ]
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
