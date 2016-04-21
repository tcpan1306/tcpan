<?php
namespace Admin\Model;

/**
 * Description of AdminModel
 *
 * @author PR
 */
class AdminModel extends \Think\Model{
    //自动验证
    public $_validate = array(
        /**
         * username 不能为空 唯一  长度2-16位
         * password 不能为空 长度6-16位
         * email 不能为空 email格式
         */
        array('username','require','用户名不能为空哟',self::EXISTS_VALIDATE,'',self::MODEL_INSERT),
        array('username','','用户名已经存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
        array('username','2,16','用户名长度不合法',self::EXISTS_VALIDATE,'length',self::MODEL_INSERT),
        array('password','require','密码不能为空',self::EXISTS_VALIDATE,'',self::MODEL_INSERT),
        array('password','6,16','密码长度不合法',self::EXISTS_VALIDATE,'length',self::MODEL_INSERT),
        array('email','require','邮箱不能为空',self::EXISTS_VALIDATE,'',self::MODEL_INSERT),
        array('email','email','邮箱不合法',self::EXISTS_VALIDATE,'',self::MODEL_INSERT),
        //登录验证
        array('username', 'require', '用户名必填', self::MUST_VALIDATE, '', 'login'),
        array('password', 'require', '密码必填', self::MUST_VALIDATE, '', 'login'),
        array('captcha', 'require', '验证码必填', self::MUST_VALIDATE, '', 'login'),
        array('captcha', 'check_captcha', '验证码不正确', self::MUST_VALIDATE, 'callback', 'login'),
    );
    protected $_auto = array(
        array('salt','\Org\Util\String::randString',self::MODEL_INSERT,'function',6),
        array('salt', '\Org\Util\String::randString', 'reset_pwd', 'function', 6),//当重置密码是自动生成一个盐
        array('add_time',NOW_TIME,self::MODEL_INSERT),
    );
    /**
     *验证验证码
     */
    public function check_captcha($code) {
        $verify = new \Think\Verify();
        return $verify->check($code);
    }
    
    /**
     * 管理员-角色关联模型.
     * @var \Think\Model 
     */
    private $_admin_role_model = null;
    
    /**
     * 管理员-权限关联模型.
     * @var \Think\Model 
     */
    private $_admin_permission_model = null;

    /**
     * 重写父类的构造方法,创建了两个基本都会使用到的模型对象.
     * @param type $name
     * @param type $tablePrefix
     * @param type $connection
     */
    public function __construct($name = '', $tablePrefix = '', $connection = '') {
        parent::__construct($name, $tablePrefix, $connection);
        $this->_admin_role_model = M('AdminRole');
        $this->_admin_permission_model = M('AdminPermission');
    }

    /**
     * 获取所有的可用分类列表.
     */
    public function getList($file = '*') {
        return $this->field($field)->where(array('status' => 1))->select();
    }
    /***
     * 添加用户
     */
    public function addAdmin(){
        unset($this->data['id']);
        //保存基本信息
        $this->data['password'] = salt_password($this->data['password'],  $this->data['salt']);
        if(($admin_id = $this->add())== false){
            return false;
        }
        //保存关联角色
      if( $this->_save_role($admin_id) === false){
          $this->error = '保存角色失败';
            return false;
      };
       //保存关联权限
      if ($this->_save_permission($admin_id) === false) {
            $this->error = '保存额外权限失败';
            return false;
        }
        return true;
    }
    /**
     * 获得管理员详细信息 包括角色 权限
     * @param type $id
     * @return boolean
     */
    public function getAdminInfo($id){
       //获得一条数据
        $row = $this->find($id);
        if(empty($row)){
            $this->error='管理员不存在';
            return false;
         }
       //权限与角色model
         
          $role_ids  = $this->_admin_role_model->where(array('admin_id' => $id))->getField('role_id', true);
        $row['role_ids'] = $role_ids;;
        
         $permission_ids        = $this->_admin_permission_model->where(array('admin_id' => $id))->getField('permission_id', true);
        $row['permission_ids'] = json_encode($permission_ids);
        return $row;
    }
    public function updateAdmin() {
        $request_data = $this->data;
        //保存角色关联
        if ($this->_save_role($request_data['id'], false) === false) {
            $this->error = '更新角色关联失败';
            return false;
        }
        //保存权限关联
        if ($this->_save_permission($request_data['id'], false) === false) {
            $this->error = '更新权限关联失败';
            return false;
        }
        return true;
    }
    /**
     * 删除管理员 并删除关联的角色
     * @param type $id
     */
    public function deleteAdmin($id) {
        if($this->delete($id)===false){
            return false;
        }
        if($this->_delete_role($id)===false){
            $this->error = '删除角色关联失败';
            return false;
        }
        if($this->_delete_permission($id)===false){
            $this->error = '删除权限关联失败';
            return false;
        }
        return true;
    }
    
    /** 
     * 保存角色关连  如果修改 先删除原来的
     * @param type $admin_id
     * @return boolean
     */
    private function _save_role($admin_id,$is_new=true){
//        $model = M('AdminRole');
        if(!$is_new){
            if($this->_delete_role($admin_id)===false){
                return false;
            }
         }
        //获取勾线的角色,如果没有勾选任何角色,就不再执行添加操作.
        $roles = I('post.role');
        if(empty($roles)){
            return true;
        }
        $data = array();
        foreach ($roles as $role ){
            $data[]=array(
                'admin_id'=>$admin_id,
                'role_id'=>$role,
            );
        }
        return $this->_admin_role_model->addAll($data);
    }
    
    /**
     * 保存权限 
     * @param type $admin_id 管理员id.
     * @return boolean
     */
    private function _save_permission($admin_id,$is_new=true){
        if (!$is_new) {
            if($this->_delete_permission($admin_id)===false){
                return false;
            }
        }
        //获取勾线的角色,如果没有勾选任何角色,就不再执行添加操作.
        $perms = I('post.perm');
        if(empty($perms)){
            return true;
        }
         $data = array();
        foreach ($perms as $perm ){
            $data[]=array(
                'admin_id'=>$admin_id,
                'permission_id'=>$perm
            );
        }
        return  $this->_admin_permission_model->addAll($data);
    }
    
   private function _delete_role($admin_id) {
        if ($this->_admin_role_model->where(array('admin_id' => $admin_id))->delete() === false) {
            return false;
        }
    }
    
    private function _delete_permission($admin_id) {
        if ($this->_admin_permission_model->where(array('admin_id' => $admin_id))->delete() === false) {
            return false;
        }
    }
    /**
     * 重置密码
     * @return type
     */
    public function resetPwd(){
        //获取数据
        $password = I('post.password');
        if(empty($password)){
            //使用系统自带的随机字符串生成方法生成
            $len = mt_rand(8, 10);
            $password = \Org\Util\String::randString($len,'','_?/]][[');
        }
        $this->data['password']=  salt_password($password, $this->data['salt']);
        return $this->save()?$password:false;
    }
    /**
     * 验证验证码
     * 验证用户名是否存在
     * 验证密码是否匹配
     */
    public function login(){
        //为了安全我们将用户信息都删除
        session('USERINFO',null);
        $request_data = $this->data;
       //验证用户是否存在
        $userinfo = $this->getByUsername($this->data['username']);
        if(empty($userinfo)){
            $this->error ='用户不存在';
            return false;
        }
        //验证密码是否匹配
        $password = salt_password($request_data['password'], $userinfo['salt']);
        if($password !=$userinfo['password']){
            $this->error = '密码不正确';
            return false;
        }
        //为了后续回话获取用户信息 我们使用session保存
        session("USERINFO",$userinfo);
        $this->_getPermission($userinfo['id']);
        
        //保存自动登录信息
        $this->_saveToken($userinfo['id']);
        return true;
    }
    
    /**
     * 判断用户是否需要自动登陆,如果需要就保存令牌到cookie和数据表中. 
     * $admin_id  管理员id
     * 
     */
   
    private function _saveToken($admin_id){
        $token_model = M('AdminToken');
        //清空原有的令牌
        cookie('AUTO_LOGIN_TOKEN',null);
        $token_model->delete($admin_id);
        //判断是否需要自动登陆
        $remember = I('post.remember');
        if($remember){
            return true;
        }
        $data = [
            'admin_id'=>$admin_id,
            'token'=>  sha1(mcrypt_create_iv(32)),//随机字符串
        ];
        //存到cookie和数据表中
        cookie('AUTO_LOGIN_TOKEN',$data,604800);
        return  $token_model->add($data);
    } 
 
    /**
     * 检查令牌信息是否匹配
     */
    public function autoLogin(){
        $data = cookie('AUTO_LOGIN_TOKEN');
        $token_model = M('AdminToken');
        if(!$token_model->where($data)->count()){
            return false;
        }
        //发现原令牌就应当失效
        cookie('AUTO_LOGIN_TOKEN',null);
        $token_model->delete($data['admin_id']);
        
        //获取用户信息保存到session中
        $userinfo = $this->find($data['admin_id']);
         session("USERINFO",$userinfo);
         
         //为了安全 我们把令牌重新生成一次
         $data = [
            'admin_id'=>$data['admin_id'],
            'token'=>  sha1(mcrypt_create_iv(32)),//随机字符串
        ];
        //存到cookie和数据表中
        cookie('AUTO_LOGIN_TOKEN',$data,604800);
        if($token_model->add($data)===false){
            return false;
        }  else {
            //发现操作都成功就回去用户权限信息
            $this->_getPermission($data['admin_id']); 
            return true;
        }
    }

        /**
     * 
     * @param type $admin_id 用户id.
     */
    private function  _getPermission($admin_id ){
        session('PATHS',NULL);
        session('PERM_IDS',NULL);
        /**
         * SELECT DISTINCT path from admin_permission ap LEFT JOIN permission p ON ap.`permission_id`=p.`id` WHERE admin_id=1 and path<>''
         * UNION
         * SELECT DISTINCT path FROM admin_role ar LEFT JOIN role_permission rp on ar.`role_id`=rp.`role_id` LEFT JOIN permission p ON rp.`           * permission_id`=p.id WHERE admin_id=1 and path<>'';
         */
        //获取通过角色得到的权限
        $role_permissions =  $this->distinct(true)->table('__ADMIN_ROLE__ as ar')->join('__ROLE_PERMISSION__ as rp ON ar.`role_id`=rp.`role_id`')->join('__PERMISSION__ as p ON rp.`permission_id`=p.`id`')->where(['admin_id'=>$admin_id ,'path'=>['neq','']])->getField('permission_id,path',true);
        //获取额外权限
        $admin_permissions = $this->distinct(true)->table('__ADMIN_PERMISSION__ as ap')->join('__PERMISSION__ as p ON ap.`permission_id` = p.`id`')->where(['admin_id'=>$admin_id ,'path'=>['neq','']])->getField('permission_id,path',true);
        //由于前面获取的都是关联数组,+合并会自动合并键名相同的元素,也就等同于做了去重
        $role_permssions=$role_permssions?:[];
        $admin_permissions=$admin_permissions?:[];
        $permissions = $role_permissions+$admin_permissions;
        //获取权限id列表
        $permission_ids = array_keys($permissions);
        //获取权限列表
        $paths = array_values($permissions);
//        dump($permission_ids);
//        dump($paths);exit;
        session('PATHS',$paths);
        session('PERM_IDS',$permission_ids);
    }
}
