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

try {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Not authenticated']);
        exit;
    }

    refreshUserPermissions();
    
    echo json_encode([
        'success' => true,
        'message' => 'Permissions refreshed successfully',
        'permissions' => $_SESSION['user_permissions'] ?? []
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to refresh permissions: ' . $e->getMessage()]);
}

