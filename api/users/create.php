<?php
header('Content-Type: application/json');
session_start();
require_once '../../includes/database.php';
require_once '../../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!hasPermission('create_accounts') && !hasPermission('manage_users')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Access denied. You do not have permission to create user accounts.']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $data = json_decode(file_get_contents('php://input'), true);

    $firstName = trim($data['first_name'] ?? '');
    $lastName = trim($data['last_name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $role = $data['role'] ?? 'officer';
    $permissions = $data['permissions'] ?? [];

    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'First name, last name, email, and password are required']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid email format']);
        exit;
    }

    if (strlen($password) < 8) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Password must be at least 8 characters']);
        exit;
    }

    $validRoles = ['adviser', 'dean', 'president', 'treasurer', 'auditor', 'secretary', 'comelec', 'event_coordinator', 'officer'];
    if (!in_array($role, $validRoles)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid role specified']);
        exit;
    }

    $checkQuery = "SELECT id FROM users WHERE email = :email";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bindValue(':email', $email);
    $checkStmt->execute();

    if ($checkStmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'error' => 'Email already exists']);
        exit;
    }

    $conn->beginTransaction();

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $createdBy = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    $insertQuery = "INSERT INTO users (first_name, last_name, email, password, role, status, created_by) 
                    VALUES (:first_name, :last_name, :email, :password, :role, 'active', :created_by)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bindValue(':first_name', $firstName);
    $insertStmt->bindValue(':last_name', $lastName);
    $insertStmt->bindValue(':email', $email);
    $insertStmt->bindValue(':password', $hashedPassword);
    $insertStmt->bindValue(':role', $role);
    $insertStmt->bindValue(':created_by', $createdBy);
    $insertStmt->execute();

    $userId = $conn->lastInsertId();

    if (!empty($permissions)) {
        $permInsertQuery = "INSERT INTO user_permissions (user_id, permission_id, granted_by) VALUES (:user_id, :perm_id, :granted_by)";
        $permInsertStmt = $conn->prepare($permInsertQuery);

        foreach ($permissions as $permId) {
            $permInsertStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $permInsertStmt->bindValue(':perm_id', $permId, PDO::PARAM_INT);
            $permInsertStmt->bindValue(':granted_by', $createdBy);
            $permInsertStmt->execute();
        }
    } else {
        $defaultPermsQuery = "INSERT INTO user_permissions (user_id, permission_id, granted_by)
                              SELECT :user_id, permission_id, :granted_by 
                              FROM role_default_permissions 
                              WHERE role = :role";
        $defaultPermsStmt = $conn->prepare($defaultPermsQuery);
        $defaultPermsStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $defaultPermsStmt->bindValue(':granted_by', $createdBy);
        $defaultPermsStmt->bindValue(':role', $role);
        $defaultPermsStmt->execute();
    }

    if ($createdBy) {
        require_once '../../includes/activity_logger.php';
        logUserActivity($createdBy, 'create', 'Created user account: ' . $email . ' with role: ' . $role);
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'User created successfully',
        'data' => ['id' => $userId, 'email' => $email, 'role' => $role]
    ]);

} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to create user: ' . $e->getMessage()]);
}

