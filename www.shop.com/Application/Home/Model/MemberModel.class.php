<?php

namespace Home\Model;

/**
 * Description of MemberModel
 *
 * @author PR
 */
class MemberModel extends \Think\Model {

    /**
     * 用户名 必填 2-16位 唯一
     * 密码 必填 6-16位
     * email 必填 email 唯一
     * 手机号码 必填 正则手机号码 唯一
     * @var type 
     */
    protected $_validate = [
        ['username', 'require', '用户名1不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT],
        ['username', '2,16', '用户名必须是2-16位', self::EXISTS_VALIDATE, 'length', self::MODEL_INSERT],
        ['username', '', '用户名已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT],
        ['password', 'require', '密码不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT],
        ['password', '6,16', '密码必须是6-16位', self::EXISTS_VALIDATE, 'length', self::MODEL_INSERT],
        ['email', 'require', '邮箱必填', self::EXISTS_VALIDATE, '', self::MODEL_INSERT],
        ['email', '', '邮箱已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT],
        ['email', 'email', '邮箱格式不正确', self::EXISTS_VALIDATE, '', self::MODEL_INSERT],
        ['captcha', 'checkPhoneCode', '手机验证码不正确', self::EXISTS_VALIDATE, 'callback', self::MODEL_INSERT],
        ['tel', 'require', '手机号码必填', self::EXISTS_VALIDATE, '', self::MODEL_INSERT],
        ['tel', '', '手机存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT],
        ['tel', 'checkTel', '手机号错误', self::EXISTS_VALIDATE, 'callback', self::MODEL_INSERT],
    ];
    public function  checkTel($tel) {
        if(preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$tel)){     
            return true;;
}  else {
    return false;    
}
    }


    /**
 * 验证手机验证是否正确
 * @param type $code
 */
    public function checkPhoneCode($code){
//        $code = session('TEL_CAPTCHA');
//        preg_match("/^(13[0-9]{9})|(18[0-9]{9})|(14[0-9]{9})|(17[0-9]{9})|(15[0-9]{9})$/",$code);
        //获取session中的验证码
        $session_code = session('TEL_CAPTCHA');
        session('TEL_CAPTCHA',null);
        if($code == $session_code){
            return true;
        }else{
            return false;
        }
    }
    /**
     * TODO:自动完成
     * 盐
     * 注册时间
     */
    
    protected $_auto = array(
        array('salt','\Org\Util\String::randString',self::MODEL_INSERT,'function',6),
        array('add_time',NOW_TIME,self::MODEL_INSERT),
    );
    
    /**
     * TODO:加盐加密
     */
    public function addMember() {
//        dump($this->data);exit;
        //保存信息
        $this->data['password'] = $this->salt_password($this->data['password'],  $this->data['salt']);
        if(($admin_id = $this->add())== false){
            return false;
        }
        return true;
    }
    
    public function salt_password($password,$salt){
     return md5(md5($password).$salt);
 }
}
