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
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['student_id'])) {
        throw new Exception('Student ID is required');
    }
    
    $studentId = $input['student_id'];
    $action = $input['action'] ?? 'toggle';
    
    $stmt = $pdo->prepare("SELECT membership_fee_status, membership_fee_receipt FROM students WHERE id = ?");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        throw new Exception('Student not found');
    }
    
    $currentStatus = $student['membership_fee_status'] ?? 'unpaid';
    $newStatus = $currentStatus === 'paid' ? 'unpaid' : 'paid';
    
    if ($action === 'set_unpaid') {
        $newStatus = 'unpaid';
    }
    
    // Check if trying to mark as paid without receipt
    if ($newStatus === 'paid' && empty($student['membership_fee_receipt'])) {
        throw new Exception('Receipt is required to mark student as paid. Please upload a receipt first.');
    }
    
    // Generate control number if marking as paid
    $controlNumber = null;
    if ($newStatus === 'paid') {
        // Get the count of paid memberships + 1
        $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM students WHERE membership_fee_status = 'paid' AND id != ?");
        $countStmt->execute([$studentId]);
        $count = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        $controlNumber = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }
    
    $updateSql = "UPDATE students SET 
                  membership_fee_status = ?,
                  membership_fee_paid_at = " . ($newStatus === 'paid' ? 'NOW()' : 'NULL') . ",
                  membership_control_number = ?
                  WHERE id = ?";
    
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([$newStatus, $controlNumber, $studentId]);
    
    if (isset($_SESSION['user_id'])) {
        $getStudentQuery = "SELECT first_name, last_name FROM students WHERE id = ?";
        $getStudentStmt = $pdo->prepare($getStudentQuery);
        $getStudentStmt->execute([$studentId]);
        $studentInfo = $getStudentStmt->fetch(PDO::FETCH_ASSOC);
        $studentName = $studentInfo ? ($studentInfo['first_name'] . ' ' . $studentInfo['last_name']) : 'Student #' . $studentId;
        
        logMembershipActivity($_SESSION['user_id'], 'update_status', 'Updated membership status to ' . $newStatus . ' for student: ' . $studentId . ' (' . $studentName . ')');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Membership status updated successfully',
        'new_status' => $newStatus
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

