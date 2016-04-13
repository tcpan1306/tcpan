<?php

namespace Admin\Model;

//use Think;

/**
 * Description of GoodsCategoryModel
 *
 * @author PR
 */
class GoodsCategoryModel extends \Think\Model {
    /**
     * 自动验证 商品名不能为空
     */
    protected $_validate = array(
        array('name','require','商品名不能为空',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
    );

    /**
     * 获取所有的可用分类列表.
     */
    public function getList($file = '*') {
        return $this->field($field)->order('lft asc')->where(array('status' => 1))->select();
    }

    /**
     * 添加商品的方法.
     * @return type
     */
    public function addCategory() {
        // dump($this->data);exit;
        // dump($this->trueTableName);exit;//获得表名  根据控制器的名字
        //使用我们的NestedSets计算左右节点和层级
        $mysql_db = new \Admin\Logic\DbMySqlLogic();
        $nestedsets = new \Admin\Service\NestedSets($mysql_db, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
        //计算左右节点和层级并保存 
        return $nestedsets->insert($this->data['parent_id'], $this->data, 'bottom');
    }

    /**
     * 编辑商品.
     * @return boolean
     */
    public function updateCategory() {
        //获取原来的父级节点
        $parent_id = $this->getFieldById($this->data['id'], 'parent_id');
        if ($parent_id != $this->data['parent_id']) {
            //重新计算左右节点和层级
            //实例化nestedsets所需要的数据库连接
            $mysql_db = new \Admin\Logic\DbMySqlLogic();
            $nestedsets = new \Admin\Service\NestedSets($mysql_db, $this->trueTableName, 'lft', 'rght', 'parent_id', 'id', 'level');
            if ($nestedsets->moveUnder($this->data['id'], $this->data['parent_id'], 'bottom') === false) {
                $this->error = '不能将当前分类移动到其后代分类';
                return false;
            }
        }
        return $this->save();
    }

    /**
     * 逻辑删除.
     */
    public function deleteCategory($id) {
        //获取到所有的后代分类
        //获取当前分类的左右节点
        $category = $this->where(array('id' => $id))->getField("id ,lft,rght");
        //  dump($category);exit;
        $cond = array(
            'lft' => array('egt', $category[$id]['lft']),
            'rght' => array('elt', $category[$id]['rght'])
        );
        return $this->where($cond)->setField('status', 0);
    }

}
