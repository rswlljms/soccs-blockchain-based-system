<?php
header('Content-Type: application/json');
require_once '../../includes/database.php';

$database = new Database();
$conn = $database->getConnection();

try {
    $query = "SELECT * FROM elections WHERE status = 'active' AND NOW() BETWEEN start_date AND end_date ORDER BY start_date DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $election = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($election) {
        $statsQuery = "SELECT 
            (SELECT COUNT(*) FROM positions) as total_positions,
            (SELECT COUNT(*) FROM candidates) as total_candidates,
            (SELECT COUNT(*) FROM students WHERE is_active = 1) as eligible_voters";
        
        $statsStmt = $conn->prepare($statsQuery);
        $statsStmt->execute();
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
        
        $election['stats'] = $stats;
        
        echo json_encode([
            'success' => true,
            'data' => $election
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'No active election found'
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

