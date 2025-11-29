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
    $database = new Database();
    $pdo = $database->getConnection();
    
    $input = json_decode(file_get_contents('php://input'), true);
    $studentId = $input['student_id'] ?? '';
    $action = $input['action'] ?? ''; // 'archive' or 'restore'
    
    if (empty($studentId) || empty($action)) {
        throw new Exception('Student ID and action are required');
    }
    
    if (!in_array($action, ['archive', 'restore'])) {
        throw new Exception('Invalid action. Must be "archive" or "restore"');
    }
    
    $isArchived = ($action === 'archive') ? 1 : 0;
    $archivedAt = ($action === 'archive') ? 'NOW()' : 'NULL';
    
    $stmt = $pdo->prepare("
        UPDATE students 
        SET is_archived = ?, archived_at = $archivedAt
        WHERE id = ?
    ");
    
    $stmt->execute([$isArchived, $studentId]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('Student not found');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Student ' . ($action === 'archive' ? 'archived' : 'restored') . ' successfully'
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}