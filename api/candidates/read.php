<?php
header('Content-Type: application/json');
require_once '../../includes/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    $stmt = $pdo->query(
        "SELECT 
            c.id,
            c.firstname,
            c.lastname,
            c.partylist,
            c.position_id as positionId,
            p.description as position,
            c.platform,
            c.photo
         FROM candidates c
         INNER JOIN positions p ON c.position_id = p.id
         ORDER BY c.id DESC"
    );
    $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($candidates as &$candidate) {
        if (empty($candidate['photo'])) {
            $candidate['photo'] = '../assets/img/logo.png';
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $candidates
    ]);
    
} catch (PDOException $e) {
    error_log("Candidate read error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to fetch candidates']);
}

