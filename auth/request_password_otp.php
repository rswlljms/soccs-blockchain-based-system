<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if($_SERVER['REQUEST_METHOD']==='OPTIONS'){http_response_code(204);exit;}

require_once '../includes/database.php';
require_once '../includes/email_config.php';

$input=json_decode(file_get_contents('php://input'),true);
$email=trim($input['email']??'');
if(!$email||!filter_var($email,FILTER_VALIDATE_EMAIL)){
    echo json_encode(['status'=>'error','message'=>'Invalid email']);exit;
}

try{
    $db=new Database();
    $pdo=$db->getConnection();

    // verify email exists in users, students, or registrations
    $stmt=$pdo->prepare("SELECT email FROM users WHERE email=? UNION SELECT email FROM students WHERE email=? UNION SELECT email FROM student_registrations WHERE email=? LIMIT 1");
    $stmt->execute([$email,$email,$email]);
    if(!$stmt->fetch()){ echo json_encode(['status'=>'error','message'=>'Email not found']); exit; }

    $otp=random_int(100000,999999);

    $pdo->prepare("DELETE FROM password_resets WHERE email=?")->execute([$email]);
    $ins=$pdo->prepare("INSERT INTO password_resets(email, otp, expires_at) VALUES(?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
    $ins->execute([$email,$otp]);

    $emailService=new EmailService();
    $emailService->sendEmail($email,'Your SOCCS password reset code',"<p>Your OTP is <strong>{$otp}</strong>. It expires in 10 minutes.</p>");

    echo json_encode(['status'=>'success']);
}catch(Exception $e){
    echo json_encode(['status'=>'error','message'=>'Could not send OTP']);
}
?>


