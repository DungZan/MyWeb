<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

function send_verification_email($to_email, $to_name, $code) {
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'dungchohaphap@gmail.com'; // Thay bằng email gửi
        $mail->Password   = 'kbsa hssc lwnr nylc';    // Thay bằng app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        //Recipients
        $mail->setFrom('dungchohaphap@gmail.com', 'MyWeb');
        $mail->addAddress($to_email, $to_name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Mã xác thực đăng ký tài khoản';
        $mail->Body    = '<b>Mã xác nhận của bạn là: </b>' . $code;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
