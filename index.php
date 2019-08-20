<?php
//error_reporting(E_ALL || ~E_NOTICE);
define('Root_Path', dirname(__FILE__));

require_once "src/my-lib/tools.php";

//输入值解析

$path = $_SERVER["QUERY_STRING"];
$path_pieces = explode("/", $path);


//输入值判断
$check_input_ret = checkInput($path_pieces);
if ($check_input_ret != 'true') {
    msg(400, $check_input_ret);
    return;
}


//授权判断
$can_run = false;


$role = 0; //角色 默认为无角色 1 管理员 2 web 3 pc
if (!$au_config['global']) { //全局开关判断
    $can_run = true;
} else {
    if (in_array($path_pieces[1], $au_config[$path_pieces[0]]['no'])) { //如果不需要授权
        $can_run = true;
    } elseif (isset($au_config[$path_pieces[0]]['yes'][$path_pieces[1]])) { //如果需要授权
        if ($role = checkToken($_POST['user_id'], $_POST['_api_key'], $_POST['_timestamp'], $_POST['_sign'], $path, $au_config[$path_pieces[0]]['yes'][$path_pieces[1]])) {
            $can_run = true;
        }
    }
}


//如果允许执行
if ($can_run) {
    include_once "src/" . $path . ".php";
    $class = '\\asgrad\\' . $path_pieces[0] . '\\' . $path_pieces[1];
    $o = new $class();
    $o->run($role);
} else {
    msg(410, '未授权的访问');
    return;
}
