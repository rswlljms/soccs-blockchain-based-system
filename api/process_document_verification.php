<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/document_verification_service.php';
require_once '../includes/email_config.php';
require_once '../includes/database.php';

$response = ['success' => false];

try {
    $svc = new DocumentVerificationService();
    $job = $svc->claimNextJob();
    if (!$job) {
        echo json_encode(['success' => true, 'message' => 'No jobs ready']);
        exit;
    }

    $studentId = $job['student_id'];
    $result = $svc->runVerification($studentId);
    $svc->completeJob($job['id'], $studentId, $result);

    // Auto decision: approve if valid, otherwise reject
    $db = new Database();
    $conn = $db->getConnection();
    $getStmt = $conn->prepare("SELECT * FROM student_registrations WHERE id = ?");
    $getStmt->execute([$studentId]);
    $student = $getStmt->fetch(PDO::FETCH_ASSOC);

    $emailService = new EmailService();

    if (($result['overall_result'] ?? '') === 'valid') {
        // Approve
        $conn->beginTransaction();
        try {
            $upd = $conn->prepare("UPDATE student_registrations SET approval_status='approved', approved_at = NOW(), approved_by = 'System' WHERE id = ?");
            $upd->execute([$studentId]);

            $ins = $conn->prepare("INSERT INTO students (id, first_name, middle_name, last_name, email, password, course, year_level, section, age, gender, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
            $ins->execute([
                $student['id'], $student['first_name'], $student['middle_name'], $student['last_name'], $student['email'], $student['password'], $student['course'], $student['year_level'], $student['section'], $student['age'], $student['gender']
            ]);

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }

        $emailService->sendApprovalNotification($student['email'], $student['first_name'], $student['id']);
        $response = ['success' => true, 'message' => 'Verified and approved', 'result' => $result];
    } else {
        // Reject
        $reason = $result['reason'] ?? 'Document verification failed';
        $upd = $conn->prepare("UPDATE student_registrations SET approval_status='rejected', rejected_at = NOW(), rejection_reason = ? WHERE id = ?");
        $upd->execute([$reason, $studentId]);
        $emailService->sendRejectionNotification($student['email'], $student['first_name'], $student['id'], $reason);
        $response = ['success' => true, 'message' => 'Verification failed and rejected', 'result' => $result];
    }

} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
    error_log('process_document_verification error: ' . $e->getMessage());
}

echo json_encode($response);
?>


