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
    /**
     * 权限列表
     */
    public function index(){
        $rows = $this->_model->getList();
        $this->assign('rows',$rows);
        $this->display();
    }

    /**
     * 添加权限
     */
    public function add(){
        if(IS_POST){
            //收集数据
            if($this->_model->create() === false){
                 $this->error(get_error($this->_model->getError()));
            }
            if($this->_model->addPermission() === false){
                $this->error(get_error($this->_model->getError()));
            }
            $this->success('添加权限成功',U('index'));
        }  else {
            $this->_before_view();
            $this->display();   
        }
         
    }
    
    /**
     *  权限的修改 
     */
    public function edit($id){
        if(IS_POST){
         if($this->_model->create() === false){
             $this->error(get_error($this->_model->getError()));
         }   
         if($this->_model->updatePermission() === false){
             $this->error(get_error($this->_model->getError()));
         }
         $this->success('修改成功',U('index'));
        }
        else{
        //显示分类信息
        $this->_before_view();
        //获得数据回显
       $row = $this->_model->where(array('status'=>1))->find($id);
       $this->assign('row',$row);
       $this->display('add');
        }
    }
    
    /**
     * 删除权限及其后代权限 
     */
     
    public function delete($id){
        if($this->_model->deletePermission($id) === false){
            $this->error(get_error($this->_model->getError()));
        }
        $this->success('删除成功',U('index'));
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
