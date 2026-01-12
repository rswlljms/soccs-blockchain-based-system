<?php
session_start();
require_once 'includes/database.php';
require_once 'includes/activity_logger.php';

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    logAuthActivity($userId, 'logout');
}

session_unset();
session_destroy();

header('Location: templates/login.php');
exit;
?>





