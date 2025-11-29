<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/database.php';

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT * FROM student_registrations ORDER BY created_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stats = [
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0
        ];
        
        foreach ($registrations as $reg) {
            $stats[$reg['approval_status']]++;
        }
        
        $response = [
            'success' => true,
            'data' => $registrations,
            'stats' => $stats
        ];
        
    } catch (Exception $e) {
        $response['message'] = 'Failed to load registrations: ' . $e->getMessage();
        error_log('Get pending students error: ' . $e->getMessage());
    }
}

echo json_encode($response);
?>