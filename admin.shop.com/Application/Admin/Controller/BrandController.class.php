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
        //模糊查询
        $cond = array();
        //模糊查询公户上名字
        $keyword =I('get.keyword');
        if($keyword){
            //拼接模糊查询条件
            $cond['name'] =array('like','%'.$keyword.'%');
        }
//        //获得数据
//     $rows = $this->_model->select();
        //传递数据
        $this->assign($this->_model->getPageResult($cond));
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
     
     /**
      * 品牌的修改
      */
     public function edit($id){
         //判断是否提交数据
         if(IS_POST){
             if($this->_model->create() === false){
                 $this->error(get_error($this->_model->getError()));                 
             }
             if($this->_model->save() === false){
                 $this->error(get_error($this->_model->getError()));
             }  else {
                 $this->success('编辑成功',U('index'));    
             }
         }  else {
             //没有的到数据的时候渲染视图 回显原有数据
         $row = $this->_model->find($id);
         //传递数据
         $this->assign("row",$row);
         //渲染视图
         $this->display('add');
         }
      }
      /**
       * 利用逻辑删除的方法 从页面出移除数据
       */
      public function delete($id){
          $data = array(
              'status'=>-1,
              'name' =>array('exp','CONCAT(name,"_del")'),
          );
          if($this->_model->where(array('id'=>$id))->setField($data)===false){
              $this->error(get_error($this->_model->getError()));
          }  else {
              $this->success('移除成功');  
          }
      }
}

