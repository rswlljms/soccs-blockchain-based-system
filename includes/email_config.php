<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/../vendor/autoload.php';

class EmailService {
    private $fromEmail = 'lspuscc.soccs@gmail.com';
    private $fromName = 'SOCCS Admin';
    private $smtpHost = 'smtp.gmail.com';
    private $smtpPort = 587;
    private $smtpUsername = 'lspuscc.soccs@gmail.com';
    private $smtpPassword = ''; // SET YOUR GMAIL APP PASSWORD HERE
    
    public function __construct() {
        require_once __DIR__ . '/app_config.php';
        $password = AppConfig::get('smtp_password');
        $username = AppConfig::get('smtp_username');
        if (!empty($password)) {
            $this->smtpPassword = $password;
        }
        if (!empty($username)) {
            $this->smtpUsername = $username;
            $this->fromEmail = $username;
        }
    }
    
    private function sanitizeRichText($text) {
        if (empty($text)) {
            return '';
        }
        
        $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><a><span><div>';
        $text = strip_tags($text, $allowedTags);
        
        $text = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $text);
        $text = preg_replace('/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi', '', $text);
        $text = preg_replace('/on\w+="[^"]*"/i', '', $text);
        $text = preg_replace('/on\w+=\'[^\']*\'/i', '', $text);
        
        $text = nl2br($text);
        
        return $text;
    }
    
    public function sendEmail($to, $subject, $htmlMessage, $plainMessage = '') {
        $mail = new PHPMailer(true);
        
        try {
            // SMTP Configuration
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
            $mail->addReplyTo($this->fromEmail, $this->fromName);
            
            // Content
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->Subject = $subject;
            $mail->Body = $htmlMessage;
            $mail->AltBody = $plainMessage ?: strip_tags($htmlMessage);
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email sending failed: " . $mail->ErrorInfo);
            return false;
        }
    }
    
    public function sendRegistrationConfirmation($email, $firstName, $studentId, $token = null) {
        $subject = "SOCCS Registration - Confirmation";
        $message = $this->getRegistrationEmailTemplate($firstName, $studentId, $email, $token);
        return $this->sendEmail($email, $subject, $message);
    }

    public function sendSetPasswordLink($email, $firstName, $token) {
        $subject = "Set Your SOCCS Account Password";
        $baseUrl = 'http://localhost/soccs-financial-management/templates/set-password.php';
        $link = $baseUrl . '?token=' . urlencode($token);

        $html = "
        <div style='font-family:Segoe UI,Arial,sans-serif;color:#1f2937'>
            <h2 style='color:#111827;margin-bottom:8px'>Hi {$firstName},</h2>
            <p style='margin:0 0 12px'>Finish setting up your SOCCS account by creating your password.</p>
            <p style='margin:0 0 16px'>For your security, this link will expire in 24 hours.</p>
            <p><a href='{$link}' style='display:inline-block;background:#9333ea;color:#fff;text-decoration:none;padding:10px 16px;border-radius:6px;font-weight:600'>Set password</a></p>
            <p style='font-size:12px;color:#6b7280;margin-top:16px'>If the button doesn't work, copy and paste this URL into your browser:<br><span style='word-break:break-all'>{$link}</span></p>
        </div>";

        return $this->sendEmail($email, $subject, $html);
    }
    
    private function getRegistrationEmailTemplate($firstName, $studentId, $email, $token = null) {
        $setPasswordCta = '';
        $tz = getenv('APP_TZ') ?: 'Asia/Manila';
        try {
            $requestedAt = (new DateTime('now', new DateTimeZone($tz)))->format('M d, Y h:i a');
        } catch (Exception $e) {
            $requestedAt = date('M d, Y h:i a', time());
        }
        if ($token) {
            $setUrl = 'http://localhost/soccs-financial-management/templates/set-password.php?token=' . urlencode($token);
            $setPasswordCta = "
                <div style='text-align:center;margin:22px 0'>
                    <a href='{$setUrl}' style='display:inline-block;background:#9333ea;color:#fff;text-decoration:none;padding:12px 22px;border-radius:8px;font-weight:700'>Set your password</a>
                    <div style='font-size:12px;color:#6b7280;margin-top:10px'>Link expires in 24 hours. If the button doesn't work, copy this URL:<br><span style='word-break:break-all'>{$setUrl}</span></div>
                </div>
            ";
        }
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; background-color: #f0f0f0; }
                .email-wrapper { width: 100%; background-color: #f0f0f0; padding: 20px 0; }
                .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
                .header { background-color: #ffffff; text-align: center; padding: 30px 20px 20px; }
                .logo { max-width: 100px; height: auto; margin-bottom: 15px; }
                .org-name { color: #2c3e50; font-size: 22px; font-weight: 600; margin: 10px 0 5px; }
                .banner-image { width: 100%; height: 180px; object-fit: cover; display: block; }
                .content { background-color: #f8f9fa; padding: 35px 30px; line-height: 1.7; }
                .greeting { color: #2c3e50; font-size: 18px; font-weight: 600; margin-bottom: 20px; }
                .message { color: #4a5568; font-size: 15px; margin-bottom: 15px; }
                .info-box { background-color: #ffffff; border-left: 4px solid #B366FF; padding: 20px; margin: 25px 0; }
                .info-title { color: #2c3e50; font-weight: 600; margin-bottom: 12px; }
                .info-item { color: #4a5568; margin: 8px 0; }
                .info-item strong { color: #2c3e50; }
                .divider { border: none; border-top: 1px solid #e0e0e0; margin: 20px 0; }
                .footer-note { background-color: #ffffff; padding: 20px 30px; border-top: 1px solid #e0e0e0; }
                .note-text { color: #718096; font-size: 13px; margin: 8px 0; }
                .contact-info { color: #4a5568; font-size: 14px; margin-top: 15px; }
                .contact-link { color: #B366FF; text-decoration: none; }
                .footer { background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: #ffffff; text-align: center; padding: 25px 20px; }
                .footer-org { font-size: 15px; font-weight: 600; margin-bottom: 8px; }
                .copyright { font-size: 12px; color: #bdc3c7; }
            </style>
        </head>
        <body>
            <div class='email-wrapper'>
                <div class='email-container'>
                    <div class='header'>
                        <img src='https://i.imgur.com/SqgdZf3.png' alt='SOCCS Logo' class='logo' style='max-width: 80px; height: auto; margin-bottom: 15px;'>
                        <div class='org-name'>Student Organization of the College of Computer Studies</div>
                    </div>
                    <img src='https://i.imgur.com/cRtxCnL.jpeg' alt='SOCCS Registration Banner' class='banner-image'>
                    <div class='content'>
                        <div class='greeting'>Good day, {$firstName} {$studentId} !</div>
                        <p class='message'>Thank you so much for applying for SOCCS membership and we want to congratulate you for successfully finishing the registration process.</p>
                        <p class='message'>Kindly wait for the official notification from the SOCCS Admin Team regarding your registration status via email and online portal. <strong>While waiting, please set your password first</strong> using the button below so you can log in once approved.</p>
                        {$setPasswordCta}
                        <div class='info-box'>
                            <div class='info-title'>Your Registration Details:</div>
                            <div class='info-item'><strong>Student ID:</strong> {$studentId}</div>
                            <div class='info-item'><strong>Email:</strong> {$email}</div>
                            <div class='info-item'><strong>Status:</strong> Pending Approval</div>
                        </div>
                        <p class='message'>Keep safe and stay connected.</p>
                    </div>
                    <div class='footer-note'>
                        <p class='note-text'><strong>This is a system generated message. Do not reply.</strong></p>
                        <p class='note-text'>This message is intended to <strong>{$email}</strong> upon request this {$requestedAt}.</p>
                        <hr class='divider'>
                        <p class='contact-info'>If you have questions, feedback or suggestions, feel free to contact us at <a href='mailto:lspuscc.soccs@gmail.com' class='contact-link'>lspuscc.soccs@gmail.com</a></p>
                        <p class='note-text' style='margin-top: 20px;'>To help keep your account secure, please do not forward this email.<br>We recommend to mark this email as not spam to improve the integrity of SOCCS mail servers.</p>
                    </div>
                    <div class='footer'>
                        <div class='footer-org'>Student Organization of the College of Computer Studies</div>
                        <div class='copyright'>Copyright © 2024</div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    public function sendApprovalWithPasswordSetup($email, $firstName, $studentId, $token) {
        $subject = "Registration Approved - Set Your Password";
        $setUrl = 'http://localhost/soccs-financial-management/templates/set-password.php?token=' . urlencode($token);
        date_default_timezone_set('Asia/Manila');
        
        $htmlContent = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Registration Approved</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
                .content { padding: 30px; }
                .message { font-size: 16px; line-height: 1.6; margin-bottom: 25px; }
                .highlight { background: #d4edda; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745; margin: 20px 0; }
                .cta-button { display: inline-block; background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 20px 0; }
                .footer { background: #2c3e50; color: white; padding: 20px; text-align: center; font-size: 14px; }
                .banner { width: 100%; height: auto; display: block; }
                .footer-org { font-size: 15px; font-weight: 600; margin-bottom: 8px; }
                .copyright { font-size: 12px; color: #bdc3c7; }
            </style>
        </head>
        <body>
            <div class='container'>
                <img src='https://i.imgur.com/cRtxCnL.jpeg' alt='SOCCS Banner' class='banner'>
                <div class='content'>
                    <div class='message'>
                        <p>Dear <strong>$firstName</strong>,</p>
                        <p>Great news! Your registration to the Student Organization of the College of Computer Studies (SOCCS) has been <strong>approved</strong>!</p>
                    </div>
                    <div class='highlight'>
                        <strong>Student ID:</strong> $studentId<br>
                        <strong>Status:</strong> Approved
                    </div>
                    <div class='message'>
                        <p><strong>Next Step: Set Your Password</strong></p>
                        <p>To access your student dashboard, please set your password by clicking the button below. This link will expire in 24 hours.</p>
                    </div>
                    <div style='text-align: center;'>
                        <a href='$setUrl' class='cta-button'>Set Your Password</a>
                    </div>
                    <div class='message'>
                        <p style='font-size: 12px; color: #6b7280;'>If the button doesn't work, copy and paste this URL into your browser:<br><span style='word-break:break-all'>$setUrl</span></p>
                    </div>
                    <div class='message'><p>Welcome to SOCCS!</p></div>
                    <div style='background-color: #f8f9fa; padding: 20px; border-top: 1px solid #e0e0e0; margin-top: 20px;'>
                        <p style='color: #718096; font-size: 13px; margin: 8px 0;'><strong>This is a system generated message. Do not reply.</strong></p>
                        <p style='color: #718096; font-size: 13px; margin: 8px 0;'>This message is intended to <strong>$email</strong> upon request this " . date('M d, Y h:i a', time()) . ".</p>
                        <p style='color: #4a5568; font-size: 14px; margin-top: 15px;'>If you have questions, feedback or suggestions, feel free to contact us at <a href='mailto:lspuscc.soccs@gmail.com' style='color: #B366FF; text-decoration: none;'>lspuscc.soccs@gmail.com</a></p>
                        <p style='color: #718096; font-size: 13px; margin-top: 20px;'>To help keep your account secure, please do not forward this email.<br>We recommend to mark this email as not spam to improve the integrity of SOCCS mail servers.</p>
                    </div>
                </div>
                <div class='footer'>
                    <div class='footer-org'>Student Organization of the College of Computer Studies</div>
                    <div class='copyright'>Copyright © 2024</div>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $this->sendEmail($email, $subject, $htmlContent);
    }

    public function sendRejectionNotification($email, $firstName, $studentId, $reason) {
        $subject = "Registration Update - SOCCS";
        date_default_timezone_set('Asia/Manila');
        
        $htmlContent = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Registration Update</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
                .content { padding: 30px; }
                .message { font-size: 16px; line-height: 1.6; margin-bottom: 25px; }
                .reason-box { background: #f8d7da; padding: 20px; border-radius: 8px; border-left: 4px solid #dc3545; margin: 20px 0; }
                .reason-title { font-weight: bold; color: #721c24; margin-bottom: 10px; }
                .reason-text { color: #721c24; }
                .footer { background: #2c3e50; color: white; padding: 20px; text-align: center; font-size: 14px; }
                .banner { width: 100%; height: auto; display: block; }
                .footer-org { font-size: 15px; font-weight: 600; margin-bottom: 8px; }
                .copyright { font-size: 12px; color: #bdc3c7; }
            </style>
        </head>
        <body>
            <div class='container'>
                <img src='https://i.imgur.com/cRtxCnL.jpeg' alt='SOCCS Banner' class='banner'>
                <div class='content'>
                    <div class='message'>
                        <p>Dear <strong>$firstName</strong>,</p>
                        <p>Thank you for your interest in registering with the Student Organization of the College of Computer Studies (SOCCS).</p>
                    </div>
                    <div class='reason-box'>
                        <div class='reason-title'>Registration Status: Not Approved</div>
                        <div class='reason-text'>
                            <strong>Student ID:</strong> $studentId<br>
                            <strong>Reason:</strong> $reason
                        </div>
                    </div>
                    <div class='message'>
                        <p>If you believe this decision was made in error or if you have additional information to provide, please contact the organization administrators.</p>
                        <p>You may reapply in the future if your circumstances change.</p>
                    </div>
                    <div style='background-color: #f8f9fa; padding: 20px; border-top: 1px solid #e0e0e0; margin-top: 20px;'>
                        <p style='color: #718096; font-size: 13px; margin: 8px 0;'><strong>This is a system generated message. Do not reply.</strong></p>
                        <p style='color: #718096; font-size: 13px; margin: 8px 0;'>This message is intended to <strong>$email</strong> upon request this " . date('M d, Y h:i a', time()) . ".</p>
                        <p style='color: #4a5568; font-size: 14px; margin-top: 15px;'>If you have questions, feedback or suggestions, feel free to contact us at <a href='mailto:lspuscc.soccs@gmail.com' style='color: #B366FF; text-decoration: none;'>lspuscc.soccs@gmail.com</a></p>
                        <p style='color: #718096; font-size: 13px; margin-top: 20px;'>To help keep your account secure, please do not forward this email.<br>We recommend to mark this email as not spam to improve the integrity of SOCCS mail servers.</p>
                    </div>
                </div>
                <div class='footer'>
                    <div class='footer-org'>Student Organization of the College of Computer Studies</div>
                    <div class='copyright'>Copyright © 2024</div>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $this->sendEmail($email, $subject, $htmlContent);
    }

    public function getActiveStudentEmails() {
        require_once __DIR__ . '/database.php';
        $database = new Database();
        $conn = $database->getConnection();
        
        try {
            $query = "SELECT email, first_name FROM students WHERE is_active = 1 AND email IS NOT NULL AND email != ''";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching student emails: " . $e->getMessage());
            return [];
        }
    }

    public function sendBulkEmail($emails, $subject, $htmlMessage, $plainMessage = '') {
        $successCount = 0;
        $failCount = 0;
        
        foreach ($emails as $emailData) {
            $email = is_array($emailData) ? $emailData['email'] : $emailData;
            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if ($this->sendEmail($email, $subject, $htmlMessage, $plainMessage)) {
                    $successCount++;
                } else {
                    $failCount++;
                }
                usleep(50000);
            }
        }
        
        return [
            'success' => $successCount,
            'failed' => $failCount,
            'total' => count($emails)
        ];
    }

    public function sendBulkEmailAsync($emails, $subject, $htmlMessage, $plainMessage = '') {
        ignore_user_abort(true);
        set_time_limit(300);
        
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        
        $this->sendBulkEmail($emails, $subject, $htmlMessage, $plainMessage);
    }

    public function getEventNotificationContent($eventTitle, $eventDate, $eventLocation, $eventDescription, $eventCategory) {
        date_default_timezone_set('Asia/Manila');
        $formattedDate = date('F d, Y h:i A', strtotime($eventDate));
        
        $htmlContent = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>New Event Announcement</title>
            <style>
                body { font-family: 'Segoe UI', 'Microsoft YaHei', 'SimHei', 'SimSun', 'Arial Unicode MS', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
                .content { padding: 30px; }
                .message { font-size: 16px; line-height: 1.6; margin-bottom: 25px; }
                .event-box { background: #e3f2fd; padding: 20px; border-radius: 8px; border-left: 4px solid #2196F3; margin: 20px 0; }
                .event-title { font-size: 20px; font-weight: bold; color: #1976D2; margin-bottom: 15px; }
                .event-detail { color: #424242; margin: 10px 0; }
                .event-detail strong { color: #1976D2; }
                .description-content { color: #424242; line-height: 1.8; word-wrap: break-word; }
                .description-content p { margin: 10px 0; }
                .description-content ul, .description-content ol { margin: 10px 0; padding-left: 30px; }
                .description-content li { margin: 5px 0; }
                .footer { background: #2c3e50; color: white; padding: 20px; text-align: center; font-size: 14px; }
                .banner { width: 100%; height: auto; display: block; }
                .footer-org { font-size: 15px; font-weight: 600; margin-bottom: 8px; }
                .copyright { font-size: 12px; color: #bdc3c7; }
            </style>
        </head>
        <body>
            <div class='container'>
                <img src='https://i.imgur.com/cRtxCnL.jpeg' alt='SOCCS Banner' class='banner'>
                <div class='content'>
                    <div class='message'>
                        <p>Dear SOCCS Member,</p>
                        <p>We are excited to announce a new event that you might be interested in!</p>
                    </div>
                    <div class='event-box'>
                        <div class='event-title'>" . htmlspecialchars($eventTitle) . "</div>
                        <div class='event-detail'><strong>Date & Time:</strong> " . htmlspecialchars($formattedDate) . "</div>
                        <div class='event-detail'><strong>Location:</strong> " . htmlspecialchars($eventLocation) . "</div>
                        <div class='event-detail'><strong>Category:</strong> " . htmlspecialchars(ucfirst($eventCategory)) . "</div>
                        <div class='event-detail'><strong>Description:</strong><br><div class='description-content'>" . $this->sanitizeRichText($eventDescription) . "</div></div>
                    </div>
                    <div class='message'>
                        <p>We hope to see you there! Please check your student dashboard for more details and updates.</p>
                    </div>
                    <div style='background-color: #f8f9fa; padding: 20px; border-top: 1px solid #e0e0e0; margin-top: 20px;'>
                        <p style='color: #718096; font-size: 13px; margin: 8px 0;'><strong>This is a system generated message. Do not reply.</strong></p>
                        <p style='color: #718096; font-size: 13px; margin: 8px 0;'>This message was sent to all registered SOCCS members on " . date('M d, Y h:i a', time()) . ".</p>
                        <p style='color: #4a5568; font-size: 14px; margin-top: 15px;'>If you have questions, feedback or suggestions, feel free to contact us at <a href='mailto:lspuscc.soccs@gmail.com' style='color: #2196F3; text-decoration: none;'>lspuscc.soccs@gmail.com</a></p>
                    </div>
                </div>
                <div class='footer'>
                    <div class='footer-org'>Student Organization of the College of Computer Studies</div>
                    <div class='copyright'>Copyright © 2024</div>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return ['subject' => "New SOCCS Event: " . $eventTitle, 'content' => $htmlContent];
    }

    public function sendEventNotification($eventTitle, $eventDate, $eventLocation, $eventDescription, $eventCategory) {
        $students = $this->getActiveStudentEmails();
        if (empty($students)) {
            return ['success' => 0, 'failed' => 0, 'total' => 0];
        }
        
        $emailData = $this->getEventNotificationContent($eventTitle, $eventDate, $eventLocation, $eventDescription, $eventCategory);
        return $this->sendBulkEmail($students, $emailData['subject'], $emailData['content']);
    }

    public function getFilingCandidacyNotificationContent($title, $announcementText, $formLink, $startDate, $endDate, $screeningDate = null) {
        date_default_timezone_set('Asia/Manila');
        $formattedStartDate = date('F d, Y h:i A', strtotime($startDate));
        $formattedEndDate = date('F d, Y h:i A', strtotime($endDate));
        $screeningInfo = $screeningDate ? "<div class='event-detail'><strong>Screening Date:</strong> " . htmlspecialchars($screeningDate) . "</div>" : "";
        
        $htmlContent = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Filing of Candidacy</title>
            <style>
                body { font-family: 'Segoe UI', 'Microsoft YaHei', 'SimHei', 'SimSun', 'Arial Unicode MS', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
                .content { padding: 30px; }
                .message { font-size: 16px; line-height: 1.6; margin-bottom: 25px; }
                .candidacy-box { background: #fff3cd; padding: 20px; border-radius: 8px; border-left: 4px solid #ffc107; margin: 20px 0; }
                .candidacy-title { font-size: 20px; font-weight: bold; color: #856404; margin-bottom: 15px; }
                .event-detail { color: #424242; margin: 10px 0; }
                .event-detail strong { color: #856404; }
                .announcement-content { color: #424242; line-height: 1.8; word-wrap: break-word; }
                .announcement-content p { margin: 10px 0; }
                .announcement-content ul, .announcement-content ol { margin: 10px 0; padding-left: 30px; }
                .announcement-content li { margin: 5px 0; }
                .cta-button { display: inline-block; background: #ffc107; color: #000; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 20px 0; }
                .footer { background: #2c3e50; color: white; padding: 20px; text-align: center; font-size: 14px; }
                .banner { width: 100%; height: auto; display: block; }
                .footer-org { font-size: 15px; font-weight: 600; margin-bottom: 8px; }
                .copyright { font-size: 12px; color: #bdc3c7; }
            </style>
        </head>
        <body>
            <div class='container'>
                <img src='https://i.imgur.com/cRtxCnL.jpeg' alt='SOCCS Banner' class='banner'>
                <div class='content'>
                    <div class='message'>
                        <p>Dear SOCCS Member,</p>
                        <p>The filing of candidacy period is now open! If you are interested in running for a position, please submit your application.</p>
                    </div>
                    <div class='candidacy-box'>
                        <div class='candidacy-title'>" . htmlspecialchars($title) . "</div>
                        <div class='event-detail'><strong>Start Date:</strong> " . htmlspecialchars($formattedStartDate) . "</div>
                        <div class='event-detail'><strong>End Date:</strong> " . htmlspecialchars($formattedEndDate) . "</div>
                        " . $screeningInfo . "
                        <div class='event-detail'><strong>Announcement:</strong><br><div class='announcement-content'>" . $this->sanitizeRichText($announcementText) . "</div></div>
                    </div>
                    <div style='text-align: center;'>
                        <a href='" . htmlspecialchars($formLink) . "' class='cta-button' target='_blank'>Apply for Candidacy</a>
                    </div>
                    <div class='message'>
                        <p>Don't miss this opportunity to serve the SOCCS community! Click the button above to access the application form.</p>
                    </div>
                    <div style='background-color: #f8f9fa; padding: 20px; border-top: 1px solid #e0e0e0; margin-top: 20px;'>
                        <p style='color: #718096; font-size: 13px; margin: 8px 0;'><strong>This is a system generated message. Do not reply.</strong></p>
                        <p style='color: #718096; font-size: 13px; margin: 8px 0;'>This message was sent to all registered SOCCS members on " . date('M d, Y h:i a', time()) . ".</p>
                        <p style='color: #4a5568; font-size: 14px; margin-top: 15px;'>If you have questions, feedback or suggestions, feel free to contact us at <a href='mailto:lspuscc.soccs@gmail.com' style='color: #ffc107; text-decoration: none;'>lspuscc.soccs@gmail.com</a></p>
                    </div>
                </div>
                <div class='footer'>
                    <div class='footer-org'>Student Organization of the College of Computer Studies</div>
                    <div class='copyright'>Copyright © 2024</div>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return ['subject' => "Filing of Candidacy Now Open: " . $title, 'content' => $htmlContent];
    }

    public function sendFilingCandidacyNotification($title, $announcementText, $formLink, $startDate, $endDate, $screeningDate = null) {
        $students = $this->getActiveStudentEmails();
        if (empty($students)) {
            return ['success' => 0, 'failed' => 0, 'total' => 0];
        }
        
        $emailData = $this->getFilingCandidacyNotificationContent($title, $announcementText, $formLink, $startDate, $endDate, $screeningDate);
        return $this->sendBulkEmail($students, $emailData['subject'], $emailData['content']);
    }

    public function getElectionNotificationContent($electionTitle, $electionDescription, $startDate, $endDate) {
        date_default_timezone_set('Asia/Manila');
        $formattedStartDate = date('F d, Y h:i A', strtotime($startDate));
        $formattedEndDate = date('F d, Y h:i A', strtotime($endDate));
        $dashboardUrl = 'http://localhost/soccs-financial-management/pages/student-voting.php';
        
        $htmlContent = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Election Active</title>
            <style>
                body { font-family: 'Segoe UI', 'Microsoft YaHei', 'SimHei', 'SimSun', 'Arial Unicode MS', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
                .content { padding: 30px; }
                .message { font-size: 16px; line-height: 1.6; margin-bottom: 25px; }
                .election-box { background: #d4edda; padding: 20px; border-radius: 8px; border-left: 4px solid #28a745; margin: 20px 0; }
                .election-title { font-size: 20px; font-weight: bold; color: #155724; margin-bottom: 15px; }
                .event-detail { color: #424242; margin: 10px 0; }
                .event-detail strong { color: #155724; }
                .description-content { color: #424242; line-height: 1.8; word-wrap: break-word; }
                .description-content p { margin: 10px 0; }
                .description-content ul, .description-content ol { margin: 10px 0; padding-left: 30px; }
                .description-content li { margin: 5px 0; }
                .cta-button { display: inline-block; background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 20px 0; }
                .footer { background: #2c3e50; color: white; padding: 20px; text-align: center; font-size: 14px; }
                .banner { width: 100%; height: auto; display: block; }
                .footer-org { font-size: 15px; font-weight: 600; margin-bottom: 8px; }
                .copyright { font-size: 12px; color: #bdc3c7; }
            </style>
        </head>
        <body>
            <div class='container'>
                <img src='https://i.imgur.com/cRtxCnL.jpeg' alt='SOCCS Banner' class='banner'>
                <div class='content'>
                    <div class='message'>
                        <p>Dear SOCCS Member,</p>
                        <p>The election is now active! Your vote matters in shaping the future of SOCCS.</p>
                    </div>
                    <div class='election-box'>
                        <div class='election-title'>" . htmlspecialchars($electionTitle) . "</div>
                        <div class='event-detail'><strong>Start Date:</strong> " . htmlspecialchars($formattedStartDate) . "</div>
                        <div class='event-detail'><strong>End Date:</strong> " . htmlspecialchars($formattedEndDate) . "</div>
                        " . ($electionDescription ? "<div class='event-detail'><strong>Description:</strong><br><div class='description-content'>" . $this->sanitizeRichText($electionDescription) . "</div></div>" : "") . "
                    </div>
                    <div style='text-align: center;'>
                        <a href='" . htmlspecialchars($dashboardUrl) . "' class='cta-button'>Cast Your Vote</a>
                    </div>
                    <div class='message'>
                        <p><strong>Important:</strong> Please cast your vote before the election ends. Every vote counts!</p>
                        <p>Log in to your student dashboard to access the voting page.</p>
                    </div>
                    <div style='background-color: #f8f9fa; padding: 20px; border-top: 1px solid #e0e0e0; margin-top: 20px;'>
                        <p style='color: #718096; font-size: 13px; margin: 8px 0;'><strong>This is a system generated message. Do not reply.</strong></p>
                        <p style='color: #718096; font-size: 13px; margin: 8px 0;'>This message was sent to all registered SOCCS members on " . date('M d, Y h:i a', time()) . ".</p>
                        <p style='color: #4a5568; font-size: 14px; margin-top: 15px;'>If you have questions, feedback or suggestions, feel free to contact us at <a href='mailto:lspuscc.soccs@gmail.com' style='color: #28a745; text-decoration: none;'>lspuscc.soccs@gmail.com</a></p>
                    </div>
                </div>
                <div class='footer'>
                    <div class='footer-org'>Student Organization of the College of Computer Studies</div>
                    <div class='copyright'>Copyright © 2024</div>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return ['subject' => "Election Now Active: " . $electionTitle, 'content' => $htmlContent];
    }

    public function sendElectionNotification($electionTitle, $electionDescription, $startDate, $endDate) {
        $students = $this->getActiveStudentEmails();
        if (empty($students)) {
            return ['success' => 0, 'failed' => 0, 'total' => 0];
        }
        
        $emailData = $this->getElectionNotificationContent($electionTitle, $electionDescription, $startDate, $endDate);
        return $this->sendBulkEmail($students, $emailData['subject'], $emailData['content']);
    }
}
?>
