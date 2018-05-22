<?php
require_once('email_config.php');
require('phpmailer/PHPMailer/PHPMailerAutoload.php');

$message = [];
$output = [
    'success'=>false,
    'message'=>''
];
$message['name'] = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
if(empty($message['name'])){
    $output['message'][] = 'missing name key';
}

$message['email'] = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
if(empty($message['email'])){
    $output['message'][] = 'invalid email key';
}
$message['subject'] = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
if(empty($message['subject'])) {
    $output['subject'][] = 'missing body key';
}

$message['body'] = filter_var($_POST['body'], FILTER_SANITIZE_STRING);
if(empty($message['body'])) {
    $output['messages'][] = 'missing body key';
}


$mail = new PHPMailer;
$mail->SMTPDebug = 3;           // Enable verbose debug output. Change to 0 to disable debugging output.

$mail->isSMTP();                // Set mailer to use SMTP.
$mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers.
$mail->SMTPAuth = true;         // Enable SMTP authentication


$mail->Username = EMAIL_USER;   // SMTP username
$mail->Password = EMAIL_PASS;   // SMTP password
$mail->SMTPSecure = 'tls';      // Enable TLS encryption, `ssl` also accepted, but TLS is a newer more-secure encryption
$mail->Port = 587;              // TCP port to connect to
$options = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
$mail->smtpConnect($options);
$mail->From = EMAIL_USER;  // sender's email address (shows in "From" field)
$mail->FromName = 'Portfolio Server';   // sender's name (shows in "From" field)
$mail->addAddress(EMAIL_TO_ADDRESS, EMAIL_UESRNAME);  // Add a recipient - MY ACTUAL EMAIL
$mail->addReplyTo($message['email'], $message['name']);                          // Add a reply-to address - PERSON OF INTEREST'S EMAIL

$mail->isHTML(true);                                 

$mail->Subject = 'Received email from '.$message['name'];
$currentDate = date('Y-m-d H:i:s');

$mail->Body = "
    <div>Name: {$message['name']} </div>
    <div>Email: {$message['email']}</div>
    <div>Subject: {$message['subject']}</div>
    <div>Message: {$message['body']}</div>
    <div>Meta data: {$_SERVER['REMOTE_ADDR']} at {$currentDate} </div>
";
$mail->AltBody = "
    Name: {$message['name']}
    Email: {$message['email']}
    Subject: {$message['subject']}
    Message: {$message['body']}
    Meta data: {$_SERVER['REMOTE_ADDR']} at {$currentDate}
";

if(!$mail->send()) {
    $output['message'] = 'Message could not be sent.';
    $output['error'] = 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    $output['message'] = 'Message has been sent';
    $output['success'] = true;
}
echo json_encode($output);
?>
