<?php
//得到错误信息
function get_errror($errors){
    if(!is_array($errors)){
        $errors = array($errors);
    }
    $html = '<ol>';
      foreach($errors as $error){
          $htmml.='<li>'.$errors.'</li>';
      }      
   $html .='</ol>'; 
   return $html;
}

