<?php
header('Content-Type: application/json');
require_once '../../includes/database.php';

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
        echo json_encode(['success' => false, 'error' => 'Invalid candidate ID']);
        exit;
    }
    
    $stmt = $pdo->prepare("SELECT photo FROM candidates WHERE id = ?");
    $stmt->execute([$id]);
    $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$candidate) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Candidate not found']);
        exit;
    }
    
    $deleteStmt = $pdo->prepare("DELETE FROM candidates WHERE id = ?");
    $deleteStmt->execute([$id]);
    
    if ($candidate['photo'] && file_exists('../../' . $candidate['photo'])) {
        unlink('../../' . $candidate['photo']);
    }
    
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    error_log("Candidate delete error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to delete candidate']);
}

