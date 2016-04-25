<?php

namespace Home\Controller;

/**
 * Description of GoodsController
 *
 * @author PR
 */
class GoodsController extends \Think\Controller{
    /**
     * 存储模型对象.
     * @var \Home\Model\GoodsModel 
     */
    private $_model = null;

    /**
     * 设置标题和初始化模型.
     */
    protected function _initialize() {
        $this->_model = D('Goods');
    }
    
     
     /**
     * 将redis中的数据同步到mysql中.
     */
    public function syncGoodsClick() {
//        set_time_limit(0);//设置脚本最大执行时间
        $goods_click_model = M('GoodsClick');
        //获取所有的点击数,从redis中
        $redis = get_redis();
        $key = 'goods_click';
        $clicks = $redis->hGetAll($key);
        $good_ids = array_keys($clicks);
       $goods_click_model->where(['goods_id'=>['in',$good_ids]])->delete();
        $data = [];
        foreach ($clicks as $key=>$value){
        $data[] = [
            'goods_id'=>$key,
            'click_times'=>$value
        ];
    }
    if($data){
        $goods_click_model->addAll($data);
        echo '<script type="text/javascript">close();</script>';
//        echo '同步成功';
    }  else {
       echo '同步失败';    
    }
        }
}
