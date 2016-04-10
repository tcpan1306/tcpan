<?php

namespace Admin\Controller;

/**
 * 品牌模块
 *
 * @author PR
 */
class BrandController extends \Think\Controller {

    private $_model = null;

    protected function _initialize() {
        $meta_title = array(
            'index' => '品牌的管理',
            'add' => '增加品牌',
            'edit' => '编辑品牌',
            'delete' => '移除品牌'
        );
        $meta_title = $meta_title[ACTION_NAME];
        $this->assign("meta_title",$meta_title);
        $this->_model = D('Brand');
    }

    /**
     * 品牌的展示  index方法
     */
    public function index() {
        //获得数据
     $rows = $this->_model->select();
        //传递数据
        $this->assign('rows',$rows);
        //渲染视图
        $this->display();
    }
     /**
      * 品牌增加 add方法
      */
     public function add(){
         //判断数据是否提交
         if(IS_POST){
             if($this->_model->create() === false){
                 $this->error(get_error($this->_model->getError()));
             }
             if($this->_model->add() === false){
                  $this->error(get_error($this->_model->getError()));
             }  else {
                 $this->success('添加品牌成功',U('index'));    
             }
             
         }  else {
         //渲染视图
         $this->display();
         }
     }
}

