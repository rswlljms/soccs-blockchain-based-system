<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Check for announcements created in the last 24 hours
    $query = "SELECT COUNT(*) as new_count 
              FROM announcements 
              WHERE is_active = 1 
                AND (target_audience = 'all' OR target_audience = 'students')
                AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $newCount = (int) $result['new_count'];
    
    echo json_encode([
        'status' => 'success',
        'hasNew' => $newCount > 0,
        'count' => $newCount
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unable to check for new announcements',
        'error' => $e->getMessage()
    ]);
}
?>
