<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();

    $id = trim($_GET['id'] ?? '');
    
    if (empty($id) || $id === 'undefined' || $id === 'null') {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Student ID is required'
        ]);
        exit;
    }

    if (strlen($id) > 20) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Student ID is too long'
        ]);
        exit;
    }

    $sql = "SELECT 
                id,
                first_name,
                middle_name,
                last_name,
                email,
                course,
                year_level,
                section,
                gender,
                membership_fee_status,
                membership_fee_receipt,
                membership_fee_paid_at,
                is_archived,
                created_at,
                updated_at
            FROM students
            WHERE id = ?";

    $stmt = $pdo->prepare($sql);
    if (!$stmt) {
        throw new Exception('Database query preparation failed');
    }
    
    $stmt->execute([$id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Student not found'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'data' => $student
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    error_log('Database error in get_student_profile.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    error_log('Error in get_student_profile.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}


