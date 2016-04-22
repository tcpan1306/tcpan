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
//        ['captcha', 'checkPhoneCode', '手机验证码不正确', self::EXISTS_VALIDATE, 'callback', self::MODEL_INSERT],
//        ['tel', 'require', '手机号码必填', self::EXISTS_VALIDATE, '', self::MODEL_INSERT],
////        ['tel', '', '手机存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT],
//        ['tel', 'checkTel', '手机号错误', self::EXISTS_VALIDATE, 'callback', self::MODEL_INSERT],
    ];

    public function checkTel($tel) {
        if (preg_match("/^(13|14|15|17|18)\d{9}$/", $tel)) {
            return true;
            ;
        } else {
            return false;
        }
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
        if (($member_id= $this->add()) == false) {
            return false;
        }
          $token =\Org\Util\String::randString(40) ;
        //发送邮件
        $url = U('active', ['email' => $request_data['email'], 'token' =>$token],true, true);
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
            'id'=>$member_id,
            'token'=>$token,
            'send_time'=>NOW_TIME,
        ];
        return  $this->save($data);
    }

    public function salt_password($password, $salt) {
        return md5(md5($password) . $salt);
    }

}
