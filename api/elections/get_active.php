<?php
header('Content-Type: application/json');
require_once '../../includes/database.php';

$database = new Database();
$conn = $database->getConnection();

try {
    $updateQuery = "UPDATE elections 
                    SET status = 'active' 
                    WHERE status = 'upcoming' 
                    AND NOW() >= start_date 
                    AND NOW() <= end_date";
    $conn->exec($updateQuery);
    
    $query = "SELECT * FROM elections 
              WHERE (status = 'active' AND NOW() BETWEEN start_date AND end_date) 
                 OR (status = 'upcoming' AND start_date > NOW())
              ORDER BY 
                CASE 
                  WHEN status = 'active' THEN 1 
                  WHEN status = 'upcoming' THEN 2 
                END,
                start_date DESC 
              LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $election = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($election) {
        $statsQuery = "SELECT 
            (SELECT COUNT(*) FROM positions WHERE election_id = :election_id) as total_positions,
            (SELECT COUNT(*) FROM candidates WHERE election_id = :election_id) as total_candidates,
            (SELECT COUNT(*) FROM students WHERE is_active = 1) as eligible_voters";
        
        $statsStmt = $conn->prepare($statsQuery);
        $statsStmt->execute(['election_id' => $election['id']]);
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

