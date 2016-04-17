<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Model;

/**
 * Description of RoleModel
 *
 * @author PR
 */
class RoleModel extends \Think\Model{
    
    /**
     * 自动验证
     * @var array 
     */
    protected  $_validate = array(
        array('name','require','角色名不能为空',self::EXISTS_VALIDATE,'',self::MODEL_INSERT),
    );
    
    /**
     * 添加角色 保存角色和权限关联
     */
    public function addRole() {
        $role_id = $this->add();
        //保存基本信息
        if($role_id === false){
            return false;
        }
        //保存角色-权限关联
        if($this->_save_permission($role_id)===false){
            $this->error = '保存权限失败';
            return false;
        }
        return true;
    }
    /**
     * 保存角色和权限的关联关系
     * @param integer $role_id  角色id
     * @param type $is_new       是添加还是修改 如果修改先删除原来数据
     * @return boolean
     */
    private function _save_permission($role_id,$is_new=true){
        $perms = I('post.perm');
        if(empty($perms)){
            echo 2;
            return true;
        }
        $data = array();
        foreach ($perms as $perm){
            $data[]  = array(
              'role_id' =>$role_id,
              'permission_id'=>$perm,
            );
         }
//         dump($data);exit;
         //创建角色 与权限关联表的模型
         $model = M('RolePermission');
          if(!$is_new){
              $model->where(array('role_id'=>$role_id))->delete();
         }
         return M('RolePermission')->addAll($data);
    }
     
    /**
     * 获取角色详情,包括权限.
     */

    public function getRoleInfo($id){
        //获得修改的数据
        $row = $this->where(array('status'=>1))->find($id);
        if(empty($row)){
            $this->error = '角色不存在';
            return false;
        }
        //权限与角色model
        $permission_model = M('RolePermission');
        $permission_ids = $permission_model->where(array('role_id'=>$id))->getField('permission_id',true);
        $row['permission_ids'] = json_encode($permission_ids);
        return $row;
    }
    /**
     * 修改角色并保存关联的权限
     */
    public function updateRole(){
     $request_data = $this->data;
        //保存基本信息
     if($this->save()===false){
         return false;
     }
     //保存关联关系
     if($this->_save_permission($request_data['id'],FALSE)===false){
         $this->error = '保存权限失败';
            return false;
     }
     return true;
    }
    /**
     * 逻辑删除 不删除关联权限
     */
    public function deleteRole($id){
         return $this->where(array('id'=>$id))->setField('status',0);
    }

    /**
     * 获取所有的可用分类列表.
     */
    public function getList($file = '*') {
        return $this->field($field)->where(array('status' => 1))->select();
    }
}
