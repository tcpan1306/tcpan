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
    
    /**
 * 加盐加密.
 * @param string $password 原密码.
 * @param string $salt     盐.
 * @return string
 */
 function salt_password($password,$salt){
     return md5(md5($password).$salt);
 }
}
