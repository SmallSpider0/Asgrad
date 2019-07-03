<?php
//时区设定
date_default_timezone_set("PRC");

//配置文件
$config = array(
    //服务器ip
    'serverAddr' => '127.0.0.1',
    //连接mysql数据库
    'dbconfig' => array(
        'dbhost' => '120.78.171.98',
        'dbuser' => 'root',
        'dbpsw' => 'xzc28901520',
        'dbname' => 'asgrad',
        'dbcharset' => 'utf8',
    ),
    //安全
    'security' => array(
        'login' => 10, //每日允许连续登陆失败次数
        'token_time_offset' => 600, //token时间戳偏差容许值
    ),
);
