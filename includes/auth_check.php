<?php
require_once __DIR__ . '/database.php';

function getUserPermissions($userId) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT p.slug 
                  FROM permissions p 
                  INNER JOIN user_permissions up ON p.id = up.permission_id 
                  WHERE up.user_id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'slug');
    } catch (Exception $e) {
        return [];
    }
}

function hasPermission($permission) {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'adviser') {
        return true;
    }
    
    if (!isset($_SESSION['user_permissions'])) {
        $_SESSION['user_permissions'] = getUserPermissions($_SESSION['user_id']);
    }
    
    return in_array($permission, $_SESSION['user_permissions']);
}

function hasAnyPermission($permissions) {
    foreach ($permissions as $perm) {
        if (hasPermission($perm)) return true;
    }
    return false;
}

function requirePermission($permission) {
    if (!hasPermission($permission)) {
        header('HTTP/1.1 403 Forbidden');
        echo json_encode(['success' => false, 'error' => 'Access denied. You do not have permission to perform this action.']);
        exit;
    }
}

function isAdviser() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'adviser';
}

function isDean() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'dean';
}

function canManageUsers() {
    return isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['adviser', 'dean']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['adviser', 'dean', 'president']);
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUserRole() {
    return $_SESSION['user_role'] ?? null;
}

function isComelec() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'comelec';
}

function refreshUserPermissions() {
    if (isset($_SESSION['user_id'])) {
        $_SESSION['user_permissions'] = getUserPermissions($_SESSION['user_id']);
    }
}

