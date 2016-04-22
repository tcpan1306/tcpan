<?php

//得到错误信息
function get_error($errors) {
    if (!is_array($errors)) {
        $errors = array($errors);
    }
    $html = '<ol>';
    foreach ($errors as $error) {
        $html.='<li>' . $error . '</li>';
    }
    $html .='</ol>';
    return $html;
}

/**
 * 发送短信验证码的函数
 * @param type $telphone 电话.
 * @param type $params   内容.
 * @param type $sign_name 短信模板.
 * @param type $template_code 
 * @return boolean
 */
function sendSMS($telphone, $params, $sign_name = '注册验证', $template_code = 'SMS_8050019') {
    $config = C('ALIDAYU_SETTING');
    vendor('Alidayu.Autoloader');
    $c = new \TopClient;
    $c->appkey = $config['ak'];
    $c->secretKey = $config['sk'];
    $req = new \AlibabaAliqinFcSmsNumSendRequest;
    $req->setSmsType("normal");
    $req->setSmsFreeSignName($sign_name);
//        $data = [
//            'code'=> (string) mt_rand(1000, 9999),
//            "product"=>'叫人起床尿尿'
//        ];
    $req->setSmsParam(json_encode($params));
    $req->setRecNum($telphone);
    $req->setSmsTemplateCode($template_code);
    $resp = $c->execute($req);
//        dump($resp);
    if (isset($resp->result->success) && $resp->result->success) {
        return true;
    } else {
        return false;
    }
}

    /**
     * 加盐加密.
     * @param string $password 原密码.
     * @param string $salt     盐.
     * @return string
     */
    function salt_password($password, $salt) {
        return md5(md5($password) . $salt);
    }

    function sendEmail($address, $subject, $content, $attachment = []) {
        $config = C('EMAIL_SETTING');
//        dump($config);exit;
        vendor('PHPMailer.PHPMailerAutoload');
        $mail = new \PHPMailer;
        $mail->isSMTP();                                          // 使用smtp发送邮件
        $mail->Host = $config['host'];                            // 配置发送服务器,如果是多个,使用英文逗号分隔
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];                    // 用户名
        $mail->Password = $config['password'];                    // 密码
//        $mail->SMTPSecure = $config['smtpsecure'];                // 传输协议
//        $mail->Port = $config['port'];                              // 
        $mail->setFrom($config['username']);       //发件人
        $mail->addAddress($address);                             // 收件人
        if ($attachment) {
            foreach ($attachment as $item) {
                $mail->addAttachment($item);
            }
        }
        $mail->isHTML(true);            //html格式邮件                      // Set email format to HTML
        $mail->Subject = $subject; //标题
        $mail->Body = $content; //正文
        $mail->CharSet = 'utf-8'; //编码
        return $mail->send();
    }

