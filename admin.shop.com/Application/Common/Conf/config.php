<?php

define('DOMAIN', 'http://www.tcpan.com/admin.shop.com');
return array(
    'TMPL_PARSE_STRING' => array(
        '__CSS__' => DOMAIN . '/Public/Css',
        '__JS__' => DOMAIN . '/Public/Js',
        '__IMG__' => DOMAIN . '/Public/Images',
        '__UPLOADIFY__' => DOMAIN . '/Public/Ext/uploadify',
        '__LAYER__' => DOMAIN . '/Public/Ext/layer',
        '__ZTREE__' => DOMAIN . '/Public/Ext/ztree',
        '__TREEGRID__' => DOMAIN . '/Public/Ext/treegrid',
        '__UEDITOR__' => DOMAIN . '/Public/Ext/ueditor',
        '__UPLOAD_URL_PREFIX__' => 'http://7xsvg3.com2.z0.glb.clouddn.com',
    ),
    //'配置项'=>'配置值'
    //配置数据库连接
    'DB_TYPE' => 'mysql',
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => '3306',
    'DB_USER' => 'root',
    'DB_PWD' => '123456',
    'DB_NAME' => 'thinkphp',
    'DB_PREFIX' => '',
    'DB_CHARSET' => 'utf8',
    'SHOW_PAGE_TRACE' => TRUE,
    'PAGE_SIZE' => 3,
    'PAGE_THEME' => '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
    'URL_MODEL' => 2,
    //文件上传的相关配置.
    'UPLOAD_SETTING' => array(
        'maxSize' => 0, //上传的文件大小限制 (0-不做限制)
        'exts' => array('jpg', 'png', 'gif', 'jpeg'), //允许上传的文件后缀
        'autoSub' => true, //自动子目录保存文件
        'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt' => '', //文件保存后缀，空则使用原后缀
        'replace' => false, //存在同名是否覆盖
        'hash' => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调，如果存在返回文件信息数组
        'driver' => 'Qiniu', // 文件上传驱动
        'driverConfig' => array(
            'secrectKey' => 'YQgsGsk31IXkkb4x0pW3osu7-Tr-sE3N3mCi-Htz', //七牛sk
            'accessKey' => 'VwzxMBAvcftQY_NfecmyQVPU9LR_SXsthdiExGuz', //七牛ak
            'domain' => '7xsvg3.com2.z0.glb.clouddn.com', //空间域名
            'bucket' => 'tcpan', //空间名称
            'timeout' => 300, //超时时间
        ), // 上传驱动配置
    ),
    'CAPTCHA_SETTING'   => [
        'length' => 4,
    ],
    'IGNORE_PATHS'=>[
           'Admin/Admin/login',
            'Admin/Captcha/captcha',
            'Admin/Index/top',
            'Admin/Index/index',
            'Admin/Index/menu',
            'Admin/Index/main',
            'Admin/Admin/index',
            'Admin/Admin/add',
        'Admin/Admin/edit',
        'Admin/Menu/index',
    ],
);
