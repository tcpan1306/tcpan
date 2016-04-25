<?php
namespace Home\Controller;

/**
 * Description of ShoppingCarController
 *
 * @author PR
 */
class ShoppingCarController extends \Think\Controller{
    /**
     * @var \Home\Model\ShoppingCarModel 
     */
    private $_model = null;
    protected function _initialize(){
        $this->_model = D('ShoppingCar');
    }
    public function flow1() {
        //取出购物车数据
        $rows = $this->_model->getShoppingCar();
        $this->assign($rows);
        $this->display();
    }
    public function flow2() {
        check_login();
        $this->display();
    }
}
