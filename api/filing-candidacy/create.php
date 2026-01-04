<?php
session_start();
header('Content-Type: application/json');
require_once '../../includes/database.php';
require_once '../../includes/activity_logger.php';
require_once '../../includes/auth_check.php';
require_once '../../includes/email_config.php';

if (!hasPermission('manage_election_status') && !isAdviser()) {
    echo json_encode([
        'success' => false,
        'error' => 'Access denied. You do not have permission to create filing periods.'
    ]);
    exit;
}

$database = new Database();
$conn = $database->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['title']) || !isset($data['announcement_text']) || !isset($data['form_link']) || 
    !isset($data['start_date']) || !isset($data['end_date'])) {
    echo json_encode([
        'success' => false,
        'error' => 'All required fields must be provided'
    ]);
    exit;
}

$title = trim($data['title']);
$announcement_text = trim($data['announcement_text']);
$form_link = trim($data['form_link']);
$start_date = $data['start_date'];
$end_date = $data['end_date'];
$screening_date = isset($data['screening_date']) ? trim($data['screening_date']) : null;
$is_active = isset($data['is_active']) ? (int)$data['is_active'] : 0;
$created_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (empty($title) || empty($announcement_text) || empty($form_link)) {
    echo json_encode([
        'success' => false,
        'error' => 'Title, announcement text, and form link cannot be empty'
    ]);
    exit;
}

if (empty($start_date) || empty($end_date)) {
    echo json_encode([
        'success' => false,
        'error' => 'Please fill in both start date and end date.'
    ]);
    exit;
}

$start_timestamp = strtotime($start_date);
$end_timestamp = strtotime($end_date);
$current_time = time();

if ($start_timestamp === false || $end_timestamp === false) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid date format. Please enter valid dates.'
    ]);
    exit;
}

if ($start_timestamp < $current_time) {
    echo json_encode([
        'success' => false,
        'error' => 'The filing period start date cannot be in the past. Please select today or a future date.'
    ]);
    exit;
}

if ($end_timestamp <= $start_timestamp) {
    echo json_encode([
        'success' => false,
        'error' => 'End date must be after start date'
    ]);
    exit;
}

if (!filter_var($form_link, FILTER_VALIDATE_URL)) {
    echo json_encode([
        'success' => false,
        'error' => 'Please provide a valid URL for the form link'
    ]);
    exit;
}

try {
    if ($is_active) {
        $deactivateQuery = "UPDATE filing_candidacy_periods SET is_active = 0";
        $conn->exec($deactivateQuery);
    }
    
    $query = "INSERT INTO filing_candidacy_periods 
              (title, announcement_text, form_link, start_date, end_date, screening_date, is_active, created_by) 
              VALUES (:title, :announcement_text, :form_link, :start_date, :end_date, :screening_date, :is_active, :created_by)";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':announcement_text', $announcement_text);
    $stmt->bindParam(':form_link', $form_link);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    $stmt->bindParam(':screening_date', $screening_date);
    $stmt->bindParam(':is_active', $is_active);
    $stmt->bindParam(':created_by', $created_by);
    
    if ($stmt->execute()) {
        $periodId = $conn->lastInsertId();
        
        if (isset($_SESSION['user_id'])) {
            logElectionActivity($_SESSION['user_id'], 'create', 'Created filing candidacy period: ' . $title . ' (ID: ' . $periodId . ')');
        }
        
        $emailService = null;
        $students = [];
        $emailData = null;
        if ($is_active) {
            $emailService = new EmailService();
            $students = $emailService->getActiveStudentEmails();
            if (!empty($students)) {
                $emailData = $emailService->getFilingCandidacyNotificationContent(
                    $title,
                    $announcement_text,
                    $form_link,
                    $start_date,
                    $end_date,
                    $screening_date
                );
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Filing candidacy period created successfully',
            'id' => $periodId
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
        
        if ($is_active && !empty($students) && $emailData) {
            try {
                $result = $emailService->sendBulkEmail($students, $emailData['subject'], $emailData['content']);
                error_log("Filing candidacy notification emails sent: " . json_encode($result));
            } catch (Exception $e) {
                error_log("Failed to send filing candidacy notification emails: " . $e->getMessage());
            }
        }
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to create filing candidacy period'
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

