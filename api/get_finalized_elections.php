<?php
header('Content-Type: application/json');
require_once '../includes/database.php';

$database = new Database();
$conn = $database->getConnection();

try {
    $query = "SELECT 
        id,
        title,
        end_date,
        updated_at,
        transaction_hash
    FROM elections
    WHERE status = 'completed'
    ORDER BY updated_at DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $elections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $result = [];
    foreach ($elections as $election) {
        $result[] = [
            'id' => $election['id'],
            'title' => $election['title'],
            'date' => $election['end_date'],
            'finalized_at' => $election['updated_at'],
            'transaction_hash' => $election['transaction_hash'] ?? null
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $result,
        'total' => count($result)
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>

