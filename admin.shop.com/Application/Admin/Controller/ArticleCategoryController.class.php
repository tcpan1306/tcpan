<?php

namespace Admin\Controller;

/**
 * 文章分类模块
 *
 * @author PR
 */
class ArticleCategoryController extends \Think\Controller {

    private $_model = null;

    protected function _initialize() {
        $meta_title = array(
            'index' => '文章分类的管理',
            'add' => '增加分类',
            'edit' => '编辑文章分类',
            'delete' => '移除分类'
        );
        $meta_title = $meta_title[ACTION_NAME];
        $this->assign("meta_title", $meta_title);
        $this->_model = D('ArticleCategory');
    }

    //文章分类的显示
    public function index() {
        //模糊查询
        $cond = array();
        //模糊查询公户上名字
        $keyword = I('get.keyword');
        if ($keyword) {
            //拼接模糊查询条件
            $cond['name'] = array('like', '%' . $keyword . '%');
        }
        $this->assign($this->_model->getPageResult($cond));
        $this->display();
    }

    /**
     * 增加文章分类
     */
    public function add() {
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model->getError()));
            }
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
     * 编辑文章分类
     */
    public function edit($id) {
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model->getError()));
            }
            if($this->_model->save() === false){
                $this->error(get_error($this->_model->getError()));
            }  else {
                $this->success('修改成功',U('index'));    
            }
        } else {
            $row = $this->_model->find(array('id' => $id));
            //传递数据
            $this->assign('row', $row);
            $this->display('add');
        }
    }

    /**
     * 移除选定的分类名
     */
    public function delete($id){
        $data = array(
            'status' => -1,
            'name'=>array('exp','CONCAT(name,"_del")'),
        );
        if($this->_model->where(array('id'=>$id))->setField($data)===false){
            $this->error(get_error($this->_model->getError()));
        }  else {
            $this->success('移除成功',U('index'));    
        }
    }
}
