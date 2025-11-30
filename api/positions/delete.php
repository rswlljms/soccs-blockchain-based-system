<?php
session_start();
header('Content-Type: application/json');
require_once '../../includes/database.php';
require_once '../../includes/activity_logger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    $data = json_decode(file_get_contents('php://input'), true);
    $id = intval($data['id'] ?? 0);
    
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid position ID']);
        exit;
    }
    
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM candidates WHERE position_id = ?");
    $checkStmt->execute([$id]);
    $count = $checkStmt->fetchColumn();
    
    if ($count > 0) {
        http_response_code(409);
        echo json_encode([
            'success' => false, 
            'error' => 'Cannot delete position with existing candidates'
        ]);
        exit;
    }
    
    $getPositionQuery = "SELECT description FROM positions WHERE id = ?";
    $getPositionStmt = $pdo->prepare($getPositionQuery);
    $getPositionStmt->execute([$id]);
    $position = $getPositionStmt->fetch(PDO::FETCH_ASSOC);
    $positionName = $position['description'] ?? 'Position #' . $id;
    
    $stmt = $pdo->prepare("DELETE FROM positions WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Position not found']);
        exit;
    }
    
    if (isset($_SESSION['user_id'])) {
        logPositionActivity($_SESSION['user_id'], 'delete', 'Deleted position: ' . $positionName . ' (ID: ' . $id . ')');
    }
    
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    error_log("Position delete error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to delete position']);
}

