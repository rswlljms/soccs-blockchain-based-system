<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $filename = $_GET['filename'] ?? '';
    $studentId = $_GET['student_id'] ?? '';
    
    if (!empty($filename)) {
        $uploadDir = '../uploads/documents/';
        $filePath = $uploadDir . basename($filename);
        
        if (!file_exists($filePath)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Document not found']);
            exit;
        }
        
        $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        $mimeType = $mimeTypes[$fileExtension] ?? 'application/octet-stream';
        
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . basename($filename) . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: private, max-age=3600');
        
        readfile($filePath);
        exit;
    }
    
    if (!empty($studentId)) {
        header('Content-Type: application/json');
        
        try {
            $database = new Database();
            $conn = $database->getConnection();
            
            $query = "SELECT * FROM student_registrations WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$studentId]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$student) {
                echo json_encode(['success' => false, 'message' => 'Student registration not found']);
                exit;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $student
            ]);
            
        } catch (Exception $e) {
            error_log('View document error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to load student details: ' . $e->getMessage()]);
        }
        exit;
    }
    
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Either filename or student_id is required']);
    exit;
}

header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Invalid request method']);
?>