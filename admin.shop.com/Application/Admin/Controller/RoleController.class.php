<?php

namespace Admin\Controller;

/**
 * Description of RoleController
 *
 * @author PR
 */
class RoleController extends \Think\Controller {

    /**
     * 存储模型对象.
     * @var \Admin\Model\PermissionModel 
     */
    private $_model = null;

    protected function _initialize() {
        $meta_titles = array(
            'index' => '角色管理',
            'add' => '添加角色',
            'edit' => '修改角色',
            'delete' => '删除角色',
        );
        $meta_title = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Role');
    }

    public function index() {

        $rows = $this->_model->getList();
        $this->assign('rows', $rows);
        $this->display();
    }

    /**
     * 添加角色
     */
    public function add() {
        if (IS_POST) {
            //收集数据
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model->getError()));
            }
            if ($this->_model->addRole() === false) {
                $this->error(get_error($this->_model->getError()));
            }
            $this->success('添加角色成功', U('index'));
        } else {
            $this->_before_view();
            $this->display();
        }
    }

    /**
     * 修改角色
     */
    public function edit($id) {
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model->getError()));
            }
            if ($this->_model->updateRole() === false) {
                $this->error(get_error($this->_model->getError()));
            }
            $this->success('修改成功', U('index'));
        } else {
            $row = $this->_model->getRoleInfo($id);
            $this->assign('row', $row);
            $this->_before_view();
            $this->display('add');
        }
    }
    /**
     * 删除角色
     */
    
     public function delete($id){
        if($this->_model->deleteRole($id)===false){
            $this->error(get_error($this->_model->getError()));
        }
        $this->success('删除成功',U('index'));
    }

    /**
     * 准备分类列表用于选择父级分类,ztree插件使用的是json对象,所以传递的是json字符串.
     */
    private function _before_view() {
        $permission_model = D('Permission');
        $permissions = $permission_model->getList('id,name,parent_id');
        $this->assign('permissions', json_encode($permissions));
    }

}
