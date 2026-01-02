<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
require_once '../../includes/database.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/activity_logger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!hasPermission('manage_events')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Access denied. You do not have permission to delete events.']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['id'])) {
        throw new Exception('Missing event ID');
    }
    
    $database = new Database();
    $conn = $database->getConnection();
    
    $getEventQuery = "SELECT title FROM events WHERE id = :id";
    $getEventStmt = $conn->prepare($getEventQuery);
    $getEventStmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
    $getEventStmt->execute();
    $event = $getEventStmt->fetch(PDO::FETCH_ASSOC);
    $eventTitle = $event['title'] ?? 'Event #' . $data['id'];
    
    $query = "DELETE FROM events WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        if (isset($_SESSION['user_id'])) {
            logEventActivity($_SESSION['user_id'], 'delete', 'Deleted event: ' . $eventTitle);
        }
        
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

