<?php

define('DOMAIN', 'http://www.shop.com');
return array(
    'TMPL_PARSE_STRING' => array(
        '__CSS__' => DOMAIN . '/Public/css',
        '__JS__' => DOMAIN . '/Public/js',
        '__IMG__' => DOMAIN . '/Public/images',
        '__JQ_VALIDATION__'=>DOMAIN . '/Public/ext/jquery_validation',
       
    ),
    //配置数据库连接
    'DB_TYPE' => 'mysql',
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => '3306',
    'DB_USER' => 'root',
    'DB_PWD' => '123456',
    'DB_NAME' => 'thinkphp',
    'DB_PREFIX' => '',
    'DB_CHARSET' => 'utf8',
    'SHOW_PAGE_TRACE'   => true,
    'ALIDAYU_SETTING'=>[
        'ak'=>' 23350860',
        'sk'=>'ed5c4fb0626b12c672b8052fb9053f1a'
    ],
    
    'CAPTCHA_SETTING'   => [
        'length' => 4,
    ],
);
