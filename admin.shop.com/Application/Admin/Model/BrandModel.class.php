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

}
