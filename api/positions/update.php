<?php
header('Content-Type: application/json');
require_once '../../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $id = intval($data['id'] ?? 0);
    $description = trim($data['description'] ?? '');
    $maxVotes = intval($data['maxVotes'] ?? 1);
    
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid position ID']);
        exit;
    }
    
    if (empty($description)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Description is required']);
        exit;
    }
    
    if ($maxVotes < 1) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Maximum votes must be at least 1']);
        exit;
    }
    
    $stmt = $pdo->prepare("UPDATE positions SET description = ?, max_votes = ? WHERE id = ?");
    $stmt->execute([$description, $maxVotes, $id]);
    
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Position not found']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'id' => $id,
            'description' => $description,
            'maxVotes' => $maxVotes
        ]
    ]);
    
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        http_response_code(409);
        echo json_encode(['success' => false, 'error' => 'Position already exists']);
    } else {
        error_log("Position update error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to update position']);
    }
}

