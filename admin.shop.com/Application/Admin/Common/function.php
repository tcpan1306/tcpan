<?php
//得到错误信息
function get_error($errors){
    if(!is_array($errors)){
        $errors = array($errors);
    }
    $html = '<ol>';
      foreach($errors as $error){
          $html.='<li>'.$error.'</li>';
      }      
   $html .='</ol>'; 
   return $html;
}

