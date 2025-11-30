<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/database.php';
require_once '../includes/activity_logger.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }
    
    if (!isset($_POST['student_id'])) {
        throw new Exception('Student ID is required');
    }
    
    $studentId = $_POST['student_id'];
    
    // Check if student exists
    $stmt = $pdo->prepare("SELECT id, first_name, last_name FROM students WHERE id = ?");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        throw new Exception('Student not found');
    }
    
    // Validate file upload
    if (!isset($_FILES['receipt']) || $_FILES['receipt']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error occurred');
    }
    
    $file = $_FILES['receipt'];
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    // Validate file type
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG, GIF, and PDF files are allowed.');
    }
    
    // Validate file size
    if ($file['size'] > $maxSize) {
        throw new Exception('File size too large. Maximum size is 5MB.');
    }
    
    // Create uploads directory if it doesn't exist
    $uploadDir = '../uploads/membership-receipts/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = $studentId . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception('Failed to save uploaded file');
    }
    
    // Update student record with receipt
    $updateSql = "UPDATE students SET 
                  membership_fee_receipt = ?,
                  membership_fee_status = 'paid',
                  membership_fee_paid_at = NOW()
                  WHERE id = ?";
    
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([$fileName, $studentId]);
    
    if (isset($_SESSION['user_id'])) {
        $studentName = $student['first_name'] . ' ' . $student['last_name'];
        logMembershipActivity($_SESSION['user_id'], 'upload_receipt', 'Uploaded membership receipt for student: ' . $studentId . ' (' . $studentName . ')');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Receipt uploaded and membership status updated successfully',
        'receipt_file' => $fileName,
        'receipt_path' => 'uploads/membership-receipts/' . $fileName
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
