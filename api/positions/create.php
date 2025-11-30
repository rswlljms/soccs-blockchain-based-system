<?php
session_start();
header('Content-Type: application/json');
require_once '../../includes/database.php';
require_once '../../includes/activity_logger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $description = trim($data['description'] ?? '');
    $maxVotes = intval($data['maxVotes'] ?? 1);
    
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
    
    $stmt = $pdo->prepare("INSERT INTO positions (description, max_votes) VALUES (?, ?)");
    $stmt->execute([$description, $maxVotes]);
    
    $newId = $pdo->lastInsertId();
    
    if (isset($_SESSION['user_id'])) {
        logPositionActivity($_SESSION['user_id'], 'create', 'Created position: ' . $description . ' (Max votes: ' . $maxVotes . ')');
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'id' => $newId,
            'description' => $description,
            'maxVotes' => $maxVotes
        ]
    ]);
    
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        http_response_code(409);
        echo json_encode(['success' => false, 'error' => 'Position already exists']);
    } else {
        error_log("Position create error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to create position']);
    }
}

