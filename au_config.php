<?php

//1 管理员 2 web 3 pc
//是否需要授权
$au_config = [
    //全局开关
    'global' => true,

    //用户模块
    'user' => [
        'no' => [
            'Register',
            'Login',
        ],
        'yes' => [
            'GetUserInfo' => [2, 3],
            'ChangeUserInfo' => [2],
            'ChangePasswd' => [2],
        ]
    ],

    //订单模块
    'order' => [
        'no' => [],
        'yes' => [
            'SubmitOrder' => [2],
            'GetOrderList' => [2],
            'GetOrderInfo' => [2],
            'GetOrderHIdList' => [2],
            'SetOrderComplete' => [2],
            'SetOrderStart' => [2],
            'GetOrderData' => [3],
            'DownloadOrderFile' => [3],
        ]
    ],

    //日志看板模块
    'log' => [
        'no' => [],
        'yes' => [
            'GetTestLogList' => [2],
            'GetDebugLogList' => [2],
            'GetLogInfo' => [2],
            'GetOrderStat' => [2],
            'GetTestStationStat' => [2],
            'GetProductStatList' => [2],

            'GetHardwareId' => [3],
            'ConfirmHardwareId' => [3],
            'AddTestLog' => [3],
            'AddDebugLog' => [3],
            'GetProductStatus' => [3],
        ]
    ],

    //管理员模块
    'admin' => [
        'no' => [
            'Login'
        ],
        'yes' => [
            'GenerateCDK' => [1],
            'DeleteCDK' => [1],
            'GetCDKList' => [1],
            'GetUserList' => [1],
            'GetUserInfo' => [1],
            'EditUserInfo' => [1],
            'GetUpdateHisList' => [1],
            'AddUpdate' => [1],
        ]
    ],

    //通用模块
    'util' => [
        'no' => [],
        'yes' => [
            'HeartBeat' => [1, 2, 3],
            'Logout' => [1, 2, 3],
            'GetUpdate' => [1, 2, 3],
        ]
    ],
];
