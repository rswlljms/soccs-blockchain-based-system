<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();

    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        throw new Exception('Student ID is required');
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
                age,
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
    $stmt->execute([$id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        throw new Exception('Student not found');
    }

    echo json_encode([
        'success' => true,
        'data' => $student
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}


