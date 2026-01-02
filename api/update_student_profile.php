<?php
session_start();
header('Content-Type: application/json');

require_once '../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['student'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $studentId = $_SESSION['student']['id'];
    
    $firstName = trim($_POST['firstName'] ?? '');
    $middleName = trim($_POST['middleName'] ?? '') ?: null;
    $lastName = trim($_POST['lastName'] ?? '');
    $dateOfBirth = trim($_POST['dateOfBirth'] ?? '');
    $phoneNumber = trim($_POST['phoneNumber'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    
    if (empty($firstName) || empty($lastName) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'First name, last name, and email are required']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }
    
    $checkEmail = "SELECT id FROM students WHERE email = ? AND id != ?";
    $checkStmt = $conn->prepare($checkEmail);
    $checkStmt->execute([$email, $studentId]);
    if ($checkStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email already in use by another student']);
        exit;
    }
    
    $query = "UPDATE students SET 
              first_name = ?,
              middle_name = ?,
              last_name = ?,
              email = ?,
              date_of_birth = ?,
              phone_number = ?,
              address = ?,
              updated_at = NOW()
              WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([
        $firstName,
        $middleName,
        $lastName,
        $email,
        $dateOfBirth ?: null,
        $phoneNumber ?: null,
        $address ?: null,
        $studentId
    ]);
    
    $_SESSION['student']['firstName'] = $firstName;
    $_SESSION['student']['middleName'] = $middleName;
    $_SESSION['student']['lastName'] = $lastName;
    $_SESSION['student']['email'] = $email;
    
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully',
        'data' => [
            'firstName' => $firstName,
            'middleName' => $middleName,
            'lastName' => $lastName,
            'email' => $email,
            'dateOfBirth' => $dateOfBirth,
            'phoneNumber' => $phoneNumber,
            'address' => $address
        ]
    ]);
    
} catch (Exception $e) {
    error_log('Student profile update error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
}
?>
