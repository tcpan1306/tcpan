<?php

namespace Admin\Controller;

/**
 * 供货商模块
 *
 * @author PR
 */
class SupplierController extends \Think\Controller{
     /**
     * 供货商列表:
     * 分页
     * 搜索
     */
     public function index(){
         //创建模型对象
         $supplier_model = D('Supplier');
         //获得数据库的数据
         $rows = $supplier_model->select();
         //传递数据
         $this->assign('rows',$rows);
         //渲染视图
         $this->display();
     }
}
