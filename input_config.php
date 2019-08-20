<?php


//输入参数配置
//https://github.com/photondragon/webgeeker-validation

$input_config = [
    //用户模块
    'user' => [
        'Register' => [
            'phone' => 'Required|Numbers',
            'email' => 'Required|Email',
            'company_name' => 'Required|Str',
            'passwd_web' => 'Required|Str',
            'passwd_pc' => 'Required|Str',
        ],
        'Rogin' =>  [
            'mode1' => 'Required|IntIn:0,1',
            'mode2' => 'Required|IntIn:0,1',
            'account' => 'Required|Str',
            'passwd' => 'Required|Str',
        ],
        'GetUserInfo' => [
            'user_id' => 'Required|IntGt:0',
        ],
        'RhangeUserInfo' => [
            'user_id' => 'Required|IntGt:0',
            'retest_times' => 'IntGeLe:0,10',
            'order_close_timeout' => 'IntGeLe:1,360',
        ],
        'RhangePasswd' => [
            'mode' => 'Required|IntIn:0,1',
            'user_id' => 'Required|IntGt:0',
            'old_passwd' => 'Required|Str',
            'new_passwd' => 'Required|Str',
        ],
    ],

    //订单模块
    'order' => [
        'SubmitOrder' =>  [
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
        'GetOrderList' =>  [
            'user_id' => 'Required|IntGt:0',
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGeLe:1,100',
            'status' => 'IntGeLe:0,3',
            'order_num' => 'Str',
            'product_name' => 'Str',
            'product_model' => 'Str',
            'made_in' => 'Str',
            'date_start' => 'DateTime',
            'date_end' => 'DateTime',
        ],
        'GetOrderInfo' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
        ],
        'GetOrderHIdList' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
            'status' => 'IntIn:0,1,2',
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGe:1',
        ],
        'SetOrderComplete' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
        ],
        'SetOrderStart' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
        ],
        'GetOrderData' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
        ],
        'DownloadOrderFile' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
            'file_name' => 'Required|Str',
        ],
    ],

    //日志模块
    'log' => [
        'GetTestLogList' =>  [
            'user_id' => 'Required|IntGt:0',
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGeLe:1,100',
            'sn' => 'Str',
            'order_num' => 'Str',
            'made_in' => 'Str',
            'date_start' => 'DateTime',
            'date_end' => 'DateTime',
        ],
        'GetDebugLogList' =>  [
            'user_id' => 'Required|IntGt:0',
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGeLe:1,100',
            'log_id' => 'Str',
            'date_start' => 'DateTime',
            'date_end' => 'DateTime',
        ],
        'GetLogInfo' =>  [
            'user_id' => 'Required|IntGt:0',
            'mode' => 'Required|IntIn:0,1',
            'id' => 'Required|IntGe:1',
        ],
        'GetOrderStat' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
            'last_timestamp_comp' => 'DateTime',
        ],
        'GetTestStationStat' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
            'last_timestamp_good' => 'DateTime',
            'last_timestamp_reject' => 'DateTime',
        ],
        'GetProductStatList' =>  [
            'user_id' => 'Required|IntGt:0',
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGeLe:1,100',
            'order_num' => 'Required|Str',
            'sn' => 'Str',
            'at_station' => 'Str',
            'status' => 'IntGeLe:0,3',
        ],
        'GetHardwareId' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
            'type' => 'StrIn:imei,mac',
        ],
        'ConfirmHardwareId' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
            'id' => 'Required|IntGe:1',
        ],
        'AddTestLog' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
            'sn' => 'Required|Str',
            'station' => 'Required|Str',
            'result' => 'Required|IntIn:0,1',
            'error_code' => 'Str',
            'start_time' => 'Required|Numbers',
            'end_time' => 'Required|Numbers',
            'test_item' => 'Required',
            'test_log' => 'Required',
        ],
        'AddDebugLog' =>  [
            'user_id' => 'Required|IntGt:0',
            'log_id' => 'Required|Str',
            'station' => 'Required|Str',
            'result' => 'Required|IntIn:0,1',
            'error_code' => 'Str',
            'start_time' => 'Required|Numbers',
            'end_time' => 'Required|Numbers',
            'test_item' => 'Required',
            'test_log' => 'Required',
        ],
        'GetProductStatus' =>  [
            'user_id' => 'Required|IntGt:0',
            'order_num' => 'Required|Str',
            'sn' => 'Required|Str',
        ],
    ],

    //管理员模块
    'admin' => [
        'Login' =>  [
            'account' => 'Required|Str',
            'passwd' => 'Required|Str',
        ],
        'GenerateCDK' =>  [
            'phone' => 'Required|Numbers',
            'email' => 'Required|Email',
            'grant_time' => 'Required|IntGe:1',
        ],
        'DeleteCDK' =>  [
            'id' => 'Required|IntGe:1',
        ],
        'GetCDKList' =>  [
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGeLe:1,100',
            'phone' => 'Numbers',
            'email' => 'Email',
            'status' => 'IntIn:0,1,2',
        ],
        'GetUserList' =>  [
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGeLe:1,100',
            'company_name' => 'Str',
        ],
        'GetUserInfo' =>  [
            'user_uid' => 'Required|IntGe:1',
        ],
        'EditUserInfo' =>  [
            'user_uid' => 'Required|IntGe:1',
            'grant_time_out' => 'Date',
            'remark' => 'Str',
        ],
        'GetUpdateHisList' =>  [
            'page' => 'Required|IntGe:1',
            'pageLimit' => 'Required|IntGeLe:1,100',
            'user_uid' => 'IntGe:1',
        ],
        'AddUpdate' =>  [
            'user_uid' => 'Required|IntGe:1',
            'description' => 'Required|Str',
            'version_id' => 'Required|Str',
            'file' => 'Required|File',
        ],
    ],

    //通用模块
    'util' => [
        'HeartBeat' =>  [
            'user_id' => 'Required|IntGe:1',
        ],
        'Logout' =>  [
            'user_id' => 'Required|IntGe:1',
        ],
        'GetUpdate' =>  [
            'user_uid' => 'Required|IntGe:1',
            'version' => 'IntGe:1',
        ],
    ],
];
