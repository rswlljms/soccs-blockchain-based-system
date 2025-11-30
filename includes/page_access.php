<?php
// Page access control helper
// Include this at the top of protected pages after session_start()

function checkPageAccess($requiredPermissions) {
    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        header("Location: ../templates/login.php");
        exit;
    }
    
    // Adviser role has full access
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'adviser') {
        return true;
    }
    
    // Check if user has any of the required permissions
    if (!isset($_SESSION['user_permissions']) || empty($_SESSION['user_permissions'])) {
        header("Location: access-denied.php");
        exit;
    }
    
    // If requiredPermissions is a string, convert to array
    if (is_string($requiredPermissions)) {
        $requiredPermissions = [$requiredPermissions];
    }
    
    // Check if user has at least one of the required permissions
    foreach ($requiredPermissions as $permission) {
        if (in_array($permission, $_SESSION['user_permissions'])) {
            return true;
        }
    }
    
    // No permission found, redirect to access denied
    header("Location: access-denied.php");
    exit;
}

function hasPagePermission($permission) {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'adviser') {
        return true;
    }
    if (!isset($_SESSION['user_permissions'])) {
        return false;
    }
    return in_array($permission, $_SESSION['user_permissions']);
}

