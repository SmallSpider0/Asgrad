<?php

//https://github.com/photondragon/webgeeker-validation
//输入参数配置 默认值用@默认值表示,留空表示不需要判断输入值
//输入值不包括【只会】在授权用到的那些
//如 startTime@0 表示startTime的默认值为0
$input_config = array(
    //用户模块
    'user' => array(
        'register' => [
            'phone' => 'Required|Numbers',
            'email' => 'Required|Email',
            'company_name' => 'Required|Str',
            'cdkey' => 'Required|Str',
            'passwd_web' => 'Required|Str',
            'passwd_pc' => 'Required|Str',
        ],
        'login' =>  [
            'mode' => 'Required|IntIn:0,1',
            'account' => 'Required|Str',
            'passwd' => 'Required|Str',
        ],
        'getUserInfo' => [
            'userId' => 'Required|IntGt:0',
        ],
        'changeUserInfo' => [
            'userId' => 'Required|IntGt:0',
            'retest_times' => 'IntGeLe:0,10',
            'order_close_timeout' => 'IntGeLe:1,360',
        ],
        'changePasswd' => [
            'mode' => 'Required|IntIn:0,1',
            'userId' => 'Required|IntGt:0',
            'oldPasswd' => 'Required|Str',
            'newPasswd' => 'Required|Str',
        ],
    ),

    //订单模块
    'order' => array(
        'submitOrder' =>  [
            'made_in' => 'Required|Str',
            'product_name' => 'Required|Str',
            'product_model' => 'Required|Str',
            'quantity' => 'Required|IntGt:0',
            'station_cnt' => 'Required|IntGeLe:1,10',
            'plan_online_time' => 'Required|DateTime',
            'working_procedure' => 'Required',
            'test_seq' => 'Required',
            'hid_list' => 'Required',
            'files' => 'Required|File',
        ],
        'getOrderList' =>  [
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGeLe:1,100',
            'status' => 'IntGeLe:0,3',
            'order_num' => 'Str',
            'made_in' => 'Str',
            'date_start	' => 'DateTime',
            'date_end' => 'DateTime',
        ],
        'getOrderInfo' =>  [
            'order_num' => 'Required|Str',
        ],
        'getOrderHIdList' =>  [
            'order_num' => 'Required|Str',
            'status' => 'Required|IntIn:0,1,2',
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGe:1',
        ],
        'setOrderComplete' =>  [
            'order_num' => 'Required|Str',
        ],
        'getOrderData' =>  [
            'order_num' => 'Required|Str',
        ],
    ),

    //日志模块
    'log' => array(
        'getTestLogList' =>  [
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGeLe:1,100',
            'sn' => 'Str',
            'order_num' => 'Str',
            'made_in' => 'Str',
            'date_start' => 'DateTime',
            'date_end' => 'DateTime',
        ],
        'getDebugLogList' =>  [
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGeLe:1,100',
            'log_id' => 'Str',
            'date_start' => 'DateTime',
            'date_end' => 'DateTime',
        ],
        'getLogInfo' =>  [
            'mode' => 'Required|IntIn:0,1',
            'id' => 'Required|IntGe:1',
        ],
        'getOrderStat' =>  [
            'order_num' => 'Required|Str',
            'last_timestamp_comp' => 'Required|DateTime',
        ],
        'getTestStationStat' =>  [
            'order_num' => 'Required|Str',
            'last_timestamp_good' => 'Required|DateTime',
            'last_timestamp_reject' => 'Required|DateTime',
        ],
        'getProductStatList' =>  [
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGeLe:1,100',
            'order_num' => 'Required|Str',
            'sn' => 'Str',
            'at_station' => 'Str',
            'status' => 'IntGeLe:0,3',
        ],
        'getHardwareId' =>  [
            'order_num' => 'Required|Str',
            'type' => 'IntIn:0,1',
        ],
        'confirmHardwareId' =>  [
            'order_num' => 'Required|Str',
            'id' => 'Required|IntGe:1',
        ],
        'addTestLog' =>  [
            'order_num' => 'Required|Str',
            'sn' => 'Required|Str',
            'station' => 'Required|Str',
            'result' => 'Required|Bool',
            'error_code' => 'Required|Str',
            'start_time' => 'Required|Numbers',
            'end_time' => 'Required|Numbers',
            'test_item' => 'Required',
            'test_log' => 'Required',
        ],
        'addDebugLog' =>  [
            'log_id' => 'Required|Str',
            'station' => 'Required|Str',
            'result' => 'Required|Bool',
            'error_code' => 'Required|Str',
            'start_time' => 'Required|Numbers',
            'end_time' => 'Required|Numbers',
            'test_item' => 'Required',
            'test_log' => 'Required',
        ],
        'getProductStatus' =>  [
            'order_num' => 'Required|Str',
            'sn' => 'Required|Str',
        ],
    ),

    //管理员模块
    'admin' => array(
        'login' =>  [
            'account' => 'Required|Str',
            'passwd' => 'Required|Str',
        ],
        'generateCDK' =>  [
            'phone' => 'Required|Numbers',
            'email' => 'Required|Email',
            'grant_time' => 'Required|IntGe:1',
        ],
        'getCDKList' =>  [
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGeLe:1,100',
            'phone' => 'Numbers',
            'email' => 'Email',
            'status' => 'IntIn:0,1,2',
        ],
        'getUserList' =>  [
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGeLe:1,100',
            'company_name' => 'Str',
        ],
        'getUserInfo' =>  [
            'user_id' => 'Required|IntGe:1',
        ],
        'editUserInfo' =>  [
            'user_id' => 'Required|IntGe:1',
            'grant_time_out' => 'DateTime',
            'remark' => 'Str',
        ],
        'getUpdateHisList' =>  [
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGeLe:1,100',
            'user_id' => 'IntGe:1',
        ],
        'addUpdate' =>  [
            'user_id' => 'Required|IntGe:1',
            'description' => 'Required|Str',
            'file' => 'Required|File',
        ],
    ),

    //通用模块
    'util' => array(
        'heartBeat' =>  [
            'user_id' => 'Required|IntGe:1',
            'mode' => 'Required|IntIn:0,1,2',
        ],
        'logout' =>  [
            'user_id' => 'Required|IntGe:1',
            'mode' => 'Required|IntIn:0,1,2',
        ],
        'getUpdate' =>  [
            'user_id' => 'Required|IntGe:1',
            'version' => 'IntGe:1',
        ],
    ),
);
