<?php
namespace Admin\Controller;

/**
 * Description of IndexController
 *
 * @author PR
 */
class IndexController extends \Think\Controller{
    
    public function index(){
        $this->display();
    }
    public function main(){
        $this->display();
    }
     public function top(){
        $this->display();
    }
     public function menu(){
         $menu_model = D('Menu');
        $menus = $menu_model->getMenuList();
//        dump($menus);exit;
        $this->assign('menus',$menus);
        $this->display();   
    }
}
