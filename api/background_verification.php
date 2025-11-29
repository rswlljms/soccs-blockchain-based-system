<?php
// Background document verification processor
// This can be called via cron job or manually to process pending verifications

require_once '../includes/database.php';
require_once '../includes/document_verification_service.php';

// Set longer execution time for background processing
set_time_limit(300); // 5 minutes
ini_set('memory_limit', '256M');

try {
    $verifier = new DocumentVerificationService();
    
    // Process one job at a time
    $job = $verifier->claimNextJob();
    
    if ($job) {
        echo "Processing verification for student: " . $job['student_id'] . "\n";
        
        $result = $verifier->runVerification($job['student_id']);
        
        echo "Verification result: " . $result['overall_result'] . "\n";
        echo "Reason: " . $result['reason'] . "\n";
        
        // Update registration status based on result
        $database = new Database();
        $conn = $database->getConnection();
        
        if ($result['overall_result'] === 'valid') {
            // Keep as approved (already approved during registration)
            echo "Student remains approved\n";
        } else {
            // Reject the registration
            $rejectQuery = "UPDATE student_registrations SET approval_status='rejected', rejected_at=NOW(), rejection_reason=? WHERE id=?";
            $rejectStmt = $conn->prepare($rejectQuery);
            $rejectStmt->execute([$result['reason'], $job['student_id']]);
            
            // Remove from students table
            $deleteQuery = "DELETE FROM students WHERE id=?";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->execute([$job['student_id']]);
            
            echo "Student registration rejected and removed\n";
        }
        
    } else {
        echo "No pending verification jobs\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    error_log("Background verification error: " . $e->getMessage());
}
?>
