<?php

define('DOMAIN', 'http://www.shop.com');
return array(
    'TMPL_PARSE_STRING' => array(
        '__CSS__' => DOMAIN . '/Public/css',
        '__JS__' => DOMAIN . '/Public/js',
        '__IMG__' => DOMAIN . '/Public/images',
        '__JQ_VALIDATION__' => DOMAIN . '/Public/ext/jquery_validation',
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
    'SHOW_PAGE_TRACE' => true,
    'ALIDAYU_SETTING' => [
        'ak' => ' 23350860',
        'sk' => 'ed5c4fb0626b12c672b8052fb9053f1a'
    ],
    'CAPTCHA_SETTING' => [
        'length' => 4,
    ],
    'EMAIL_SETTING' => [
        'host' => 'smtp.163.com',
        'username' => '13075412875@163.com',
        'password' => 'tcpan163',
        'smtpsecure' => 'ssl',
        'port' => 465,
    ],
   //Redis Session配置
    'SESSION_AUTO_START'    =>  true,    // 是否自动开启Session
    'SESSION_TYPE'            =>  'Redis',    //session类型
    'SESSION_PERSISTENT'    =>  1,        //是否长连接(对于php来说0和1都一样)
    'SESSION_CACHE_TIME'    =>  1,        //连接超时时间(秒)
    'SESSION_EXPIRE'        =>  0,        //session有效期(单位:秒) 0表示永久缓存
    'SESSION_PREFIX'        =>  'sess_',        //session前缀
    'SESSION_REDIS_HOST'    =>  '127.0.0.1', //分布式Redis,默认第一个为主服务器
    'SESSION_REDIS_PORT'    =>  '6379',           //端口,如果相同只填一个,用英文逗号分隔
    'SESSION_REDIS_AUTH'    =>  '',    //Redis auth认证(密钥中不能有逗号),如果相同只填一个,用英文逗号分隔

);
