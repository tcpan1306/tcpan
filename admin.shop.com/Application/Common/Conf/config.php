<?php

define('DOMAIN', 'http://www.tcpan.com/admin.shop.com');
return array(
    'TMPL_PARSE_STRING' => array(
        '__CSS__' => DOMAIN . '/Public/Css',
        '__JS__' => DOMAIN . '/Public/Js',
        '__IMG__' => DOMAIN . '/Public/Images',
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
    'SHOW_PAGE_TRACE'=>TRUE,
    'PAGE_SIZE'=>3,
    'PAGE_THEME'=>'%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
    'URL_MODEL'=>2,
);
