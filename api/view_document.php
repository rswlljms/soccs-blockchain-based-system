<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/database.php';

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $studentId = $_GET['student_id'] ?? '';
    
    if (empty($studentId)) {
        $response['message'] = 'Student ID is required';
        echo json_encode($response);
        exit;
    }
    
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT * FROM student_registrations WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$studentId]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$student) {
            $response['message'] = 'Student registration not found';
            echo json_encode($response);
            exit;
        }
        
        $response = [
            'success' => true,
            'data' => $student
        ];
        
    } catch (Exception $e) {
        $response['message'] = 'Failed to load student details: ' . $e->getMessage();
        error_log('View document error: ' . $e->getMessage());
    }
}

echo json_encode($response);
?>