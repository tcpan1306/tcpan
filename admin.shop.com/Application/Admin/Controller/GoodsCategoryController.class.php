<?php

namespace Admin\Controller;

/**
 * Description of GoodsCategoryController
 *
 * @author PR
 */
class GoodsCategoryController extends \Think\Controller {

    /**
     * 存储模型对象.
     * @var \Admin\Model\GoodsCategoryModel 
     */
    private $_model = null;

    protected function _initialize() {
        $meta_titles = array(
            'index' => '商品分类管理',
            'add' => '添加商品分类',
            'edit' => '修改商品分类',
            'delete' => '删除商品分类',
        );
        $meta_title = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
        $this->_model = D('GoodsCategory');
    }

    /**
     * 列表页面.
     */
    public function index() {
        $rows = $this->_model->getList();
        $this->assign('rows', $rows);
        $this->display();
    }

    /**
     * 添加商品.
     */
    public function add() {
         //判断数据是否提交
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model->getError()));
            }
            //添加节点
            //提示条状
            if ($this->_model->addCategory() === false) {
                $this->error(get_error($this->_model->getError()));
            } else {
                $this->success('添加成功', U('index'));
            }
        } else {
            $this->_before_view();
            $this->display();
        }
    }

    /**
     * 编辑修改方法.
     * @param type $id
     */
    public function edit($id) {
        //判断是否提交数据
        if (IS_POST) {
            //收集数据
            //调用updateCategory的编辑方法
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model->getError()));
            }
            if ($this->_model->updateCategory() === false) {
                $this->error(get_error($this->_model->getError()));
            }
            $this->success('修改成功', U('index'));
        } else {
            //查找数据
            $row = $this->_model->find($id);
            //传送数据
            $this->assign('row', $row);
            $this->_before_view();
            //渲染视图
            $this->display('add');
        }
    }

    /**
     * 逻辑删除商品
     * @param int $id
     */
    public function delete($id) {
        if ($this->_model->deleteCategory($id) === false) {
            $this->error(get_error($this->_model->getError()));
        }
        $this->success('删除成功', U('index'));
    }

    /**
     * 准备分类列表用于选择父级分类,ztree插件使用的是json对象,所以传递的是json字符串.
     */
    private function _before_view() {
        $categories = $this->_model->getList('id,name,parent_id');
        array_unshift($categories, array('id' => 0, 'name' => "顶级分类", 'parent_id' => 0));
        $this->assign('categories', json_encode($categories));
    }

}
