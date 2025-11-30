<?php
header('Content-Type: application/json');
session_start();
require_once '../../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $data = json_decode(file_get_contents('php://input'), true);
    $userId = intval($data['id'] ?? 0);

    if ($userId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Valid user ID is required']);
        exit;
    }

    $checkQuery = "SELECT id, email, role, status FROM users WHERE id = :id";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bindValue(':id', $userId, PDO::PARAM_INT);
    $checkStmt->execute();
    $user = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit;
    }

    if ($user['status'] === 'active') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'User is already active']);
        exit;
    }

    $conn->beginTransaction();

    $updateQuery = "UPDATE users SET status = 'active' WHERE id = :id";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindValue(':id', $userId, PDO::PARAM_INT);
    $updateStmt->execute();

    if (isset($_SESSION['user_id'])) {
        require_once '../../includes/activity_logger.php';
        logUserActivity($_SESSION['user_id'], 'reactivate', 'Reactivated user: ' . $user['email'] . ' (Previous status: ' . $user['status'] . ')');
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'User reactivated successfully'
    ]);

} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to reactivate user: ' . $e->getMessage()]);
}

