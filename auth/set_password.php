<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once '../includes/database.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $token = $input['token'] ?? '';
    $password = $input['password'] ?? '';
    $strong = preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password) && preg_match('/\d/', $password) && preg_match('/[^A-Za-z0-9]/', $password) && strlen($password) >= 8;
    if (!$token || !$strong) {
        $response['message'] = 'Invalid token or weak password (must include uppercase, lowercase, number, and symbol; min 8 chars)';
        echo json_encode($response); exit;
    }

    $db = new Database();
    $pdo = $db->getConnection();

    $pdo->beginTransaction();
    $stmt = $pdo->prepare("SELECT id, email, set_password_expires_at FROM student_registrations WHERE set_password_token = ? FOR UPDATE");
    $stmt->execute([$token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) { throw new Exception('Invalid or used link'); }
    if (new DateTime() > new DateTime($row['set_password_expires_at'])) { throw new Exception('Link expired'); }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Update password in both tables
    $upd = $pdo->prepare("UPDATE student_registrations SET password = ?, set_password_token = NULL, set_password_expires_at = NULL WHERE set_password_token = ?");
    $upd->execute([$hashed, $token]);

    // Also update in students table (since auto-approved)
    $updStudent = $pdo->prepare("UPDATE students SET password = ? WHERE id = ?");
    $updStudent->execute([$hashed, $row['id']]);

    $pdo->commit();

    $response = ['status' => 'success', 'redirect' => '/soccs-financial-management/templates/login.php'];
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) { $pdo->rollBack(); }
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>


