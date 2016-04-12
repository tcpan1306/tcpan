<?php
namespace Admin\Controller;

/**
 * Description of UploadController
 *
 * @author PR
 */
class UploadController extends \Think\Controller{
    
    public function upload(){
        
//        var_dump($_FILES);exit;
         //创建upload对象
        $config = C('UPLOAD_SETTING');
         $upload = new \Think\Upload($config);
        //执行上传
       $file_info = $upload->upload($_FILES);
        //返回后上传结果
      if($config['driver']=='Qiniu'){
           $file_url = $file_info['file']['savepath'].$file_info['file']['savename'];
           $file_url = str_replace('/','_' ,$file_url);
       }  else {
           $file_url = $file_info['file']['savepath'].$file_info['file']['savename'];
       }
        $return = array(
            'status'=>$file_info ? 1:0 ,
            'file_url'=>$file_url,
//            'url_prefix'=>$config['URL_PREFIX']
            'msg'=>$upload->getError(),
        );
         $this->ajaxReturn($return);
     }
}
