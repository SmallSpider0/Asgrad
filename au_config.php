<?php

//1 管理员 2 web 3 pc
//是否需要授权
$au_config = array(
    //全局开关
    'global' => false,

    'user' => array(
        'no' => array(
            'register',
            'login'
        ),
        'yes' => array(
            'heartBeat' => array(1, 2, 3),
            'getUserInfo' => array(2, 3),
            'changeUserInfo' => array(2),
            'changePasswd' => array(2),
            'logout' => array(1, 2, 3),
        )
    ),
);
