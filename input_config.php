<?php

//输入参数配置 默认值用@默认值表示,留空表示不需要判断输入值
//输入值不包括【只会】在授权用到的那些
//如 startTime@0 表示startTime的默认值为0
$input_config = array(
    //个人中心
    'userCenter' => array(
        'getUserInfo' => array('userId@null', 'userId2'),
    ),
);
