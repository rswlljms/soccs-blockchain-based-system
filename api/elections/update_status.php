<?php
header('Content-Type: application/json');
require_once '../../includes/database.php';

$database = new Database();
$conn = $database->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !isset($data['status'])) {
    echo json_encode([
        'success' => false,
        'error' => 'ID and status are required'
    ]);
    exit;
}

$id = (int)$data['id'];
$status = $data['status'];

$validStatuses = ['upcoming', 'active', 'completed', 'cancelled'];
if (!in_array($status, $validStatuses)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid status value'
    ]);
    exit;
}

try {
    $query = "UPDATE elections SET status = :status WHERE id = :id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':status', $status);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Election status updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to update election status'
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

