<?php
header('Content-Type: application/json');
session_start();
require_once '../../includes/database.php';
require_once '../../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!hasPermission('manage_users') && !hasPermission('demote_accounts')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Access denied. You do not have permission to update users.']);
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

    $checkQuery = "SELECT id, role, email FROM users WHERE id = :id";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bindValue(':id', $userId, PDO::PARAM_INT);
    $checkStmt->execute();
    $existingUser = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$existingUser) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit;
    }

    $conn->beginTransaction();

    $updateFields = [];
    $updateParams = [':id' => $userId];
    $changes = [];

    if (isset($data['first_name']) && !empty(trim($data['first_name']))) {
        $updateFields[] = "first_name = :first_name";
        $updateParams[':first_name'] = trim($data['first_name']);
        $changes['first_name'] = trim($data['first_name']);
    }

    if (isset($data['last_name']) && !empty(trim($data['last_name']))) {
        $updateFields[] = "last_name = :last_name";
        $updateParams[':last_name'] = trim($data['last_name']);
        $changes['last_name'] = trim($data['last_name']);
    }

    if (isset($data['email']) && !empty(trim($data['email']))) {
        $newEmail = trim($data['email']);
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $conn->rollBack();
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid email format']);
            exit;
        }

        $emailCheck = "SELECT id FROM users WHERE email = :email AND id != :check_id";
        $emailStmt = $conn->prepare($emailCheck);
        $emailStmt->bindValue(':email', $newEmail);
        $emailStmt->bindValue(':check_id', $userId, PDO::PARAM_INT);
        $emailStmt->execute();

        if ($emailStmt->fetch()) {
            $conn->rollBack();
            http_response_code(409);
            echo json_encode(['success' => false, 'error' => 'Email already in use by another user']);
            exit;
        }

        $updateFields[] = "email = :email";
        $updateParams[':email'] = $newEmail;
        $changes['email'] = $newEmail;
    }

    if (isset($data['role'])) {
        if (!hasPermission('manage_users') && !hasPermission('demote_accounts')) {
            $conn->rollBack();
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Access denied. You do not have permission to change user roles.']);
            exit;
        }
        
        $validRoles = ['adviser', 'dean', 'president', 'treasurer', 'auditor', 'secretary', 'comelec', 'event_coordinator', 'officer'];
        if (!in_array($data['role'], $validRoles)) {
            $conn->rollBack();
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid role specified']);
            exit;
        }
        $updateFields[] = "role = :role";
        $updateParams[':role'] = $data['role'];
        $changes['role'] = ['from' => $existingUser['role'], 'to' => $data['role']];
    }

    if (isset($data['status'])) {
        if (!hasPermission('manage_users')) {
            $conn->rollBack();
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Access denied. You do not have permission to change user status.']);
            exit;
        }
        
        $validStatuses = ['active', 'inactive', 'suspended'];
        if (!in_array($data['status'], $validStatuses)) {
            $conn->rollBack();
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid status specified']);
            exit;
        }
        $updateFields[] = "status = :status";
        $updateParams[':status'] = $data['status'];
        $changes['status'] = $data['status'];
    }

    if (isset($data['password']) && !empty($data['password'])) {
        if (strlen($data['password']) < 8) {
            $conn->rollBack();
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Password must be at least 8 characters']);
            exit;
        }
        $updateFields[] = "password = :password";
        $updateParams[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $changes['password'] = 'changed';
    }

    if (!empty($updateFields)) {
        $updateQuery = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = :id";
        $updateStmt = $conn->prepare($updateQuery);
        foreach ($updateParams as $key => $value) {
            $updateStmt->bindValue($key, $value);
        }
        $updateStmt->execute();
    }

    if (isset($data['permissions']) && is_array($data['permissions'])) {
        if (!hasPermission('manage_users')) {
            $conn->rollBack();
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Access denied. You do not have permission to manage user permissions.']);
            exit;
        }
        
        if ($existingUser['role'] === 'adviser' || $existingUser['role'] === 'dean') {
            $conn->rollBack();
            $roleName = $existingUser['role'] === 'adviser' ? 'SOCCS Adviser' : 'CCS Dean';
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => $roleName . ' permissions cannot be modified.']);
            exit;
        }
        
        $deletePermsQuery = "DELETE FROM user_permissions WHERE user_id = :user_id";
        $deletePermsStmt = $conn->prepare($deletePermsQuery);
        $deletePermsStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        
        if (!$deletePermsStmt->execute()) {
            $conn->rollBack();
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to delete existing permissions']);
            exit;
        }

        if (!empty($data['permissions'])) {
            $grantedBy = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            
            $validPermsQuery = "SELECT id FROM permissions WHERE id IN (" . implode(',', array_fill(0, count($data['permissions']), '?')) . ")";
            $validPermsStmt = $conn->prepare($validPermsQuery);
            $validPermsStmt->execute(array_map('intval', $data['permissions']));
            $validPermIds = $validPermsStmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (count($validPermIds) !== count($data['permissions'])) {
                $conn->rollBack();
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'One or more permission IDs are invalid']);
                exit;
            }
            
            $permInsertQuery = "INSERT INTO user_permissions (user_id, permission_id, granted_by) VALUES (:user_id, :perm_id, :granted_by)";
            $permInsertStmt = $conn->prepare($permInsertQuery);

            foreach ($data['permissions'] as $permId) {
                $permInsertStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
                $permInsertStmt->bindValue(':perm_id', intval($permId), PDO::PARAM_INT);
                $permInsertStmt->bindValue(':granted_by', $grantedBy, $grantedBy !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
                
                if (!$permInsertStmt->execute()) {
                    $conn->rollBack();
                    http_response_code(500);
                    $errorInfo = $permInsertStmt->errorInfo();
                    echo json_encode(['success' => false, 'error' => 'Failed to insert permission: ' . ($errorInfo[2] ?? 'Unknown error')]);
                    exit;
                }
            }
        }
        $changes['permissions'] = 'updated';
    }

    if (isset($_SESSION['user_id'])) {
        require_once '../../includes/activity_logger.php';
        $changeSummary = [];
        foreach ($changes as $field => $value) {
            $changeSummary[] = $field . ': ' . (is_array($value) ? json_encode($value) : $value);
        }
        $description = 'Updated user: ' . ($existingUser['email'] ?? 'ID ' . $userId) . ' - ' . implode(', ', $changeSummary);
        logUserActivity($_SESSION['user_id'], 'update', $description);
    }

    $conn->commit();

    $response = [
        'success' => true,
        'message' => 'User updated successfully',
        'changes' => $changes
    ];

    if (isset($changes['permissions']) && $changes['permissions'] === 'updated') {
        $updatedUserId = $userId;
        $isCurrentUser = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $updatedUserId;
        
        $permCheckQuery = "SELECT p.id, p.name, p.slug 
                          FROM permissions p 
                          INNER JOIN user_permissions up ON p.id = up.permission_id 
                          WHERE up.user_id = :user_id";
        $permCheckStmt = $conn->prepare($permCheckQuery);
        $permCheckStmt->bindValue(':user_id', $updatedUserId, PDO::PARAM_INT);
        $permCheckStmt->execute();
        $savedPermissions = $permCheckStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response['saved_permissions'] = $savedPermissions;
        $response['permission_count'] = count($savedPermissions);
        
        if ($isCurrentUser) {
            require_once '../../includes/auth_check.php';
            refreshUserPermissions();
            $response['message'] = 'Permission Updated';
            $response['permissions_refreshed'] = true;
        } else {
            $response['message'] = 'Permission Updated';
            $response['permissions_refreshed'] = false;
            $response['requires_logout'] = true;
        }
    }

    echo json_encode($response);

} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to update user: ' . $e->getMessage()]);
}

