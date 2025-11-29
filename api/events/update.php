<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
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

