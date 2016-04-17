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
    );
    protected $_auto = array(
        array('salt','\Org\Util\String::randString',self::MODEL_INSERT,'function',6),
        array('salt', '\Org\Util\String::randString', 'reset_pwd', 'function', 6),//当重置密码是自动生成一个盐
        array('add_time',NOW_TIME,self::MODEL_INSERT),
    );

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
}
