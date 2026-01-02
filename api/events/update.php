<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
require_once '../../includes/database.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/activity_logger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!hasPermission('manage_events')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Access denied. You do not have permission to manage events.']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['id'])) {
        throw new Exception('Invalid data or missing event ID');
    }
    
    $database = new Database();
    $conn = $database->getConnection();
    
    $updateFields = [];
    $params = [':id' => (int)$data['id']];
    
    if (isset($data['name'])) {
        $updateFields[] = "title = :title";
        $params[':title'] = $data['name'];
    }
    
    if (isset($data['description'])) {
        $updateFields[] = "description = :description";
        $params[':description'] = $data['description'];
    }
    
    if (isset($data['date']) && isset($data['time'])) {
        $datetime = $data['date'] . ' ' . $data['time'];
        $updateFields[] = "date = :date";
        $params[':date'] = $datetime;
    }
    
    if (isset($data['is_multi_day'])) {
        $updateFields[] = "is_multi_day = :is_multi_day";
        $params[':is_multi_day'] = (bool)$data['is_multi_day'];
        
        if ($data['is_multi_day'] && isset($data['end_date']) && !empty($data['end_date'])) {
            $endTime = isset($data['end_time']) ? $data['end_time'] : (isset($data['time']) ? $data['time'] : '23:59');
            $endDatetime = $data['end_date'] . ' ' . $endTime;
            $updateFields[] = "end_date = :end_date";
            $params[':end_date'] = $endDatetime;
        } else {
            $updateFields[] = "end_date = NULL";
        }
    }
    
    if (isset($data['location'])) {
        $updateFields[] = "location = :location";
        $params[':location'] = $data['location'];
    }
    
    if (isset($data['category'])) {
        $updateFields[] = "category = :category";
        $params[':category'] = $data['category'];
    }
    
    if (isset($data['status'])) {
        $updateFields[] = "status = :status";
        $params[':status'] = $data['status'];
    }
    
    if (empty($updateFields)) {
        throw new Exception('No fields to update');
    }
    
    $updateFields[] = "updated_at = NOW()";
    
    $query = "UPDATE events SET " . implode(', ', $updateFields) . " WHERE id = :id";
    
    $stmt = $conn->prepare($query);
    
    if ($stmt->execute($params)) {
        if (isset($_SESSION['user_id'])) {
            $getEventQuery = "SELECT title FROM events WHERE id = :id";
            $getEventStmt = $conn->prepare($getEventQuery);
            $getEventStmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
            $getEventStmt->execute();
            $event = $getEventStmt->fetch(PDO::FETCH_ASSOC);
            $eventTitle = $event['title'] ?? 'Event #' . $data['id'];
            
            logEventActivity($_SESSION['user_id'], 'update', 'Updated event: ' . $eventTitle);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Event updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update event');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

