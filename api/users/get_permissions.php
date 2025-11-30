<?php
header('Content-Type: application/json');
require_once '../../includes/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();

    $query = "SELECT id, name, slug, description, module FROM permissions ORDER BY module, name";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $grouped = [];
    foreach ($permissions as $perm) {
        $module = ucfirst($perm['module']);
        if (!isset($grouped[$module])) {
            $grouped[$module] = [];
        }
        $grouped[$module][] = $perm;
    }

    echo json_encode([
        'success' => true,
        'data' => $permissions,
        'grouped' => $grouped
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch permissions: ' . $e->getMessage()
    ]);
}

