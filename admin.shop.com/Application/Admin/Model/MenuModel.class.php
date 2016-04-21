<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Model;

/**
 * Description of MenuModel
 *
 * @author PR
 */
class MenuModel extends \Think\Model {

    /**
     * 自动验证规则.
     * @var array 
     */
    protected $_validate = array(
        array('name', 'require', '菜单名称必填', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
    );

    /**
     * 获取所有的可用分类列表.
     */
    public function getList($file = '*') {
        return $this->field($field)->where(array('status' => 1))->order('lft')->select();
    }

    /**
     * 添加菜单.
     * 保存基本信息
     * 保存菜单与权限的关系
     */
    public function addMenu() {
        $nestedsets = $this->_get_nestedsets();
        if ($menu_id = $nestedsets->insert($this->data['parent_id'], $this->data, 'bottom') === false) {
            $this->error = '添加菜单失败';
            return false;
        }
        //保存权限关系
        if ($this->_save_permission($menu_id) === falsse) {
            $this->error = '保存权限失败';
            return false;
        }
        return true;
    }

    public function getMenuInfo($id) {
        $row = $this->where(['status' => 1])->find($id);
        if (empty($row)) {
            $this->error = '菜单不存在';
            return false;
        }
        //获取权限列表
        $menu_permission_model = M('MenuPermission');
        $cond = [
            'menu_id' => $id,
        ];
        $permission_ids = $menu_permission_model->getField('permission_id', true);
        $row['permission_ids'] = json_encode($permission_ids);
        return $row;
    }

    public function updateMenu() {
        //保存数据
        $request_data = $this->data;
        //判断是否修改了父级菜单如果修改了才会使用nestedsets
        $parent_id = $this->where(['id' => $this->data['id']])->getField('parent_id');
        if ($parent_id != $this->data['parent_id']) {
            $nestedsets = $this->_get_nestedsets();
            if ($nestedsets->moveUnder($this->data['id'], $this->data['parent_id'], 'bottom') === false) {
                $this->error = '不能将菜单移动到其后代菜单下';
                return false;
            }
        }
        //保存基本信息
        if($this->save()===false){
            return false;
        }
        //保存权限关系
        if($this->_save_permission($request_data['id']) === false){
            $this->error = '保存权限失败';
            return false;
        }
        return true;
    }
    /**
     * 删除菜单及后代菜单
     * @param type $id
     */
    public function deleteMenu($id) {
        //获取到菜单的左右节点
        $menu_info = $this->where(['status'=>1,'id'=>$id])->getField('id,lft,rght');
         $cond = [
            'lft'=>array('egt',$menu_info[$id]['lft']),
            'rght'=>array('elt',$menu_info[$id]['rght']),
        ];
      return $this->where($cond)->setField('status',0);
    }

    /**
     * 使用nestedsets插件 计算左右节点
     * @return \Admin\Service\NestedSets
     */
    private function _get_nestedsets() {
        $mysql_db = D('DbMySql', 'Logic');
        return new \Admin\Service\NestedSets($mysql_db, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
    }

    /**
     * 保存菜单-权限 
     * @param type $admin_id 管理员id.
     * @return boolean
     */
    private function _save_permission($menu_id, $is_new = true) {
        //获取权限列表
        $perms = I('post.perm');
        if (empty($perms)) {
            return false;
        }
        //生成一个数组
        $data = array();
        foreach ($perms as $perm) {
            $data[] = array(
                'menu_id' => $menu_id,
                'permission_id' => $perm
            );
        }
        //添加
        $model = M('MenuPermission');
        if (!$is_new) {
            $model->where(['menu_id' => $menu_id])->delete();
        }
        return $model->addAll($data);
    }

    /**
     * 获取当前登录用户所能看见的菜单列表
     */
    public function getMenuList() {
       //获取用户权限id列表
      $permission_ids = session('PERM_IDS');
      if(empty($permission_ids)){
            return [];
        }
        //SELECT path,NAME,LEVEL FROM menu_permission AS mp LEFT JOIN menu AS m ON mp.`menu_id`=m.`id` WHERE permission_id IN (6,16,17,28)
        $cond = [
            'permission_id'=>['in',$permission_ids],
        ];
        //alias 别名
        $menus = $this->distinct(true)->alias('m')->field('id,path,name,level,parent_id')->join('__MENU_PERMISSION__ as mp ON mp.`menu_id`=m.`id`')->where($cond)->select();
//        dump($menus);exit;
        return $menus;
    }
}
