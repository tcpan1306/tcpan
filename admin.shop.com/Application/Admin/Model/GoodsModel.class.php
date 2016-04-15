<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Model;

/**
 * Description of GoodsModel
 *
 * @author PR
 */
class GoodsModel extends \Think\Model {
   
    //商品状态
    public $goods_status = array(
        1=>'精品',
        2=>'新品',
        3=>'热销'
     );
    //商品的售卖状态
    public $is_on_sales = array(
        1=>'上架',
        2=>'下架'
    );

    /**
     * 自动验证
     */
    protected $_validate = array(
        //商品名称不能为空
        //商品分类不能为空
        //商品的库存不能为空
        //商品的价格不能为空
        //售价不能为空
        array('name', 'requIre', '商品名称不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('goods_category_id', 'require', '商品分类不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('stock', 'require', '商品库存不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('shop_price', 'require', '市场价不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('market_price', 'require', '售价不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
    );

    /**
     * 自动完成添加 在插入时候将商品状态执行按位或
     * 添加的时间当时当前时间
     */
    protected $_auto = array(
        array('goods_status', 'array_sum', self::MODEL_INSERT, 'function'),
        array('inputtime', NOW_TIME, self::MODEL_INSERT),
    );

    /**
     * 新增商品.
     * 1保存基本信息
     * 1.1计算货号
     * 保存详细描述
     * TODO:保存相册信息
     */
    public function addGoods() {
        unset($this->data['id']);
        //自动计算货号
        $this->_calc_sn();
        //保存基本信息
        //exit;
        $goods_id = $this->add();
        if ($goods_id === false) {
            return false;
        };
        //保存详细描述
        if ($this->_save_goods_content($goods_id) === false) {
            $this->error = '保存商品详尽信息失败';
            return false;
        }
        //保存相册信息
        if($this->_save_gallery($goods_id)===FALSE){
            $this->error = '保存相册失败';
            return FALSE;
        }
        return true;
    }

   
      
    /**
     * 分页  模糊查询
     */
    public function getPageResult(array $cond = array()) {

        $cond = $cond + array(
            'status' => 1, //status的值要大于-1蔡显示
        );
        //获取总行数
        $count = $this->where($cond)->count();
//        echo $this->getLastSql();
//        exit;
        //获取显示多少页
        $size = C('PAGE_SIZE');
        $page_obj = new \Think\Page($count, $size);
        $page_obj->setConfig("theme", C('PAGE_THEME'));
        $page_html = $page_obj->show();
        $pageIndex=I('get.p');
        $pageIndex=$pageIndex>ceil($count/$size)?ceil($count/$size):$pageIndex;
        $rows = $this->where($cond)->page($pageIndex, $size)->select();
        foreach ($rows as $key=>$value){
            $rows[$key]['is_best'] = $value['goods_status'] & 1?1:0;
            $rows[$key]['is_new'] = $value['goods_status'] & 2?1:0;
            $rows[$key]['is_hot'] = $value['goods_status'] & 4?1:0;
        }
        return array(
            'rows' => $rows,
            'page_html' => $page_html
        );
    }
    
    /**
     * 通过商品的id获取详细信息.
     * 基本信息
     * 商品详细描述
     * TODO:商品的相册
     * @param integer $goods_id
     */
    public function getGoodsInfo($goods_id){
        $cond = array(
            'status'=>1,
        );
        $row = $this->where($cond)->find($goods_id);
        if(empty($row)){
            $this->error = '商品不存在';
            return false;
        }
        //回显数据 将goods_status 数据处理后 交由模板
        $tmp_status = $row['goods_status'];  
        $row['goods_status'] = array();
        if($tmp_status & 1){
            $row['goods_status'][] = 1;
        }
        if($tmp_status & 2){
            $row['goods_status'][] = 2;
        }
        if($tmp_status & 4){
            $row['goods_status'][] = 4;
        }
        $row['goods_status'] = json_encode($row['goods_status']);
        //取得详尽信息
        $content = M('GoodsIntro')->getFieldByGoodsId($goods_id,'content');
        $row['content'] =$content ? $content:'';
        //取得相册内容
        $paths = M('GoodsGallery')->where(array('goods_id'=>$goods_id))->getField('id,id,path',true);
        $row['paths'] = $paths ? $paths : array();
        return $row;
    }

    /**
     * 保存修改的数据
     */
    
public function updateGoods(){
    $request_data = $this->data;
    //保存基本信息
    if($this->save()===false){
        return false;
    }
    //保存详细信息
    if($this->_save_goods_content($request_data['id'],false) === false){
        $this->error = '保存商品详细描述失败';
        return false;
    }
    //保存相册信息
    if($this->_save_gallery($request_data['id'])===false){
        $this->error = '保存相册失败';
        return FALSE;
    }
    return true;
}

 /**
     * 保存详细描述
     */
    private function _save_goods_content($goods_id,$is_new=true) {
        $content = I('post.content', '', false);
        $data = array(
            'goods_id' => $goods_id,
            'content' => $content
        );
        if($is_new){
             return M('GoodsIntro')->add($data);
        }  else {
           return M('GoodsIntro')->save($data);    
        }
       
    }
    /**
     * 获取所有的可用分类列表.
     */
    public function getList($file = '*') {
        return $this->field($field)->where(array('status' => 1))->select();
    }

    /**
     * 自动计算货号
     */
    private function _calc_sn() {
        //货号计算
        $sn = $this->data['sn'];
        //如果没有传递货号 就生成SN年月日当天第几个商品
        if (empty($sn)) {
            $day = date('Ymd');
//            dump($day);
//            $sn = 'SN' . $day;
            $goods_count_model = M('GoodsDayCount');
            //如果是今天的一个商品 就掺入一条数据
            if (!($count = $goods_count_model->getFieldByDay($day, 'count'))) {
                $count = 1;
                $data = array(
                    'day' => $day,
                    'count' => $count,
                );
                $goods_count_model->add($data);
            } else {
                $count++;
                $goods_count_model->where(array('day' => $day))->setInc('count', 1);
            }
        }
        $this->data['sn'] = 'SN' . $day . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
    
    /**
     * 保存相册
     */
    
    private function _save_gallery($goods_id){
        $paths = I('post.path');
        if(!$paths){
            return true;
        }
        $gallery_model = M('GoodsGallery');
        //用于保存所有图片
        $data = array();
        foreach ($paths as $path){
            $data[]=array(
                'goods_id'=>$goods_id,
                'path'=>$path,
            );
        }
        return $gallery_model->addAll($data);
    }
}
