<?php

namespace Home\Model;

/**
 * Description of GoodsModel
 *
 * @author PR
 */
class GoodsModel extends \Think\Model {

    public function getGoodsListByGoodsStatus($goods_status, $limit = 5) {
        $cond = [
            'goods_status&' . $goods_status,
            'status' => 1,
            'is_on_sale' => 1
        ];
        return $this->field('id,name,logo,shop_price')->where($cond)->select();
    }

    /**
     * 获取商品详尽信息
     * 包括相册 详细描述
     * @return type
     */
    public function getGoodsInfo($id) {
        $cond = [
            'status' => 1,
            'is_on_sale' => 1,
            'id' => $id
        ];
        $row = $this->where($cond)->find();
//        dump($row);exit;
        if(!$row){
             $this->error = '您所查找的商品离家出走了';
            return false;            
        }
        //获得品牌名称
        $row['brand_name'] = M('Brand')->where(['id'=>$row['brand_id']])->getField('name');
        //获取详尽描述
        $row['content'] = M('GoodsIntro')->where(['goods_id'=>$id])->getField('content');
        //获取相册列表
        $row['galleries'] = M('GoodsGallery')->where(['goods_id'=>$id])->getField('path',true);
//        dump($row);exit;
        return $row;
        }
        /**
         * 获取商品点击数,并且保存到数据库中
         * @param integer $goods_id 商品id.
         * @return int
         */
        public function getGoodsClick($goods_id){
            $goods_click_model = M('GoodsClick');
            $count = $goods_click_model->getFieldByGoodsId($goods_id,'click_times');
            if(empty($count)){
                $goods_click_model->add(['goods_id'=>$goods_id,'click_times'=>1]);
                return 1;
            }  else {
            $goods_click_model->where(['goods_id'=>$goods_id])->setInc('click_times',1);    
            return $count;
            }
        }
        public function getGoodsClickFromRedis($goods_id){
            $key = 'goods_click';
            $redis = get_redis();
            return $redis->hIncrBy($key,$goods_id,1); 
        }
}
