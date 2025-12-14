<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if($_SERVER['REQUEST_METHOD']==='OPTIONS'){http_response_code(204);exit;}

require_once '../includes/database.php';

$input=json_decode(file_get_contents('php://input'),true);
$email=trim($input['email']??'');
$otp=trim($input['otp']??'');
$password=trim($input['password']??'');

if(!$email||!preg_match('/^[0-9]{6}$/',$otp)){
    echo json_encode(['status'=>'error','message'=>'Invalid request']); exit;
}

$strong = preg_match('/[a-z]/',$password)&&preg_match('/[A-Z]/',$password)&&preg_match('/\d/',$password)&&preg_match('/[^A-Za-z0-9]/',$password)&&strlen($password)>=8;
if(!$strong){ echo json_encode(['status'=>'error','message'=>'Weak password']); exit; }

try{
    $db=new Database();
    $pdo=$db->getConnection();
    $stmt=$pdo->prepare("SELECT email FROM password_resets WHERE email=? AND otp=? AND expires_at>=NOW() LIMIT 1");
    $stmt->execute([$email,$otp]);
    if(!$stmt->fetch()){
        echo json_encode(['status'=>'error','message'=>'Invalid or expired OTP']); exit;
    }

    $hash=password_hash($password,PASSWORD_DEFAULT);
    // Try users first, then students, then registrations
    $upd=$pdo->prepare("UPDATE users SET password=? WHERE email=?");
    $upd->execute([$hash,$email]);
    if($upd->rowCount()===0){
        $upd2=$pdo->prepare("UPDATE students SET password=? WHERE email=?");
        $upd2->execute([$hash,$email]);
        if($upd2->rowCount()===0){
            $upd3=$pdo->prepare("UPDATE student_registrations SET password=? WHERE email=?");
            $upd3->execute([$hash,$email]);
        }
    }
    $pdo->prepare("DELETE FROM password_resets WHERE email=?")->execute([$email]);
    echo json_encode(['status'=>'success']);
}catch(Exception $e){
    echo json_encode(['status'=>'error','message'=>'Could not reset password']);
}
?>


