<?php
error_reporting(E_ALL || ~E_NOTICE);
define(Root_Path, dirname(__FILE__));

require_once "src/my-lib/tools.php";

//引入对应的class
$paths = explode("@", $_SERVER["QUERY_STRING"]);
$path = $paths[0];
$path_pieces = explode("/", $path);
$get = explode("/", $paths[1]);

//是否授权访问
$can_run = false;

//输入值存在性判断
$check_input_ret = check_input($path_pieces, $get);
if ($check_input_ret != 'true') {
    msg(400, '未收到输入数据: ' . $check_input_ret);
    return;
}

//授权判断
if (!$au_config['global']) {
    $can_run = true;
} else {
    if (in_array($path, $au_config['yes'])) { //判断是否是需要授权的接口
        if (check_token($_POST['id'], $_POST['api_key'], $_POST['timestamp'], $_POST['sign'], $path)) {
            $can_run = true;
        }
    } elseif (in_array($path, $au_config['no'])) { //判断是否是不需要授权的接口
        $can_run = true;
    }
}

//如果允许执行
$class = end($path_pieces);
if ($can_run) {
    require_once "src/" . $path . ".php";
    $o = new $class();
    $o->run();
} else {
    msg(410, '未授权的访问');
    return;
}
