<?php
session_start();
header('Content-Type: application/json');
require_once '../../includes/database.php';
require_once '../../includes/activity_logger.php';
require_once '../../includes/auth_check.php';

if (!hasPermission('manage_election_status') && !isAdviser()) {
    echo json_encode([
        'success' => false,
        'error' => 'Access denied. You do not have permission to toggle filing period status.'
    ]);
    exit;
}

$database = new Database();
$conn = $database->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !isset($data['is_active'])) {
    echo json_encode([
        'success' => false,
        'error' => 'ID and status are required'
    ]);
    exit;
}

$id = (int)$data['id'];
$is_active = (int)$data['is_active'];

try {
    if ($is_active) {
        $deactivateQuery = "UPDATE filing_candidacy_periods SET is_active = 0 WHERE id != :id";
        $deactivateStmt = $conn->prepare($deactivateQuery);
        $deactivateStmt->bindParam(':id', $id);
        $deactivateStmt->execute();
    }
    
    $query = "UPDATE filing_candidacy_periods SET is_active = :is_active WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':is_active', $is_active);
    
    if ($stmt->execute()) {
        if (isset($_SESSION['user_id'])) {
            $action = $is_active ? 'activated' : 'deactivated';
            logElectionActivity($_SESSION['user_id'], 'update', $action . ' filing candidacy period (ID: ' . $id . ')');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Filing candidacy period status updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to update filing candidacy period status'
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

