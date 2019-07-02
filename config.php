<?php
//时区设定
date_default_timezone_set("PRC");

//配置文件
$config = array(
    //服务器ip
    'serverAddr' => '120.78.171.98',
    //连接mysql数据库
    'dbconfig' => array(
        'dbhost' => '120.78.171.98',
        'dbuser' => 'otc_exchange',
        'dbpsw' => '971660e85a65f6e008838ab5b0f85cd0a00',
        'dbname' => 'otc',
        'dbcharset' => 'utf8',
    ),
    //安全
    'security' => array(
        'verifi_code' => 10, //单个验证码可尝试次数
        'login' => 10, //每日允许连续登陆失败次数
        'token_time_offset' => 600, //token时间戳偏差容许值
    ),
);
