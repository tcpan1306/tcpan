<?php


namespace Home\Model;

/**
 * Description of ArticleModel
 *
 * @author PR
 */
class ArticleModel extends \Think\Model{
    /**
     * 
     */
    public function getHelpArticleList(){
        //取得帮助分类
       $help_categories =  M('ArticleCategory')->where(['is_help'=>1,'status'=>1])->limit(5)->getField('id,name');
        //根据分类获取文章
        foreach ($help_categories as $key=>$value){
            $value=[
                'name'=>$value,
                'list'=>$this->field('id,name')->where(['status'=>1,'article_category_id'=>$key])->limit(6)->select(),
            ];
            $help_categories[$key] = $value;
        }
        //组织数据返回
        return $help_categories;
    }
}
