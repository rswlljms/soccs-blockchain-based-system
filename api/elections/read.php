<?php
header('Content-Type: application/json');
require_once '../../includes/database.php';

$database = new Database();
$conn = $database->getConnection();

try {
    $query = "SELECT * FROM elections ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $elections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $elections
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

