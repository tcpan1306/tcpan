<?php

namespace Admin\Model;

/**
 * 品牌的管理
 *
 * @author PR
 */
class BrandModel extends \Think\Model {

    //开启批量验证
    protected $patchValidate = true;
    //制作自动验证的方法 验证添加品牌
    protected $_validate = array(
        /**
         * 品牌唯一
         * 品牌不能为空
         */
        array('name', '', '该品牌已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
        array('name', 'require', '品牌不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
    );

    /**
     * 分页  模糊查询
     */
    public function getPageResult(array $cond = array()) {

        $cond = $cond + array(
            'status' => array('gt', -1), //status的值要大于-1蔡显示
        );
        //获取总行数
        $count = $this->where($cond)->count();
        //获取显示多少页
        $size = C('PAGE_SIZE');
        $page_obj = new \Think\Page($count, $size);
        $page_obj->setConfig("theme", C('PAGE_THEME'));
        $page_html = $page_obj->show();
        $rows = $this->where($cond)->page(I('get.p'), $size)->select();
        return array(
            'rows' => $rows,
            'page_html' => $page_html
        );
    }

}
