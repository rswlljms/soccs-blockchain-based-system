<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['id'])) {
        throw new Exception('Missing event ID');
    }
    
    $database = new Database();
    $conn = $database->getConnection();
    
    $query = "DELETE FROM events WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete event');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

