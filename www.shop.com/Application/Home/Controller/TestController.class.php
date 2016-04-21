<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;

/**
 * Description of TestController
 *17002810530
 * @author PR
 */
class TestController extends \Think\Controller {

    public function sendSMS() {
              $flag = sendSMS('13075412875', ['code' => (string)  mt_rand(1000, 9999), 'product' => '叫人起床尿尿']);
    }
      
    public function sendEmail() {
        $address = '13075412875@163.com';

//        require 'PHPMailerAutoload.php';
        vendor('PHPMailer.PHPMailerAutoload');

        $mail = new \PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host       = 'smtp.163.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth   = true;                               // Enable SMTP authentication
        $mail->Username   = '13075412875@163.com';                 // SMTP username
        $mail->Password   = 'tcpan163';                           // SMTP password
//        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
//        $mail->Port       = 25;                                    // TCP port to connect to

        $mail->setFrom('13075412875@163.com', 'tcpan163');
        $mail->addAddress('13075412875@163.com');     // Add a recipient

//        $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'welcome join us';
        $mail->Body    = '<span style="color:red">tcpan</span>人生如梦 一晃就过';
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        $mail->CharSet = 'utf-8';

        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }
    }
}
