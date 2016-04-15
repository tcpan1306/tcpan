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

 function arr2select(array $data,$name,$value_field='id',$name_field='name',$select=''){
     $html ='<select name="'.$name.'">';
     $html .='<option value=""> 请选择...</option>';
     foreach ($data as $value){
         if($value[$value_field]== $select){
               $html .= '<option value="'.$value[$value_field].'" selected="selected">' .$value[$name_field]. '</option>';
         }  else {
           $html .='<option value="'.$value[$value_field].'">'.$value[$name_field].'</option>';
         }
     }
     $html .='</select>';
     return $html;
 }

 function onearr2select(array $data,$name,$select=''){
     $html ='<select name="'.$name.'">';
     $html .='<option value=""> 请选择...</option>';
     foreach ($data as $key=>$value){
         $key = (string)$key;
         if($key === $select){
               $html .= '<option value="'.$key.'" selected="selected">' .$value. '</option>';
         }  else {
           $html .='<option value="'.$key.'">'.$value.'</option>';
         }
     }
     $html .='</select>';
     return $html;
 }
 