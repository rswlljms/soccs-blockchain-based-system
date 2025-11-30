<?php
header('Content-Type: application/json');
require_once '../../includes/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();

    $showInactive = isset($_GET['show_inactive']) && $_GET['show_inactive'] === 'true';
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $role = isset($_GET['role']) ? trim($_GET['role']) : '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 10;
    $offset = ($page - 1) * $limit;

    $whereConditions = [];
    $params = [];

    if (!$showInactive) {
        $whereConditions[] = "u.status = 'active'";
    }

    if (!empty($search)) {
        $whereConditions[] = "(u.first_name LIKE :search OR u.last_name LIKE :search OR u.email LIKE :search)";
        $params[':search'] = "%$search%";
    }

    if (!empty($role) && $role !== 'all') {
        $whereConditions[] = "u.role = :role";
        $params[':role'] = $role;
    }

    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

    $countQuery = "SELECT COUNT(*) as total FROM users u $whereClause";
    $countStmt = $conn->prepare($countQuery);
    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRecords / $limit);

    $query = "SELECT 
                u.id,
                u.first_name,
                u.last_name,
                u.email,
                u.role,
                u.status,
                u.last_login,
                u.created_at,
                u.updated_at,
                creator.email as created_by_email
              FROM users u
              LEFT JOIN users creator ON u.created_by = creator.id
              $whereClause
              ORDER BY u.created_at DESC
              LIMIT :limit OFFSET :offset";

    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as &$user) {
        $permQuery = "SELECT p.id, p.name, p.slug, p.module 
                      FROM permissions p 
                      INNER JOIN user_permissions up ON p.id = up.permission_id 
                      WHERE up.user_id = :user_id";
        $permStmt = $conn->prepare($permQuery);
        $permStmt->bindValue(':user_id', $user['id'], PDO::PARAM_INT);
        $permStmt->execute();
        $user['permissions'] = $permStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode([
        'success' => true,
        'data' => $users,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $totalRecords,
            'per_page' => $limit
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch users: ' . $e->getMessage()
    ]);
}

