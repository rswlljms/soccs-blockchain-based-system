<?php
require_once __DIR__ . '/database.php';

function logActivity($userId, $activityType, $activityDescription, $module = null) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "INSERT INTO activity_logs (user_id, activity_type, activity_description, module) 
                  VALUES (:user_id, :activity_type, :activity_description, :module)";
        
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':activity_type', $activityType, PDO::PARAM_STR);
        $stmt->bindValue(':activity_description', $activityDescription, PDO::PARAM_STR);
        $stmt->bindValue(':module', $module, PDO::PARAM_STR);
        
        $stmt->execute();
        return true;
    } catch (Exception $e) {
        error_log("Activity logging failed: " . $e->getMessage());
        return false;
    }
}

function logFundActivity($userId, $action, $description) {
    $activityTypes = [
        'create' => 'fund_created',
        'update' => 'fund_updated',
        'delete' => 'fund_deleted',
        'view' => 'fund_viewed'
    ];
    $activityType = $activityTypes[$action] ?? 'fund_action';
    return logActivity($userId, $activityType, $description, 'financial');
}

function logExpenseActivity($userId, $action, $description) {
    $activityTypes = [
        'create' => 'expense_created',
        'update' => 'expense_updated',
        'delete' => 'expense_deleted',
        'view' => 'expense_viewed'
    ];
    $activityType = $activityTypes[$action] ?? 'expense_action';
    return logActivity($userId, $activityType, $description, 'financial');
}

function logMembershipActivity($userId, $action, $description) {
    $activityTypes = [
        'update_status' => 'membership_status_updated',
        'upload_receipt' => 'membership_receipt_uploaded',
        'view' => 'membership_viewed'
    ];
    $activityType = $activityTypes[$action] ?? 'membership_action';
    return logActivity($userId, $activityType, $description, 'membership');
}

function logStudentActivity($userId, $action, $description) {
    $activityTypes = [
        'approve' => 'student_approved',
        'reject' => 'student_rejected',
        'archive' => 'student_archived',
        'restore' => 'student_restored',
        'update' => 'student_updated',
        'view' => 'student_viewed'
    ];
    $activityType = $activityTypes[$action] ?? 'student_action';
    return logActivity($userId, $activityType, $description, 'students');
}

function logEventActivity($userId, $action, $description) {
    $activityTypes = [
        'create' => 'event_created',
        'update' => 'event_updated',
        'delete' => 'event_deleted',
        'archive' => 'event_archived',
        'view' => 'event_viewed'
    ];
    $activityType = $activityTypes[$action] ?? 'event_action';
    return logActivity($userId, $activityType, $description, 'events');
}

function logElectionActivity($userId, $action, $description) {
    $activityTypes = [
        'create' => 'election_created',
        'update' => 'election_updated',
        'delete' => 'election_deleted',
        'start' => 'election_started',
        'end' => 'election_ended',
        'view' => 'election_viewed'
    ];
    $activityType = $activityTypes[$action] ?? 'election_action';
    return logActivity($userId, $activityType, $description, 'elections');
}

function logCandidateActivity($userId, $action, $description) {
    $activityTypes = [
        'register' => 'candidate_registered',
        'update' => 'candidate_updated',
        'delete' => 'candidate_deleted',
        'approve' => 'candidate_approved',
        'reject' => 'candidate_rejected',
        'view' => 'candidate_viewed'
    ];
    $activityType = $activityTypes[$action] ?? 'candidate_action';
    return logActivity($userId, $activityType, $description, 'elections');
}

function logPositionActivity($userId, $action, $description) {
    $activityTypes = [
        'create' => 'position_created',
        'update' => 'position_updated',
        'delete' => 'position_deleted',
        'view' => 'position_viewed'
    ];
    $activityType = $activityTypes[$action] ?? 'position_action';
    return logActivity($userId, $activityType, $description, 'elections');
}

function logUserActivity($userId, $action, $description) {
    $activityTypes = [
        'create' => 'user_created',
        'update' => 'user_updated',
        'deactivate' => 'user_deactivated',
        'reactivate' => 'user_reactivated',
        'delete' => 'user_deleted',
        'permissions_updated' => 'user_permissions_updated',
        'view' => 'user_viewed'
    ];
    $activityType = $activityTypes[$action] ?? 'user_action';
    return logActivity($userId, $activityType, $description, 'users');
}

function logReportActivity($userId, $action, $description) {
    $activityTypes = [
        'generate_financial' => 'financial_report_generated',
        'generate_membership' => 'membership_report_generated',
        'generate_event' => 'event_report_generated',
        'generate_election' => 'election_report_generated',
        'export_pdf' => 'report_exported_pdf',
        'view' => 'report_viewed'
    ];
    $activityType = $activityTypes[$action] ?? 'report_action';
    return logActivity($userId, $activityType, $description, 'reports');
}

function logAuthActivity($userId, $action, $description = null) {
    $activityTypes = [
        'login' => 'user_login',
        'logout' => 'user_logout',
        'password_change' => 'password_changed',
        'password_reset' => 'password_reset'
    ];
    $activityType = $activityTypes[$action] ?? 'auth_action';
    $defaultDescription = [
        'login' => 'User logged into the system',
        'logout' => 'User logged out of the system',
        'password_change' => 'User changed password',
        'password_reset' => 'User password reset'
    ];
    $description = $description ?? ($defaultDescription[$action] ?? 'Authentication action');
    return logActivity($userId, $activityType, $description, 'authentication');
}

function logSettingsActivity($userId, $action, $description) {
    $activityTypes = [
        'update' => 'settings_updated',
        'view' => 'settings_viewed'
    ];
    $activityType = $activityTypes[$action] ?? 'settings_action';
    return logActivity($userId, $activityType, $description, 'settings');
}

