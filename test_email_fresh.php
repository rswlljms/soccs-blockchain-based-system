<?php
/**
 * Fresh Email Test - Bypasses any caching
 */

require_once 'includes/email_config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Fresh Email Test - SOCCS</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f0f0f0; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 15px 0; }
        input, button { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { background: #B366FF; color: white; border: none; cursor: pointer; font-weight: bold; }
        button:hover { background: #9933ff; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>📧 Fresh Email Test (No Cache)</h1>
        
        <div class='info'>
            <strong>This test:</strong> Sends a fresh email using the updated banner image<br>
            <strong>Banner URL:</strong> https://i.imgur.com/cRtxCnL.jpeg
        </div>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $testEmail = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    
    if (!$testEmail) {
        echo "<div class='error'>❌ Invalid email address!</div>";
    } else {
        echo "<div class='info'>📤 Sending fresh email to: <strong>{$testEmail}</strong></div>";
        
        // Create fresh email service instance
        $emailService = new EmailService();
        
        $result = $emailService->sendRegistrationConfirmation(
            $testEmail,
            'Test User',
            'TEST2024001'
        );
        
        if ($result) {
            echo "<div class='success'>
                ✅ <strong>Fresh email sent successfully!</strong><br><br>
                <strong>What to check:</strong><br>
                • Open your email inbox<br>
                • Look for 'SOCCS Registration - Confirmation'<br>
                • Check if banner image appears (purple banner with checkmark)<br>
                • If banner doesn't show, try opening email in different email client<br><br>
                <strong>Banner should show:</strong> Purple gradient with white checkmark icon
            </div>";
        } else {
            echo "<div class='error'>
                ❌ <strong>Failed to send fresh email!</strong><br><br>
                <strong>Possible issues:</strong><br>
                • Email configuration problem<br>
                • Network connectivity issue<br>
                • SMTP server not responding<br><br>
                <strong>Check:</strong> C:\\xampp\\sendmail\\error.log for details
            </div>";
        }
    }
}

echo "
        <form method='POST'>
            <h3>Enter Test Email Address:</h3>
            <input type='email' name='email' placeholder='your-email@gmail.com' required>
            <button type='submit'>📨 Send Fresh Test Email</button>
        </form>
        
        <div class='info' style='margin-top: 30px;'>
            <strong>🔍 Troubleshooting:</strong><br>
            • <a href='test_banner.php'>Test banner image loading</a><br>
            • <a href='test_email.php'>Original email test</a><br>
            • <a href='email_preview.html'>Preview email design</a><br>
            • Clear browser cache (Ctrl+F5)<br>
            • Restart Apache in XAMPP
        </div>
    </div>
</body>
</html>";
?>
