<?php

namespace Home\Controller;

/**
 * Description of MemberController
 *
 * @author PR
 */
class MemberController extends \Think\Controller {

   /**
     * 存储模型对象.
     * @var \Home\Model\MemberModel 
     */
    protected function _initialize() {
        $meta_titles = array(
            'index' => '啊咿呀哟母婴商城',
            'register' => '用户注册',
            'edit' => '修改商品分类',
            'delete' => '删除商品分类',
        );
        $meta_title = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '啊咿呀哟母婴商城';
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Member');
    }

    /**
     * 注册
     */
    public function register() {
        if (IS_POST) {
            //1.收集数据
            if($this->_model->create()===false){
                $this->error(get_error($this->_model->getError()));
            }
            //2.执行插入
            if($this->_model->addMember() === false){
                $this->error(get_error($this->_model->getError()));
            }  else {
             //3.提示跳转
            $this->success('注册成功',U('login')); 
            }
         } else {
            $this->display();
        }
    }
    /**
     * 激活邮箱
     * @param type $email
     * @param type $token
     */
    public function  active($email,$token){
        //1.检测数据表中是否有匹配记录 
        $cond = [
            'email'=>$email,
            'token'=>$token,
            'send_time'=>['egt',NOW_TIME-86400],//send_time + 86400 > now_time
        ];
        //2.有就修改用户状态
        //3.没错误提示  跳转到用户注册
        if(!$this->_model->where($cond)->count()){
            $this->error('验证失败',U('register'));
        }
        if($this->_model->where($cond)->setField(['status'=>1,'token'=>'','send_time'=>0])===false){
            $this->error('激活失败',U('login'));
        }
        //4.激活成功删除数据表中的token
        $this->success('激活成功',U('login'));
    }

        /**
     * 验证是否唯一
     */
    public function checkUniqueByParams(){
        $model = D('Member');
        $cond = I('get.');
        if($cond){
            if($model->where($cond)->count()){
                $this->ajaxReturn(false);
            }
        }
        $this->ajaxReturn(true);
    }
    
     /**
     * 使用ajax发送验证码.
     * @param string $telphone
     */
    public function sendSMS($telphone){
        $code = \Org\Util\String::randNumber(1000, 9999);
        //存session
        session('TEL_CAPTCHA',$code);
        //发短信
        $data = [
            'code'=>$code,
            'product'=>'TCpan运动商城',
        ];
        if(sendSMS($telphone, $data)){
            $this->success('发送成功');
        }else{
            $this->error('发送失败');
        }
    }
}
