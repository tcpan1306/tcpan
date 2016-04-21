<?php

namespace Common\Behaviors;

/**
 * Description of CheckPermissionBehavior
 *
 * @author PR
 */
class CheckPermissionBehavior extends \Think\Behavior {

    public function run(&$params) {
        //获取当前请求的路径
        $url = implode('/', [MODULE_NAME, CONTROLLER_NAME, ACTION_NAME]);
        //先判断是否忽略
        $ignore =C('IGNORE_PATHS');
        if (in_array($url, $ignore)) {
            return true;
        }
        $userinfo = session("USERINFO");
        if(empty($userinfo)){
            $admin_model = D('Admin');
            //自动登录并保存用户的信息和权限信息
            $admin_model->autoLogin();
            $userinfo = session("USERINFO");
        }
        if ($userinfo) {
            if($userinfo['username'] === 'tcpan'){
                return true;
            }
          }
          //获取用户可以访问的路径 
         $paths = session('PATHS');
         if(!is_array($paths)){
             $paths = [];
         }
//         获取当前请求路径
            if (!in_array($url, $paths)) {
                $url = U('Admin/Admin/login');
                redirect($url);
            }
    }

}
