<?php

namespace Admin\Controller;

/**
 * Description of GoodsController
 *
 * @author PR
 */
class GoodsController extends \Think\Controller {

    /**
     * 存储模型对象.
     * @var \Admin\Model\GoodsModel 
     */
    private $_model = null;

    protected function _initialize() {
        $meta_titles = array(
            'index' => '商品管理',
            'add' => '添加商品',
            'edit' => '修改商品',
            'delete' => '删除商品',
        );
        $meta_title = $meta_titles[ACTION_NAME];
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Goods');
    }

    /**
     * 商品展示 
     */
    public function index() {
        //准备搜索条件
        $keyword =I('get.keyword');
        $category =  I('get.cat_id');     
        $brand = I('get.brand_id');
        $supplier = I('get.supplier_id');
        $goods_status = I('get.goods_status');
        $is_on_sale = I('get.is_on_sale');
        $cond = array();
        if($keyword){
            $cond['name'] = array('like','%'.$keyword.'%');
        }
        if($category){
            $cond['goods_category_id'] =$category;
        }
        if($brand){
            $cond['brand_id'] = $brand;
        }
        if($supplier){
            $cond['supplier_id'] = $supplier;
        }
        if ($goods_status) {
            $cond[] = 'goods_status&'.$goods_status;
        }
        if(strlen($is_on_sale)){
            $cond['is_on_sale'] = $is_on_sale;
        }
        
        $rows = $this->_model->getPageResult($cond);
        $this->assign($rows);
        //准备品牌管理数组 id作为主键
        $brands =D('Brand')->where(array('status'=>1))->getField('id,name');
        $this->assign('brands',$brands);
        //准备分类数组 id作为主键
        $categories =D('GoodsCategory')->where(array('status'=>1))->getField('id,name');
        $this->assign('categories',$categories);
        //准备供货商管理数组 id作为主键
        $suppliers =D('Supplier')->where(array('status'=>1))->getField('id,name');
        $this->assign('suppliers',$suppliers);
        
        //是否上架
        $this->assign('is_on_sale',$this->_model->is_on_sales);
        $this->assign('goods_status',$this->_model->goods_status);
        //精品推荐
//        $cond = array();
//        $this->assign('')
        $this->display();
    }

    /**
     * 添加商品. 
     */
    public function add() {
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model->getError()));
            }
            if ($this->_model->addGoods() === false) {
                $this->error(get_error($this->_model->getError()));
            } else {
                $this->success('添加商品成功', U('index'));
            }
        } else {
            //获取商品分类的json对象
            //传递到表单
            $this->_before_view();
            $this->display('add');
        }
    }

    /**
     * 编辑商品
     */
    public function edit($id) {
        if (IS_POST) {
            //收集数据
            if($this->_model->create() === false){
                $this->error(get_error($this->_model->getError()));
            }
            if($this->_model->updateGoods()===false){
                $this->error(get_error($this->_model->getError()));
            }  else {
                $this->success('修改商品成功',U('index'));
            }
        } else {
            //获取商品信息,如果没有找到,就跳转到列表页
            if (!$row = $this->_model->getGoodsInfo($id)) {
                 $this->error('请检查商品id',U('index'));
            }
            $this->_before_view();
                $this->assign('row', $row);
                $this->display('add');
        }
    }

    /**
     * 准备分类列表用于选择父级分类,ztree插件使用的是json对象,所以传递的是json字符串.
     */
    private function _before_view() {
        //商品分类
        $categories = D('GoodsCategory')->getList('id,name,parent_id');
        $this->assign('categories', json_encode($categories));
        //品牌分类
        $brands = D('Brand')->getList('id,name');
        $this->assign('brands', $brands);
        //商品供货商
        $suppliers = D('Supplier')->getList('id,name');
        $this->assign('suppliers', $suppliers);
//        dump($suppliers);exit;
    }
     /**
       * 利用逻辑删除的方法 从页面出移除数据
       */
      public function delete($id){
          $data = array(
              'status'=>0,
              'name' =>array('exp','CONCAT(name,"_del")'),
          );
          if($this->_model->where(array('id'=>$id))->setField($data)===false){
              $this->error(get_error($this->_model->getError()));
          }  else {
              $this->success('移除成功');  
          }
      }
    
    
}
