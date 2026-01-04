<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
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
    
    $query = "SELECT * FROM elections 
              WHERE status IN ('active', 'upcoming')
              ORDER BY 
                CASE 
                  WHEN status = 'active' THEN 1 
                  WHEN status = 'upcoming' THEN 2 
                END,
                start_date DESC 
              LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $election = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($election) {
        $statsQuery = "SELECT 
            (SELECT COUNT(*) FROM positions) as total_positions,
            (SELECT COUNT(*) FROM candidates) as total_candidates,
            (SELECT COUNT(*) FROM students WHERE is_active = 1) as eligible_voters";
        
        $statsStmt = $conn->prepare($statsQuery);
        $statsStmt->execute();
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
        
        $election['stats'] = $stats;
        
        echo json_encode([
            'success' => true,
            'data' => $election
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'No active election found'
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

