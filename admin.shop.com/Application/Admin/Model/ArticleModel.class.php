<?php

namespace Admin\Model;

/**
 * Description of ArticleModel
 *
 * @author PR
 */
class ArticleModel extends \Think\Model{
     //开启批量验证
    protected $patchValidate = true;
    //制作自动验证的方法 验证添加品牌
    protected $_validate = array(
        /**
         * 分类唯一
         * 分类不能为空
         */
        array('name', '', '该分类已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
        array('name', 'require', '分类不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
    );
    /**
     * 自动完成   完成时间入库
     */
    protected $_auto = array(
        array('inputtime',NOW_TIME)
    );




    /**
     * 分页 模糊查询
     * @param array $cond
     * @return type
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
        $article_category=M('ArticleCategory')->select();
       
        return array(
            'rows' => $rows,
//            'article_categorys'=>$article_categorys,
            'page_html' => $page_html
        );
    }
    
    //分类的下拉的方法
    public function getCategory(){
        //显示文章分类的名字的条件
         $status = array(
                'status' => array('gt', -1),
            );
          $articlecategorys = D('ArticleCategory')->where($status)->select();
          return   $articlecategorys;
    }
}
