<?php
header('Content-Type: application/json');
require_once '../../includes/database.php';
require_once '../../includes/email_config.php';

$database = new Database();
$conn = $database->getConnection();

try {
    $checkQuery = "SELECT id, title, description, start_date, end_date FROM elections 
                    WHERE status = 'upcoming' 
                    AND NOW() >= start_date 
                    AND NOW() <= end_date";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->execute();
    $newlyActivated = $checkStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($newlyActivated)) {
        $updateQuery = "UPDATE elections 
                        SET status = 'active' 
                        WHERE status = 'upcoming' 
                        AND NOW() >= start_date 
                        AND NOW() <= end_date";
        $conn->exec($updateQuery);
        
        ignore_user_abort(true);
        set_time_limit(300);
        
        foreach ($newlyActivated as $election) {
            try {
                $emailService = new EmailService();
                $students = $emailService->getActiveStudentEmails();
                if (!empty($students) && $election['start_date'] && $election['end_date']) {
                    $emailData = $emailService->getElectionNotificationContent(
                        $election['title'],
                        $election['description'],
                        $election['start_date'],
                        $election['end_date']
                    );
                    $result = $emailService->sendBulkEmail($students, $emailData['subject'], $emailData['content']);
                    error_log("Auto-activated election notification emails sent for '{$election['title']}': " . json_encode($result));
                }
            } catch (Exception $e) {
                error_log("Failed to send auto-activated election notification emails for '{$election['title']}': " . $e->getMessage());
            }
        }
    }
    
    $query = "SELECT * FROM elections ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $elections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $elections
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

