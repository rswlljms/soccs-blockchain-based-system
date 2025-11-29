<?php
header('Content-Type: application/json');
require_once '../../includes/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    $stmt = $pdo->query("SELECT id, description, max_votes as maxVotes FROM positions ORDER BY id ASC");
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $positions
    ]);
    
} catch (PDOException $e) {
    error_log("Position read error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to fetch positions']);
}

