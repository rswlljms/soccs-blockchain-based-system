<?php
session_start();
header('Content-Type: application/json');
require_once '../../includes/database.php';
require_once '../../includes/activity_logger.php';
require_once '../../includes/auth_check.php';

if (!hasPermission('manage_election_status') && !isAdviser()) {
    echo json_encode([
        'success' => false,
        'error' => 'Access denied. You do not have permission to delete filing periods.'
    ]);
    exit;
}

$database = new Database();
$conn = $database->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'ID is required'
    ]);
    exit;
}

$id = (int)$data['id'];

try {
    $query = "DELETE FROM filing_candidacy_periods WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        if (isset($_SESSION['user_id'])) {
            logElectionActivity($_SESSION['user_id'], 'delete', 'Deleted filing candidacy period (ID: ' . $id . ')');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Filing candidacy period deleted successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to delete filing candidacy period'
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

