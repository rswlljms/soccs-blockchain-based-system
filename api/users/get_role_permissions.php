<?php
header('Content-Type: application/json');
require_once '../../includes/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();

    $role = isset($_GET['role']) ? trim($_GET['role']) : '';

    if (empty($role)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Role is required']);
        exit;
    }

    $query = "SELECT p.id, p.name, p.slug, p.description, p.module 
              FROM permissions p 
              INNER JOIN role_default_permissions rdp ON p.id = rdp.permission_id 
              WHERE rdp.role = :role
              ORDER BY p.module, p.name";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':role', $role);
    $stmt->execute();

    $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'role' => $role,
        'permissions' => $permissions,
        'count' => count($permissions)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch role permissions: ' . $e->getMessage()
    ]);
}

