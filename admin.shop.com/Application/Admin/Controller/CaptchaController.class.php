<?php
namespace Admin\Controller;
class CaptchaController extends \Think\Controller{
    public function captcha(){
        $options = array('length'=>4);
        $verify = new \Think\Verify($options);
        $verify->entry();
    }
}
