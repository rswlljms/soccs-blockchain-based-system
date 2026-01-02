<?php
session_start();
header('Content-Type: application/json');
require_once '../../includes/database.php';
require_once '../../includes/activity_logger.php';
require_once '../../includes/auth_check.php';

if (!hasPermission('manage_election_status')) {
    echo json_encode([
        'success' => false,
        'error' => 'Access denied. You do not have permission to update elections.'
    ]);
    exit;
}

$database = new Database();
$conn = $database->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !isset($data['title']) || !isset($data['start_date']) || !isset($data['end_date'])) {
    echo json_encode([
        'success' => false,
        'error' => 'ID, title, start date, and end date are required'
    ]);
    exit;
}

$id = (int)$data['id'];
$title = trim($data['title']);
$description = isset($data['description']) ? trim($data['description']) : null;
$start_date = $data['start_date'];
$end_date = $data['end_date'];

if (empty($title)) {
    echo json_encode([
        'success' => false,
        'error' => 'Title cannot be empty'
    ]);
    exit;
}

if (empty($start_date) || empty($end_date)) {
    echo json_encode([
        'success' => false,
        'error' => 'Please fill in both start date and end date.'
    ]);
    exit;
}

$current_time = time();
$start_timestamp = strtotime($start_date);
$end_timestamp = strtotime($end_date);

if ($start_timestamp === false || $end_timestamp === false) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid date format. Please enter valid dates.'
    ]);
    exit;
}

if ($start_timestamp < $current_time) {
    echo json_encode([
        'success' => false,
        'error' => 'The election start date cannot be in the past. Please select a current or future date.'
    ]);
    exit;
}

if ($end_timestamp <= $start_timestamp) {
    echo json_encode([
        'success' => false,
        'error' => 'End date must be after start date'
    ]);
    exit;
}

try {
    $query = "UPDATE elections 
              SET title = :title, 
                  description = :description, 
                  start_date = :start_date, 
                  end_date = :end_date 
              WHERE id = :id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    
    if ($stmt->execute()) {
        if (isset($_SESSION['user_id'])) {
            logElectionActivity($_SESSION['user_id'], 'update', 'Updated election: ' . $title . ' (ID: ' . $id . ')');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Election updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to update election'
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

