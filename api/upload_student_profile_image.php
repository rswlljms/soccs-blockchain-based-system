<?php
session_start();
header('Content-Type: application/json');

require_once '../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['student'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if (!isset($_FILES['profileImage']) || $_FILES['profileImage']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No image uploaded']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $studentId = $_SESSION['student']['id'];
    $file = $_FILES['profileImage'];
    
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024;
    
    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF allowed']);
        exit;
    }
    
    if ($file['size'] > $maxSize) {
        echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 5MB']);
        exit;
    }
    
    $uploadDir = '../uploads/student-profiles/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $getOldImage = "SELECT profile_image FROM students WHERE id = ?";
    $stmt = $conn->prepare($getOldImage);
    $stmt->execute([$studentId]);
    $oldData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($oldData && !empty($oldData['profile_image']) && file_exists('../' . $oldData['profile_image'])) {
        unlink('../' . $oldData['profile_image']);
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'profile_' . $studentId . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        echo json_encode(['success' => false, 'message' => 'Failed to save image']);
        exit;
    }
    
    $dbPath = 'uploads/student-profiles/' . $filename;
    $updateQuery = "UPDATE students SET profile_image = ?, updated_at = NOW() WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->execute([$dbPath, $studentId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Profile image updated successfully',
        'imagePath' => $dbPath
    ]);
    
} catch (Exception $e) {
    error_log('Profile image upload error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
}
?>
