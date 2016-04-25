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
        
        ['username', 'require', '用户名必填', self::MUST_VALIDATE, '', 'login'],
        ['password', 'require', '密码必填', self::MUST_VALIDATE, '', 'login'],
        ['checkcode', 'require', '验证码必填', self::MUST_VALIDATE, '', 'login'],
        ['checkcode', 'checkCode', '验证码不正确', self::MUST_VALIDATE, 'callback', 'login'],
    ];

    public function checkTel($tel) {
        if (preg_match("/^(13|14|15|17|18)\d{9}$/", $tel)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 验证验证码.
     * @param string $code 验证码.
     * @return bool
     */
    protected function checkCode($code) {
        $verify = new \Think\Verify();
        return $verify->check($code);
    }


    /**
     * 验证手机验证是否正确
     * @param type $code
     */
    public function checkPhoneCode($code) {
//        $code = session('TEL_CAPTCHA');
//        preg_match("/^(13[0-9]{9})|(18[0-9]{9})|(14[0-9]{9})|(17[0-9]{9})|(15[0-9]{9})$/",$code);
        //获取session中的验证码
        $session_code = session('TEL_CAPTCHA');
        session('TEL_CAPTCHA', null);
        if ($code == $session_code) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * TODO:自动完成
     * 盐
     * 注册时间
     */
    protected $_auto = array(
        array('salt', '\Org\Util\String::randString', self::MODEL_INSERT, 'function', 6),
        array('add_time', NOW_TIME, self::MODEL_INSERT),
    );

    /**
     * TODO:加盐加密
     */
    public function addMember() {
        $request_data = $this->data;
        //保存信息
        $this->data['password'] = $this->salt_password($this->data['password'], $this->data['salt']);
        if (($member_id = $this->add()) == false) {
            return false;
        }
        $token = \Org\Util\String::randString(40);
        //发送邮件
        $url = U('active', ['email' => $request_data['email'], 'token' => $token], true, true);
        //发送邮件

        $content = <<<EMAIL
<h1>注册成功,请激活账号</h1>
<p style="border:1px dotted blue">请点击<a href='$url'>链接</a>进行激活,如果无法点击,请复制下列地址粘贴到浏览器访问:$url</p>
<p>我们从未存在,我们无处不在!</p>
<p style="text-align:right;">北京仙人跳文化传播有限公司</p>
EMAIL;
        if (sendEmail($request_data['email'], '注册成功,请激活账号', $content) === false) {
            $this->error = '激活邮件发送失败';
            return false;
        }
        //保存邮件信息
        $data = [
            'id' => $member_id,
            'token' => $token,
            'send_time' => NOW_TIME,
        ];
        return $this->save($data);
    }

//    /**
//     * 加盐加密.
//     * @param type $password
//     * @param type $salt
//     * @return type
//     */
//    public function salt_password($password, $salt) {
//        return md5(md5($password) . $salt);
//    }

    
    /**
     * 1.验证验证码[自动验证]
     * 2.用户名和密码必填[自动验证]
     * 3.验证用户名是否存在
     * 4.验证密码是否匹配
     */
    public function login(){
        //为了安全我们将用户信息都删除
        session('MEMBER_INFO',null);
        $request_data = $this->data;
        //1.验证用户名是否存在
        $userinfo = $this->getByUsername($this->data['username']);
        if(empty($userinfo)){
            $this->error = '用户不存在';
            return false;
        }
        //2.进行密码匹配验证
        $password = salt_password($request_data['password'], $userinfo['salt']);
        if($password != $userinfo['password']){
            $this->error = '密码不正确';
            return false;
        }
        //为了后续会话获取用户信息,我们存下来
        session('MEMBER_INFO',$userinfo);
        //保存自动登陆信息
        $this->_saveToken($userinfo['id']);
        if($this->_cookie2db() === false){
            $this->error = '购物车同步失败';
            return false;
        }
        return true;
    }
    
     /**
     * 判断用户是否需要自动登陆,如果需要就保存令牌到cookie和数据表中.
     * @param integer $member_id 管理员id.
     */
    private function _saveToken($member_id){
        //清空原有的令牌
        $token_model = M('MemberToken');
        cookie('AUTO_LOGIN_TOKEN',null);
        $token_model->delete($member_id);
        
        //判断是否需要自动登陆
        $remeber = I('post.remember');
        if(!$remeber){
            return true;
        }
        $data = [
            'member_id'=>$member_id,
            'token'=>sha1(mcrypt_create_iv(32)),
        ];
        //存到cookie和数据表中
        cookie('AUTO_LOGIN_TOKEN',$data,604800);
        return $token_model->add($data);
    }
    
     private function _cookie2db(){
        //将用户的cookie购物车保存到数据库中
        $shopping_car_model = D('ShoppingCar');
        return $shopping_car_model->cookie2db();
    }
}
