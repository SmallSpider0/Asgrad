<?php

//1 管理员 2 web 3 pc
//是否需要授权
$au_config = [
    //全局开关
    'global' => false,

    //用户模块
    'user' => [
        'no' => [
            'register',
            'login'
        ],
        'yes' => [
            'getUserInfo' => [2, 3],
            'changeUserInfo' => [2],
            'changePasswd' => [2],
        ]
    ],

    //订单模块
    'util' => [
        'no' => [],
        'yes' => [
            'submitOrder' => [2],
            'getOrderList' => [2],
            'getOrderInfo' => [2],
            'getOrderHIdList' => [2],
            'setOrderComplete' => [2],
            'getOrderData' => [3],
        ]
    ],

    //日志看板模块
    'log' => [
        'no' => [],
        'yes' => [
            'getTestLogList' => [2],
            'getDebugLogList' => [2],
            'getLogInfo' => [2],
            'getOrderStat' => [2],
            'getTestStationStat' => [2],
            'getProductStatList' => [2],

            'getHardwareId' => [3],
            'confirmHardwareId' => [3],
            'addTestLog' => [3],
            'addDebugLog' => [3],
            'getProductStatus' => [3],
        ]
    ],

    //管理员模块
    'admin' => [
        'no' => [
            'login'
        ],
        'yes' => [
            'generateCDK' => [1],
            'getCDKList' => [1],
            'getUserList' => [1],
            'getUserInfo' => [1],
            'editUserInfo' => [1],
            'getUpdateHisList' => [1],
            'addUpdate' => [1],
        ]
    ],

    //通用模块
    'util' => [
        'no' => [],
        'yes' => [
            'heartBeat' => [1, 2, 3],
            'logout' => [1, 2, 3],
            'getUpdate' => [1],
        ]
    ],
];
