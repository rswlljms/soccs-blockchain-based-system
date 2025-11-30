<?php
session_start();
header('Content-Type: application/json');
require_once '../../includes/database.php';
require_once '../../includes/activity_logger.php';

$database = new Database();
$conn = $database->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Election ID is required'
    ]);
    exit;
}

$id = (int)$data['id'];

try {
    $checkQuery = "SELECT status FROM elections WHERE id = :id";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bindParam(':id', $id);
    $checkStmt->execute();
    
    $election = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$election) {
        echo json_encode([
            'success' => false,
            'error' => 'Election not found'
        ]);
        exit;
    }
    
    if ($election['status'] === 'active') {
        echo json_encode([
            'success' => false,
            'error' => 'Cannot delete an active election. Please close it first.'
        ]);
        exit;
    }
    
    $electionTitle = $election['title'] ?? 'Election #' . $id;
    
    $query = "DELETE FROM elections WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        if (isset($_SESSION['user_id'])) {
            logElectionActivity($_SESSION['user_id'], 'delete', 'Deleted election: ' . $electionTitle . ' (ID: ' . $id . ')');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Election deleted successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to delete election'
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

