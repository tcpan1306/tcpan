<?php


namespace Admin\Controller;

/**
 * Description of PermissionController
 *
 * @author PR
 */
class PermissionController extends \Think\Controller {
    
    /**
     * 存储模型对象.
     * @var \Admin\Model\PermissionModel 
     */
    private $_model = null;

    protected function _initialize() {
        $meta_titles = array(
            'index' => '权限管理',
            'add' => '添加权限',
            'edit' => '修改权限',
            'delete' => '删除权限',
        );
        $meta_title = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Permission');
    }
    
    public function add(){
        
    }
}
