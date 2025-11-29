<?php
session_start();
require_once 'includes/db.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // DEBUGGING: compare passwords
    if (password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['email'];
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
