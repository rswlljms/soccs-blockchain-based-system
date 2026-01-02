<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Get the count of paid memberships
    $countStmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM students 
        WHERE membership_fee_status = 'paid'
    ");
    $countStmt->execute();
    $count = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Next control number
    $nextControlNumber = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    
    echo json_encode([
        'success' => true,
        'next_control_number' => $nextControlNumber
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
