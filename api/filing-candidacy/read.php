<?php
header('Content-Type: application/json');
require_once '../../includes/database.php';

$database = new Database();
$conn = $database->getConnection();

try {
    $query = "SELECT fcp.*, CONCAT(u.first_name, ' ', u.last_name) as created_by_name 
              FROM filing_candidacy_periods fcp
              LEFT JOIN users u ON fcp.created_by = u.id
              ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $periods = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $periods
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

