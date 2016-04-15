<?php

namespace Admin\Model;

/**
 * 供应商管理的model
 *
 * @author PR
 */
class SupplierModel extends \Think\Model {
    //自动验证供应商是否合法
    protected  $_validate = array(
        /**
         * 验证名字必填
         * 验证名字是否唯一
         */
     array('name','require','供应商不能为空',self::EXISTS_VALIDATE,'',self::MODEL_INSERT),
     array('name','','供应商已经存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
    );
    
    /**
     * 要求不使用数组函数,进行数组合并,要求如果元素键名冲突,就以第一个为准
     */
    public function getPageResult(array $cond=array()){
        $cond = $cond + array(
            'status' =>array('gt',-1),
        );
       //获取总行数
        $count = $this->where($cond)->count();
        //获取页尺寸
        $size = C('PAGE_SIZE');
        $page_obj = new \Think\Page($count,$size);
        $page_obj->setConfig("theme",C("PAGE_THEME"));
        $page_html = $page_obj->show();
        $rows = $this->where($cond)->page(I('get.p'),$size)->select();
        return array(
            'rows' =>$rows,
            'page_html' =>$page_html,
        );
    }
    /**
     * 获取所有的可用分类列表.
     */
    public function getList($file = '*') {
        return $this->field($field)->where(array('status' => 1))->select();
    }
}
