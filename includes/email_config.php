<?php

class EmailService {
    private $fromEmail = 'lspuscc.soccs@gmail.com';
    private $fromName = 'SOCCS Admin';
    private $replyToEmail = 'lspuscc.soccs@gmail.com';
    
    public function sendEmail($to, $subject, $htmlMessage, $plainMessage = '') {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: {$this->fromName} <{$this->fromEmail}>" . "\r\n";
        $headers .= "Reply-To: {$this->replyToEmail}" . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        $success = @mail($to, $subject, $htmlMessage, $headers);
        
        if (!$success) {
            error_log("Failed to send email to: {$to}");
        }
        
        return $success;
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
                body {
                    margin: 0;
                    padding: 0;
                    font-family: 'Segoe UI', Arial, sans-serif;
                    background-color: #f0f0f0;
                }
                .email-wrapper {
                    width: 100%;
                    background-color: #f0f0f0;
                    padding: 20px 0;
                }
                .email-container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }
                .header {
                    background-color: #ffffff;
                    text-align: center;
                    padding: 30px 20px 20px;
                }
                .logo {
                    max-width: 100px;
                    height: auto;
                    margin-bottom: 15px;
                }
                .org-name {
                    color: #2c3e50;
                    font-size: 22px;
                    font-weight: 600;
                    margin: 10px 0 5px;
                }
                .tagline {
                    color: #B366FF;
                    font-size: 12px;
                    font-weight: 600;
                    letter-spacing: 2px;
                    margin: 5px 0;
                }
                .banner {
                    width: 100%;
                    height: 180px;
                    background: linear-gradient(135deg, #B366FF 0%, #9933ff 100%);
                    background-size: cover;
                    background-position: center;
                    background-repeat: no-repeat;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    position: relative;
                }
                .banner-icon {
                    font-size: 60px;
                    color: white;
                    position: relative;
                    z-index: 1;
                }
                .banner-image {
                    width: 100%;
                    height: 180px;
                    object-fit: cover;
                    display: block;
                }
                .content {
                    background-color: #f8f9fa;
                    padding: 35px 30px;
                    line-height: 1.7;
                }
                .greeting {
                    color: #2c3e50;
                    font-size: 18px;
                    font-weight: 600;
                    margin-bottom: 20px;
                }
                .message {
                    color: #4a5568;
                    font-size: 15px;
                    margin-bottom: 15px;
                }
                .info-box {
                    background-color: #ffffff;
                    border-left: 4px solid #B366FF;
                    padding: 20px;
                    margin: 25px 0;
                }
                .info-title {
                    color: #2c3e50;
                    font-weight: 600;
                    margin-bottom: 12px;
                }
                .info-item {
                    color: #4a5568;
                    margin: 8px 0;
                }
                .info-item strong {
                    color: #2c3e50;
                }
                .divider {
                    border: none;
                    border-top: 1px solid #e0e0e0;
                    margin: 20px 0;
                }
                .footer-note {
                    background-color: #ffffff;
                    padding: 20px 30px;
                    border-top: 1px solid #e0e0e0;
                }
                .note-text {
                    color: #718096;
                    font-size: 13px;
                    margin: 8px 0;
                }
                .contact-info {
                    color: #4a5568;
                    font-size: 14px;
                    margin-top: 15px;
                }
                .contact-link {
                    color: #B366FF;
                    text-decoration: none;
                }
                .footer {
                    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
                    color: #ffffff;
                    text-align: center;
                    padding: 25px 20px;
                }
                .footer-org {
                    font-size: 15px;
                    font-weight: 600;
                    margin-bottom: 8px;
                }
                .copyright {
                    font-size: 12px;
                    color: #bdc3c7;
                }
            </style>
        </head>
        <body>
            <div class='email-wrapper'>
                <div class='email-container'>
                    <!-- Header with Logo -->
                    <div class='header'>
                        <img src='https://i.imgur.com/SqgdZf3.png' alt='SOCCS Logo' class='logo' style='max-width: 80px; height: auto; margin-bottom: 15px;' onerror=\"this.style.display='none'; this.parentElement.innerHTML+='<div style=font-size:60px;margin-bottom:15px>📚</div>';\">
                        <div class='org-name'>Student Organization of the College of Computer Studies</div>
                        
                    </div>
                    
                    <!-- Banner -->
                    <img src='https://i.imgur.com/cRtxCnL.jpeg' alt='SOCCS Registration Banner' class='banner-image'>
                    
                    <!-- Content -->
                <div class='content'>
                        <div class='greeting'>Good day, {$firstName} {$studentId} !</div>
                        
                        <p class='message'>
                            Thank you so much for applying for SOCCS membership and we want to 
                            congratulate you for successfully finishing the registration process.
                        </p>
                        
                        <p class='message'>
                            Kindly wait for the official notification from the SOCCS Admin Team 
                            regarding your registration status via email and online portal. <strong>While waiting, please set your password first</strong> using the button below so you can log in once approved.
                        </p>
                        {$setPasswordCta}
                        
                        <div class='info-box'>
                            <div class='info-title'>Your Registration Details:</div>
                            <div class='info-item'><strong>Student ID:</strong> {$studentId}</div>
                            <div class='info-item'><strong>Email:</strong> {$email}</div>
                            <div class='info-item'><strong>Status:</strong> Pending Approval</div>
                        </div>
                        
                        <p class='message'>
                            Keep safe and stay connected.
                        </p>
                    </div>
                    
                    <!-- Footer Note -->
                    <div class='footer-note'>
                        <p class='note-text'>
                            <strong>This is a system generated message. Do not reply.</strong>
                        </p>
                        <p class='note-text'>
                            This message is intended to <strong>{$email}</strong> upon request this {$requestedAt}.
                        </p>
                        
                        <hr class='divider'>
                        
                        <p class='contact-info'>
                            If you have questions, feedback or suggestions, feel free to contact us at 
                            <a href='mailto:lspuscc.soccs@gmail.com' class='contact-link'>lspuscc.soccs@gmail.com</a>
                        </p>
                        
                        <p class='note-text' style='margin-top: 20px;'>
                            To help keep your account secure, please do not forward this email.<br>
                            We recommend to mark this email as not spam to improve the integrity of SOCCS mail servers.
                        </p>
                    </div>
                    
                    <!-- Footer -->
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
        
        // Set timezone to ensure correct current time
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
                <!-- SOCCS Banner -->
                <img src='https://i.imgur.com/cRtxCnL.jpeg' alt='SOCCS Banner' class='banner'>
                
                <div class='content'>
                    <div class='message'>
                        <p>Dear <strong>$firstName</strong>,</p>
                        <p>Great news! Your registration to the Student Organization of the College of Computer Studies (SOCCS) has been <strong>approved</strong>!</p>
                    </div>
                    
                    <div class='highlight'>
                        <strong>Student ID:</strong> $studentId<br>
                        <strong>Status:</strong> Approved ✅
                    </div>
                    
                    <div class='message'>
                        <p><strong>Next Step: Set Your Password</strong></p>
                        <p>To access your student dashboard, please set your password by clicking the button below. This link will expire in 24 hours.</p>
                    </div>
                    
                    <div style='text-align: center;'>
                        <a href='$setUrl' class='cta-button'>
                            Set Your Password
                        </a>
                    </div>
                    
                    <div class='message'>
                        <p style='font-size: 12px; color: #6b7280;'>If the button doesn't work, copy and paste this URL into your browser:<br><span style='word-break:break-all'>$setUrl</span></p>
                    </div>
                    
                    <div class='message'>
                        <p>Welcome to SOCCS!</p>
                    </div>
                    
                    <div style='background-color: #f8f9fa; padding: 20px; border-top: 1px solid #e0e0e0; margin-top: 20px;'>
                        <p style='color: #718096; font-size: 13px; margin: 8px 0;'>
                            <strong>This is a system generated message. Do not reply.</strong>
                        </p>
                        <p style='color: #718096; font-size: 13px; margin: 8px 0;'>
                            This message is intended to <strong>$email</strong> upon request this " . date('M d, Y h:i a', time()) . ".
                        </p>
                        
                        <p style='color: #4a5568; font-size: 14px; margin-top: 15px;'>
                            If you have questions, feedback or suggestions, feel free to contact us at 
                            <a href='mailto:lspuscc.soccs@gmail.com' style='color: #B366FF; text-decoration: none;'>lspuscc.soccs@gmail.com</a>
                        </p>
                        
                        <p style='color: #718096; font-size: 13px; margin-top: 20px;'>
                            To help keep your account secure, please do not forward this email.<br>
                            We recommend to mark this email as not spam to improve the integrity of SOCCS mail servers.
                        </p>
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
        
        // Set timezone to ensure correct current time
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
                <!-- SOCCS Banner -->
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
                        <p style='color: #718096; font-size: 13px; margin: 8px 0;'>
                            <strong>This is a system generated message. Do not reply.</strong>
                        </p>
                        <p style='color: #718096; font-size: 13px; margin: 8px 0;'>
                            This message is intended to <strong>$email</strong> upon request this " . date('M d, Y h:i a', time()) . ".
                        </p>
                        
                        <p style='color: #4a5568; font-size: 14px; margin-top: 15px;'>
                            If you have questions, feedback or suggestions, feel free to contact us at 
                            <a href='mailto:lspuscc.soccs@gmail.com' style='color: #B366FF; text-decoration: none;'>lspuscc.soccs@gmail.com</a>
                        </p>
                        
                        <p style='color: #718096; font-size: 13px; margin-top: 20px;'>
                            To help keep your account secure, please do not forward this email.<br>
                            We recommend to mark this email as not spam to improve the integrity of SOCCS mail servers.
                        </p>
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
}
?>

