# Email Setup Guide for SOCCS Financial Management System

## Overview
This guide explains how to configure email functionality for student registration notifications.

## Current Implementation
The system uses PHP's built-in `mail()` function for sending emails. For production environments, you may need additional configuration.

---

## Option 1: Using PHP mail() Function (Current Setup)

### Prerequisites
- Configured mail server on your hosting environment
- For local development (XAMPP), you need to configure PHP to send emails

### XAMPP Local Setup

#### 1. Configure php.ini
Open `C:\xampp\php\php.ini` and modify the following lines:

```ini
[mail function]
SMTP = smtp.gmail.com
smtp_port = 587
sendmail_from = lspuscc.soccs@gmail.com
sendmail_path = "\"C:\xampp\sendmail\sendmail.exe\" -t"
```

#### 2. Configure sendmail.ini
Open `C:\xampp\sendmail\sendmail.ini` and configure:

```ini
[sendmail]
smtp_server=smtp.gmail.com
smtp_port=587
error_logfile=error.log
debug_logfile=debug.log
auth_username=lspuscc.soccs@gmail.com
auth_password=your_app_password_here
force_sender=lspuscc.soccs@gmail.com
```

#### 3. Generate Gmail App Password
1. Go to Google Account Settings
2. Enable 2-Step Verification
3. Go to App Passwords
4. Generate a new app password for "Mail"
5. Use this password in `sendmail.ini`

#### 4. Restart Apache
After configuration, restart Apache in XAMPP Control Panel.

---

## Option 2: Using PHPMailer (Recommended for Production)

### Installation
```bash
cd C:\xampp\htdocs\soccs-financial-management
composer require phpmailer/phpmailer
```

### Updated Email Configuration
Replace `includes/email_config.php` with:

```php
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

class EmailService {
    private $fromEmail = 'lspuscc.soccs@gmail.com';
    private $fromName = 'SOCCS Admin';
    private $smtpHost = 'smtp.gmail.com';
    private $smtpPort = 587;
    private $smtpUsername = 'lspuscc.soccs@gmail.com';
    private $smtpPassword = 'your_app_password';
    
    public function sendEmail($to, $subject, $htmlMessage) {
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = $this->smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpUsername;
            $mail->Password = $this->smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->smtpPort;
            
            // Recipients
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($to);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlMessage;
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email Error: {$mail->ErrorInfo}");
            return false;
        }
    }
    
    // ... rest of the methods
}
?>
```

---

## Testing Email Functionality

### 1. Test Email Sending
Create a test file `test_email.php`:

```php
<?php
require_once 'includes/email_config.php';

$emailService = new EmailService();
$result = $emailService->sendRegistrationConfirmation(
    'your_test_email@example.com',
    'Test User',
    'TEST001'
);

if ($result) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email. Check error logs.";
}
?>
```

### 2. Check Error Logs
- XAMPP: `C:\xampp\sendmail\error.log`
- PHP errors: `C:\xampp\php\logs\php_error_log`

---

## Common Issues & Solutions

### Issue 1: Email not sending
**Solution:** 
- Verify SMTP credentials
- Check firewall settings
- Enable "Less secure app access" or use App Password for Gmail

### Issue 2: Email goes to spam
**Solution:**
- Add SPF and DKIM records to your domain
- Use a verified sender email address
- Avoid spam trigger words in email content

### Issue 3: SMTP connection timeout
**Solution:**
- Check if port 587 or 465 is blocked by firewall
- Try alternative SMTP ports
- Verify internet connection

---

## Production Deployment

### For cPanel/Web Hosting
1. Most hosting providers have pre-configured PHP mail()
2. Use hosting provider's SMTP settings
3. Consider using services like SendGrid, Mailgun, or Amazon SES

### Environment Variables
For security, store credentials in `.env` file:

```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=lspuscc.soccs@gmail.com
SMTP_PASSWORD=your_app_password
SMTP_FROM_EMAIL=lspuscc.soccs@gmail.com
SMTP_FROM_NAME=SOCCS Admin
```

Update `email_config.php` to read from environment:
```php
private $smtpPassword = $_ENV['SMTP_PASSWORD'] ?? '';
```

---

## Email Templates

The system sends two types of emails:

1. **Registration Confirmation** - Sent immediately after registration
2. **Approval Notification** - Sent when admin approves the registration

Both templates are in `includes/email_config.php` and can be customized.

---

## Security Best Practices

1. ✅ Never commit SMTP passwords to Git
2. ✅ Use App Passwords instead of account passwords
3. ✅ Enable 2FA on email accounts
4. ✅ Use environment variables for sensitive data
5. ✅ Regularly rotate SMTP credentials
6. ✅ Monitor email sending logs
7. ✅ Implement rate limiting to prevent spam

---

## Support

For issues or questions:
- Check PHP error logs
- Review sendmail debug logs
- Contact system administrator
- Email: lspuscc.soccs@gmail.com

