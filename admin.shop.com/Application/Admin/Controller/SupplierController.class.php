<?php

namespace Admin\Controller;

/**
 * 供货商模块
 *
 * @author PR
 */
class SupplierController extends \Think\Controller {

    private $_model = null;

    protected function _initialize() {
        $meta_title = array(
            'index' => '供货商的管理',
            'add' => '添加供货商',
            'edit' => '修改供货商',
            'delete' => '删除供货商'
        );
        $meta_title = $meta_title[ACTION_NAME]; //获得当前控制器的名字
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Supplier');
    }

    /**
     * 供货商列表:
     * 分页
     * 搜索
     */
    public function index() {
        //获取搜索的关键字的功能
        $cond = array();
        //模糊查询供货商的名字
        $keyword = I('get.keyword');
        if($keyword){
            $cond['name'] = array('like','%'.$keyword.'%');//拼接模糊查询的条件
        }
        //创建模型对象
//         $supplier_model = D('Supplier');
        //获得数据库的数据
//        $rows = $this->_model->select();
        //传递数据
        $this->assign($this->_model->getPageResult($cond));
        //渲染视图
        $this->display();
    }

    /**
     * 供货商的添加
     */
    public function add() {
        // 判断数据是否接受
        if (IS_POST) {
            //收集数据
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model->getError()));
            }
            //保存数据并判断
            if ($this->_model->add() === false) {
                $this->error(get_error($this->_model->getError()));
            } else {
                $this->success('添加成功', U('index'));
            }
        } else {
            $this->display();
        }
    }

    /**
     * 修改供应商
     */
    public function edit($id) {
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model->getError()));
            }
            //保存数据
            if($this->_model->save() === false) {
                $this->error(get_error($this->_model->getError()));
            }  else {
                $this->success('修改成功',U('index'));    
            }
            
        } else {
            //取出的数据回显
            $row = $this->_model->find($id);
//            dump($row);exit;
            $this->assign("row", $row);
            $this->display('add');
        }
    }
    /**
     * 删除供应商 通过逻辑删除的方法
     */
    public function delete($id){
        $data = array(
            'status' =>-1,
            'name' => array('exp',"CONCAT(name,'_del')"),//数据的改变 用concat
        );
        //删除的供应商的名字后添加_del后缀
        if($this->_model->where(array('id'=>$id))->setField($data)===false){
             $this->error(get_error($this->_model->getError()));
        }  else {
             $this->success('删除成功');    
        }
    }
}
