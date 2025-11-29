<?php
/**
 * Email Testing Script
 * Test email functionality before using in production
 */

require_once 'includes/email_config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Email Test - SOCCS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success { color: #4CAF50; padding: 15px; background: #e8f5e9; border-radius: 5px; margin: 15px 0; }
        .error { color: #f44336; padding: 15px; background: #ffebee; border-radius: 5px; margin: 15px 0; }
        .info { color: #2196F3; padding: 15px; background: #e3f2fd; border-radius: 5px; margin: 15px 0; }
        input, button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            background: #B366FF;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background: #9933ff;
        }
        .config-info {
            background: #fff3cd;
            padding: 15px;
            border-left: 4px solid #ffc107;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>📧 Email Configuration Test</h1>
        <p>Test the email sending functionality for SOCCS registration system.</p>
        
        <div class='config-info'>
            <strong>⚙️ Email Configuration:</strong><br>
            From: lspuscc.soccs@gmail.com<br>
            System: PHP mail() function
        </div>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $testEmail = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    
    if (!$testEmail) {
        echo "<div class='error'>❌ Invalid email address!</div>";
    } else {
        $emailService = new EmailService();
        
        echo "<div class='info'>📤 Sending test email to: <strong>{$testEmail}</strong></div>";
        
        $result = $emailService->sendRegistrationConfirmation(
            $testEmail,
            'Test User',
            'TEST2024001'
        );
        
        if ($result) {
            echo "<div class='success'>
                ✅ <strong>Email sent successfully!</strong><br><br>
                Please check your inbox (and spam folder) for the email.<br><br>
                <strong>Email Details:</strong><br>
                To: {$testEmail}<br>
                Subject: SOCCS Registration - Confirmation<br>
                Type: HTML Email with Registration Details
            </div>";
        } else {
            echo "<div class='error'>
                ❌ <strong>Failed to send email!</strong><br><br>
                <strong>Troubleshooting Steps:</strong><br>
                1. Check XAMPP sendmail configuration (C:\\xampp\\sendmail\\sendmail.ini)<br>
                2. Verify SMTP credentials are correct<br>
                3. Check error logs (C:\\xampp\\sendmail\\error.log)<br>
                4. Ensure internet connection is active<br>
                5. Review docs/EMAIL_SETUP_GUIDE.md for detailed setup
            </div>";
        }
    }
}

echo "
        <form method='POST'>
            <h3>Enter Test Email Address:</h3>
            <input type='email' name='email' placeholder='your-email@gmail.com' required>
            <p style='font-size: 14px; color: #666;'>💡 Enter your Gmail address to receive the test email</p>
            <button type='submit'>📨 Send Test Email</button>
        </form>
        
        <div class='info' style='margin-top: 30px;'>
            <strong>📚 Setup Resources:</strong><br>
            • <a href='docs/EMAIL_SETUP_GUIDE.md' target='_blank'>Email Setup Guide</a><br>
            • <a href='docs/REGISTRATION_SETUP.md' target='_blank'>Registration Setup Guide</a><br>
            • <a href='pages/student-registration.php'>Student Registration Form</a>
        </div>
        
        <div class='config-info' style='margin-top: 20px;'>
            <strong>⚠️ Important Notes:</strong><br>
            • For XAMPP local testing, configure sendmail.ini with Gmail App Password<br>
            • Check spam folder if email doesn't arrive<br>
            • For production, consider using PHPMailer or SMTP service<br>
            • See EMAIL_SETUP_GUIDE.md for detailed instructions
        </div>
    </div>
</body>
</html>";
?>

