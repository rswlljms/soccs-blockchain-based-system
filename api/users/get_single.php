<?php
header('Content-Type: application/json');
require_once '../../includes/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();

    $userId = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($userId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Valid user ID is required']);
        exit;
    }

    $query = "SELECT 
                u.id,
                u.first_name,
                u.last_name,
                u.email,
                u.role,
                u.status,
                u.last_login,
                u.created_at,
                u.updated_at
              FROM users u
              WHERE u.id = :id";

    $stmt = $conn->prepare($query);
    $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit;
    }

    $permQuery = "SELECT p.id, p.name, p.slug, p.module 
                  FROM permissions p 
                  INNER JOIN user_permissions up ON p.id = up.permission_id 
                  WHERE up.user_id = :user_id";
    $permStmt = $conn->prepare($permQuery);
    $permStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $permStmt->execute();
    $user['permissions'] = $permStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $user
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch user: ' . $e->getMessage()
    ]);
}

