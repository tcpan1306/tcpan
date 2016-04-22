<?php

namespace Home\Model;

/**
 * Description of GoodsCategoryModel
 *
 * @author PR
 */
class GoodsCategoryModel extends \Think\Model {
     /**
     * 获得分类列表.
     * @return array
     */
    public function getList(){
        $cond = [
            'status'=>1
        ];
        return $this->where($cond)->select();
    }
}
