<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/database.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (isset($user['status']) && $user['status'] !== 'active') {
        echo json_encode(['status' => 'error', 'message' => 'Your account is inactive. Please contact an administrator.']);
        exit;
    }

    if (password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['email'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'] ?? 'officer';
        $_SESSION['user_name'] = ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '');
        
        // Load user permissions into session
        try {
            $database = new Database();
            $pdo = $database->getConnection();
            $permQuery = "SELECT p.slug FROM permissions p 
                          INNER JOIN user_permissions up ON p.id = up.permission_id 
                          WHERE up.user_id = ?";
            $permStmt = $pdo->prepare($permQuery);
            $permStmt->execute([$user['id']]);
            $_SESSION['user_permissions'] = $permStmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            $_SESSION['user_permissions'] = [];
        }
        
        $updateSql = "UPDATE users SET last_login = NOW() WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("i", $user['id']);
        $updateStmt->execute();
        
        require_once 'includes/activity_logger.php';
        logAuthActivity($user['id'], 'login');
        
        echo json_encode(['status' => 'success']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Incorrect password.']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Email not found.']);
    exit;
}
?>
