<?php

namespace Admin\Controller;

/**
 * Description of ArticleController
 *
 * @author PR
 */
class ArticleController extends \Think\Controller {

    private $_model = null;

    protected function _initialize() {
        $meta_title = array(
            'index' => '文章分类的管理',
            'add' => '增加分类',
            'edit' => '编辑文章分类',
            'delete' => '移除分类'
        );
        $meta_title = $meta_title[ACTION_NAME];
        $this->assign("meta_title", $meta_title);
        $this->_model = D('Article');
    }

    /**
     * index 显示方法
     */
    public function index() {
        $cond = array();
        //模糊查询
        $keyword = I('get.keyword');
        if ($keyword) {
            $cond['name'] = array('like', '%' . $keyword . '%');
        }
        $article_category = M('ArticleCategory')->select();
//       dump($article_category);exit;
        $this->assign($this->_model->getPageResult($cond));
        $this->assign('article_category', $article_category); //传递分类表的数据 得到分类名字
        $this->display();
    }

    /**
     * 增加文章
     */
    public function add() {
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model->getError()));
            }
            $rest = $this->_model->add();
//            dump($rest);exit;
            if ($rest === false) {
                $this->error(get_error($this->_model->getError()));
            } else {
                $data = array(
                    'content' => $content = $_POST['content'],
                    'article_id' => $rest,
                );
                if (D('ArticleContent')->add($data) === false) {
                    $this->error(get_error(D('ArticleContent')->getError()));
                }
                $this->success('添加成功', U('index'));
            }
        } else {
//            //显示文章分类的名字的条件
            $articlecategorys = $this->_model->getCategory();
            $this->assign('articlecategorys', $articlecategorys);
            $this->display();
        }
    }

    /**
     * 修改数据
     * @param int $id
     */
    public function edit($id) {
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model->getError()));
            }
            $rest = $this->_model->save();
//            dump($rest);exit;
            if ($rest === false) {
                $this->error(get_error($this->_model->getError()));
            } else {
                $data = array(
                    'content' => I(post.content),
                    'article_id'=>$id,
                );
                if (D('ArticleContent')->save($data) === false) {
                    $this->error(get_error(D('ArticleContent')->getError()));
                }
                $this->success('添加成功', U('index'));
            }
        } else {
            //数据的回显
            $articlecategorys = $this->_model->getCategory();//获得文章分类的内容
            $this->assign('articlecategorys', $articlecategorys);
            $ArticleContent = D('ArticleContent')->find($id);//获得文章内容
            $this->assign('ArticleContent', $ArticleContent);
            $row = $this->_model->where(array('id' =>$id))->find();
//            dump($row);
//           dump( $articlecategorys);exit;
//            dump($row['article_category_id']);
//            exit;
            $this->assign('row', $row);
            $this->display('edit');
        }
    }
    /**
     * 移除
     */
     public function delete($id){
        $data = array(
            'status' => -1,
            'name'=>array('exp','CONCAT(name,"_del")'),
        );
        if($this->_model->where(array('id'=>$id))->setField($data)===false){
            $this->error(get_error($this->_model->getError()));
        }  else {
            $this->success('移除成功',U('index'));    
        }
    }
}
