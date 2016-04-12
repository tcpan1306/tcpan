<?php


namespace Admin\Model;

//use Think;

/**
 * Description of GoodsCategoryModel
 *
 * @author PR
 */
class GoodsCategoryModel extends \Think\Model{
    /**
     * 获取所有的可用分类列表.
     */
    public function getList($file = '*'){
        return $this->field($field)->where(array('status'=>1))->select();
    }
    public function addCategory(){
        return $this->add();
    }
}
