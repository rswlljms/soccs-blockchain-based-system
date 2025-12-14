<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
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
              WHERE status IN ('active', 'upcoming')
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

