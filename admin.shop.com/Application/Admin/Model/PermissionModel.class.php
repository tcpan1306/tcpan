<?php

namespace Admin\Model;

/**
 * Description of PermissionModel
 *
 * @author PR
 */
class PermissionModel extends \Think\Model {

    /**
     * 添加权限
     * @return boolean
     */
    public function addPermission() {
        //创建nestedsets对象
        $nestedsets = $this->_get_nestedsets();
        //执行插入操作
        if ($nestedsets->insert($this->data['parent_id'], $this->data, 'bottom') === false) {
            $this->error = "创建失败";
            return false;
        }
        return TRUE;
    }

    /**
     * 修改权限
     */
    public function updatePermission() {
        //判断是否移动了父级权限
        $parent_id = $this->getFieldById($this->data['id'], 'parent_id');
        //如果移动了就使用nestedsets
        if ($this->data['parent_id'] != $parent_id) {
            //创建nestedsets对象
            $nestedsets = $this->_get_nestedsets();
            if($nestedsets->moveUnder($this->data['id'], $this->data['parent_id'], 'bottom') === false){
                $this->error ='不能把后代权限设定为父级权限';
                return FALSE;
            }
        }
        //否则直接保存即可
        return $this->save();
    }
    
    /**
     * 删除权限及其后代权限
     */
    public function deletePermission($id){
        $permission_info = $this->where(array('id'=>$id))->getField('id ,lft, rght ');
        if(!$permission_info){
            $this->error ='权限不存在' ;
            return false;
        }
        $cond = array(
            'lft'=>array('egt',$permission_info[$id]['lft']),
            'rght'=>array('elt',$permission_info[$id]['rght']),
        );
        $this->where($cond)->setField('status',0);
    }

        /**
     * 获取nestedsets对象 
     */
    private function _get_nestedsets() {
        //创建nestedsets 所需的数据库对象
        $db_obj = D('DbMySql', 'Logic');
        //创建nestedsets对象
        return new \Admin\Service\NestedSets($db_obj, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
    }

    /**
     * 获取所有的可用分类列表.
     */
    public function getList($file = '*') {
        return $this->field($field)->order('lft asc')->where(array('status' => 1))->select();
    }

}
