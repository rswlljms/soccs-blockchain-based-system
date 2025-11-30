<?php
header('Content-Type: application/json');
session_start();
require_once '../../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $data = json_decode(file_get_contents('php://input'), true);

    $userId = intval($data['id'] ?? 0);
    $hardDelete = isset($data['hard_delete']) && $data['hard_delete'] === true;

    if ($userId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Valid user ID is required']);
        exit;
    }

    $checkQuery = "SELECT id, email, role FROM users WHERE id = :id";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bindValue(':id', $userId, PDO::PARAM_INT);
    $checkStmt->execute();
    $user = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit;
    }

    if ($user['role'] === 'adviser') {
        $countQuery = "SELECT COUNT(*) as count FROM users WHERE role = 'adviser' AND status = 'active'";
        $countStmt = $conn->prepare($countQuery);
        $countStmt->execute();
        $superAdminCount = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];

        if ($superAdminCount <= 1) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Cannot delete the last adviser account']);
            exit;
        }
    }

    $conn->beginTransaction();

    if ($hardDelete) {
        $deletePermsQuery = "DELETE FROM user_permissions WHERE user_id = :user_id";
        $deletePermsStmt = $conn->prepare($deletePermsQuery);
        $deletePermsStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $deletePermsStmt->execute();

        $deleteQuery = "DELETE FROM users WHERE id = :id";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bindValue(':id', $userId, PDO::PARAM_INT);
        $deleteStmt->execute();

        $action = 'delete_user';
        $message = 'User permanently deleted';
    } else {
        $deactivateQuery = "UPDATE users SET status = 'inactive' WHERE id = :id";
        $deactivateStmt = $conn->prepare($deactivateQuery);
        $deactivateStmt->bindValue(':id', $userId, PDO::PARAM_INT);
        $deactivateStmt->execute();

        $action = 'deactivate_user';
        $message = 'User deactivated successfully';
    }

    if (isset($_SESSION['user_id'])) {
        require_once '../../includes/activity_logger.php';
        $actionType = $hardDelete ? 'delete' : 'deactivate';
        $description = ($hardDelete ? 'Deleted' : 'Deactivated') . ' user: ' . $user['email'] . ' (Role: ' . $user['role'] . ')';
        logUserActivity($_SESSION['user_id'], $actionType, $description);
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => $message
    ]);

} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to delete user: ' . $e->getMessage()]);
}

