<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
require_once '../../includes/database.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/activity_logger.php';
require_once '../../includes/email_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!hasPermission('manage_events')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Access denied. You do not have permission to manage events.']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data');
    }
    
    $requiredFields = ['name', 'date', 'location', 'description', 'category', 'time', 'status'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            throw new Exception("Missing required field: $field");
        }
    }
    
    $database = new Database();
    $conn = $database->getConnection();
    
    $datetime = $data['date'] . ' ' . $data['time'];
    $isMultiDay = isset($data['is_multi_day']) ? (bool)$data['is_multi_day'] : false;
    $endDatetime = null;
    
    if ($isMultiDay && isset($data['end_date']) && !empty($data['end_date'])) {
        $endTime = isset($data['end_time']) ? $data['end_time'] : $data['time'];
        $endDatetime = $data['end_date'] . ' ' . $endTime;
    }
    
    $query = "INSERT INTO events (title, description, date, end_date, is_multi_day, location, category, status, created_by, created_at) 
              VALUES (:title, :description, :date, :end_date, :is_multi_day, :location, :category, :status, :created_by, NOW())";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':title', $data['name'], PDO::PARAM_STR);
    $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
    $stmt->bindParam(':date', $datetime, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $endDatetime, PDO::PARAM_STR);
    $stmt->bindParam(':is_multi_day', $isMultiDay, PDO::PARAM_BOOL);
    $stmt->bindParam(':location', $data['location'], PDO::PARAM_STR);
    $stmt->bindParam(':category', $data['category'], PDO::PARAM_STR);
    $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);
    
    $createdBy = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 'admin';
    $stmt->bindParam(':created_by', $createdBy, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        $eventId = $conn->lastInsertId();
        
        // Insert contests if provided
        if (isset($data['contests']) && is_array($data['contests']) && count($data['contests']) > 0) {
            $contestQuery = "INSERT INTO event_contests (event_id, contest_details, registration_link) VALUES (:event_id, :contest_details, :registration_link)";
            $contestStmt = $conn->prepare($contestQuery);
            
            foreach ($data['contests'] as $contest) {
                if (!empty($contest['contest_details']) && !empty($contest['registration_link'])) {
                    $contestStmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
                    $contestStmt->bindParam(':contest_details', $contest['contest_details'], PDO::PARAM_STR);
                    $contestStmt->bindParam(':registration_link', $contest['registration_link'], PDO::PARAM_STR);
                    $contestStmt->execute();
                }
            }
        }
        
        if (isset($_SESSION['user_id'])) {
            logEventActivity($_SESSION['user_id'], 'create', 'Created event: ' . $data['name'] . ' (' . $data['category'] . ')');
        }
        
        $emailService = new EmailService();
        $students = $emailService->getActiveStudentEmails();
        $emailData = null;
        if (!empty($students)) {
            $emailData = $emailService->getEventNotificationContent(
                $data['name'],
                $datetime,
                $data['location'],
                $data['description'],
                $data['category']
            );
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Event created successfully',
            'event_id' => (int)$eventId
        ]);
        
        if (ob_get_level()) {
            ob_end_flush();
        }
        flush();
        
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        
        ignore_user_abort(true);
        set_time_limit(300);
        
        if (!empty($students) && $emailData) {
            try {
                $result = $emailService->sendBulkEmail($students, $emailData['subject'], $emailData['content']);
                error_log("Event notification emails sent: " . json_encode($result));
            } catch (Exception $e) {
                error_log("Failed to send event notification emails: " . $e->getMessage());
            }
        }
    } else {
        throw new Exception('Failed to create event');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

