<?php

//时区设定
date_default_timezone_set("PRC");

//配置文件
$config = array(
    //服务器ip
    'serverAddr' => '127.0.0.1',
    //连接mysql数据库
    'dbconfig' => array(
        'dbhost' => 'rm-bp1t5139o83gi60y790110.mysql.rds.aliyuncs.com',
        'dbuser' => 'xzc2034222',
        'dbpsw' => 'Xzc28901520xzc',
        'dbname' => 'asgrad',
        'dbcharset' => 'utf8',
    ),
    //安全
    'security' => array(
        'login' => 10, //每日允许连续登陆失败次数
        'token_time_offset' => 600, //token时间戳偏差容许值
    ),
    //七牛云sdk
    'qiniu-sdk' => array(
        'accessKey' => 'Mftnw0ULVkfW-iq4pcK7g9wDwJ4MzD5Kq-Y-duD6',
        'secretKey' => 'lMhYzRLHFQl_01NfCveXMx6f0FxBiWiISNu3YEa_',
        'bucket' => 'asgrad_file',
    ),
    'hid_types' => array('imei', 'mac'),
    'order_files_url' => 'upload/order_file_upload',
    'top_issue_update_period' => 360,
);
