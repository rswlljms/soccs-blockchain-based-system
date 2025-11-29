<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $studentId = $_POST['student_id'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    if (empty($studentId)) {
        throw new Exception('Student ID is required');
    }
    
    if (!isset($_FILES['receipt']) || $_FILES['receipt']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Receipt file is required');
    }
    
    $file = $_FILES['receipt'];
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    // Validate file type
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG, and PDF files are allowed.');
    }
    
    // Validate file size
    if ($file['size'] > $maxSize) {
        throw new Exception('File size must be less than 5MB.');
    }
    
    // Create receipts directory if it doesn't exist
    $receiptsDir = '../uploads/receipts/';
    if (!is_dir($receiptsDir)) {
        mkdir($receiptsDir, 0755, true);
    }
    
    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'receipt_' . $studentId . '_' . time() . '.' . $fileExtension;
    $filePath = $receiptsDir . $fileName;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception('Failed to upload file');
    }
    
    // Update database
    $stmt = $pdo->prepare("
        UPDATE students 
        SET membership_fee_status = 'paid', 
            membership_fee_receipt = ?, 
            membership_fee_paid_at = NOW() 
        WHERE id = ?
    ");
    
    $stmt->execute([$fileName, $studentId]);
    
    if ($stmt->rowCount() === 0) {
        // Clean up uploaded file if database update failed
        unlink($filePath);
        throw new Exception('Student not found');
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Receipt uploaded successfully',
        'receipt_file' => $fileName
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}
?>
