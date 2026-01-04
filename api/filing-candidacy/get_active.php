<?php
header('Content-Type: application/json');
require_once '../../includes/database.php';

$database = new Database();
$conn = $database->getConnection();

try {
    $currentDateTime = date('Y-m-d H:i:s');
    
    $query = "SELECT * FROM filing_candidacy_periods 
              WHERE is_active = 1 
              AND start_date <= :current_date 
              AND end_date >= :current_date
              ORDER BY created_at DESC 
              LIMIT 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':current_date', $currentDateTime);
    $stmt->execute();
    
    $period = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($period) {
        echo json_encode([
            'success' => true,
            'data' => $period
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'data' => null
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

