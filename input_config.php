<?php


//输入参数配置
//https://github.com/photondragon/webgeeker-validation

$input_config = [
    //用户模块
    'user' => [
        'register' => [
            'phone' => 'Required|Numbers',
            'email' => 'Required|Email',
            'company_name' => 'Required|Str',
            'passwd_web' => 'Required|Str',
            'passwd_pc' => 'Required|Str',
        ],
        'login' =>  [
            'mode1' => 'Required|IntIn:0,1',
            'mode2' => 'Required|IntIn:0,1',
            'account' => 'Required|Str',
            'passwd' => 'Required|Str',
        ],
        'getUserInfo' => [
            'user_id' => 'Required|IntGt:0',
        ],
        'changeUserInfo' => [
            'user_id' => 'Required|IntGt:0',
            'retest_times' => 'IntGeLe:0,10',
            'order_close_timeout' => 'IntGeLe:1,360',
        ],
        'changePasswd' => [
            'mode' => 'Required|IntIn:0,1',
            'user_id' => 'Required|IntGt:0',
            'old_passwd' => 'Required|Str',
            'new_passwd' => 'Required|Str',
        ],
    ],

    //订单模块
    'order' => [
        'submitOrder' =>  [
            'user_id' => 'Required|IntGt:0',
            'made_in' => 'Required|Str',
            'product_name' => 'Required|Str',
            'product_model' => 'Required|Str',
            'quantity' => 'Required|IntGt:0',
            'station_cnt' => 'Required|IntGeLe:1,10',
            'plan_online_time' => 'Required|DateTime',
            'working_procedure' => 'Required',
            'test_seq' => 'Required',
            'hid_list' => 'Required',
            'file' => 'Required|File',
        ],
        'getOrderList' =>  [
            'user_id' => 'Required|IntGt:0',
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGeLe:1,100',
            'status' => 'IntGeLe:0,3',
            'order_num' => 'Str',
            'made_in' => 'Str',
            'date_start' => 'DateTime',
            'date_end' => 'DateTime',
        ],
        'getOrderInfo' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
        ],
        'getOrderHIdList' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
            'status' => 'IntIn:0,1,2',
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGe:1',
        ],
        'setOrderComplete' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
        ],
        'getOrderData' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
        ],
        'downloadOrderFile' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
            'file_name' => 'Required|Str',
        ],
    ],

    //日志模块
    'log' => [
        'getTestLogList' =>  [
            'user_id' => 'Required|IntGt:0',
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGeLe:1,100',
            'sn' => 'Str',
            'order_num' => 'Str',
            'made_in' => 'Str',
            'date_start' => 'DateTime',
            'date_end' => 'DateTime',
        ],
        'getDebugLogList' =>  [
            'user_id' => 'Required|IntGt:0',
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGeLe:1,100',
            'log_id' => 'Str',
            'date_start' => 'DateTime',
            'date_end' => 'DateTime',
        ],
        'getLogInfo' =>  [
            'user_id' => 'Required|IntGt:0',
            'mode' => 'Required|IntIn:0,1',
            'id' => 'Required|IntGe:1',
        ],
        'getOrderStat' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
            'last_timestamp_comp' => 'Required|DateTime',
        ],
        'getTestStationStat' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
            'last_timestamp_good' => 'Required|DateTime',
            'last_timestamp_reject' => 'Required|DateTime',
        ],
        'getProductStatList' =>  [
            'user_id' => 'Required|IntGt:0',
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGeLe:1,100',
            'order_num' => 'Required|Str',
            'sn' => 'Str',
            'at_station' => 'Str',
            'status' => 'IntGeLe:0,3',
        ],
        'getHardwareId' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
            'type' => 'IntIn:0,1',
        ],
        'confirmHardwareId' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
            'id' => 'Required|IntGe:1',
        ],
        'addTestLog' =>  [
            'user_id' => 'Required|IntGt:0',
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
            'user_id' => 'Required|IntGt:0',
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
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
            'sn' => 'Required|Str',
        ],
    ],

    //管理员模块
    'admin' => [
        'login' =>  [
            'account' => 'Required|Str',
            'passwd' => 'Required|Str',
        ],
        'generateCDK' =>  [
            'phone' => 'Required|Numbers',
            'email' => 'Required|Email',
            'grant_time' => 'Required|IntGe:1',
        ],
        'deleteCDK' =>  [
            'id' => 'Required|IntGe:1',
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
            'grant_time_out' => 'Date',
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
    ],

    //通用模块
    'util' => [
        'heartBeat' =>  [
            'user_id' => 'Required|IntGe:1',
        ],
        'logout' =>  [
            'user_id' => 'Required|IntGe:1',
        ],
        'getUpdate' =>  [
            'user_id' => 'Required|IntGe:1',
            'version' => 'IntGe:1',
        ],
    ],
];
