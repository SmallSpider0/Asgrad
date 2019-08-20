<?php

// 制定允许其他域名访问
header("Access-Control-Allow-Origin:*");
// 响应类型
header('Access-Control-Allow-Methods:POST');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with, content-type');

require_once ROOT_PATH . '/config.php';
require_once ROOT_PATH . '/input_config.php';
require_once ROOT_PATH . '/au_config.php';
require_once ROOT_PATH . "/vendor/autoload.php";



global $db;

dBConnect();

/**
 * 数据库获取单行数据
 *
 * @param string $table  表名
 * @param string $ret     返回值，输入res则返回查询结果
 * @param string $err_ret 出错时的返回值
 * @param string $cl      查询的列名，用逗号空格分隔
 *
 * @return void
 */
function dbGetOne($table, $ret = '', $err_ret = '', $cl = '*')
{
    global $db;
    $res = $db->getOne($table, $cl);
    if (!$res) {
        if (!$db->getLastError()) {
            msg(201, "无数据");
        } else {
            msg(401, $err_ret);
        }
    } else {
        if ($ret == 'res') {
            $ret = $res;
        }
        msg(200, $ret);
    }
}

/**
 * 数据库插入
 *
 * @param string $table 表名
 * @param array  $data   待插入数据
 * @param string $ret    返回值，输入res则返回查询结果
 *
 * @return void
 */
function dbInsert($table, $data, $ret = '')
{
    global $db;
    if ($res = $db->insert($table, $data)) {
        if ($ret == 'res') {
            $ret = $res;
        }
        msg(200, $ret);
    } else {
        msg(402, $db->getLastError());
    }
}

/**
 * 数据库更新
 *
 * @param string $table 表名
 * @param array  $data   待更新数据
 * @param string $ret    返回值，输入res则返回查询结果
 *
 * @return void
 */
function dbUpdate($table, $data, $ret = '')
{
    global $db;
    if ($res = $db->update($table, $data)) {
        if ($ret == 'res') {
            $ret = $res;
        }
        msg(200, $ret);
    } else {
        msg(402, $db->getLastError());
    }
}

/**
 * 数据库删除
 *
 * @param string $table 表名
 *
 * @return void
 */
function dbDelete($table)
{
    global $db;
    if ($db->delete($table)) {
        msg(200);
    } else {
        msg(402, $db->getLastError());
    }
}

/**
 * 数据库连接
 *
 * @return void
 */
function dBConnect()
{
    global $db;
    global $config;
    $dbcfg = $config['dbconfig'];
    $db = new MysqliDb($dbcfg['dbhost'], $dbcfg['dbuser'], $dbcfg['dbpsw'], $dbcfg['dbname']);
}

/**
 * 参数校验
 *
 * @param string $var     参数值
 * @param string $default 参数默认值
 *
 * @return bool
 */
function checkVar(&$var, $default = '')
{
    if (!isset($var) and $default != '') {
        $var = $default;
    }
    return isset($var);
}

/**
 * Token校验
 *
 * @param string $id        用户id
 * @param string $api_key   ak
 * @param string $timestamp 时间戳
 * @param string $sign      签名
 * @param string $url       接口url
 * @param array  $auth      配置文件
 *
 * @return bool
 */
function checkToken($id, $api_key, $timestamp, $sign, $url, $auth)
{
    $tables = ["admin_login", "user_login_web", "user_login_pc"];
    global $db;
    global $config;
    if (checkVar($id) and checkVar($api_key) and checkVar($timestamp) and checkVar($sign)) {
        //判断调用接口的角色
        foreach ($auth as $value) {
            $db->where('id', $id)->where('api_key', $api_key);
            $res = $db->getOne($tables[$value - 1], "security_key, time_out");
            if ($res) {
                $table = $tables[$value - 1];
                $role = $value;
                break;
            }
        }
        if (!$res) {
            return false;
        }

        if ($res['time_out'] > time() && abs(time() - $timestamp) < $config['security']['token_time_offset']) { //还没过期
            if ($sign == hash_hmac("sha256", $api_key . $timestamp . $url, $res['security_key'])) {
                //调用接口后 刷新过期时间
                if ($res['time_out'] - time() < 1800) {
                    $updateData = array(
                        "time_out" => time() + 7200, //2小时
                    );
                    $db->where('id', $id);
                    $db->update($table, $updateData);
                }
                return $role; //1 管理员 2 web 3 pc
            }
        }
    }
    return false;
}

/**
 * 构建返回数据并返回
 *
 * @param int   $code 返回代码
 * @param array $msg  返回数据
 *
 * @return void
 */
function msg($code, $msg = 'success')
{
    $arr = array(
        'code' => $code,
        'msg' => $msg,
    );
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
}

use WebGeeker\Validation\Validation;
/**
 * 输入值校验
 *
 * @param array $path_pieces 分隔好的接口路径
 *
 * @return string
 */
function checkInput($path_pieces)
{
    if (count($path_pieces) > 2) {
        return 'url error';
    }
    global $input_config;

    if (!isset($input_config[$path_pieces[0]][$path_pieces[1]])) {
        return 'url error';
    } else {
        $para = $input_config[$path_pieces[0]][$path_pieces[1]];
    }

    //输入值判断
    try {
        Validation::validate(array_merge($_POST, $_FILES), $para);
    } catch (Exception $e) {
        return $e->getMessage();
    }

    return 'true';
}

/**
 * 生成随机字符串
 *
 * @param int $length 生成字符串长度
 *
 * @return string
 */
function random($length)
{
    $key = '';
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
    for ($i = 0; $i < $length; $i++) {
        $key .= $pattern{
            mt_rand(0, 61)}; //生成php随机数
    }
    return $key;
}

/**
 * 生成随机数字串
 *
 * @param int $length 生成数字串长度
 *
 * @return void
 */
function randomNum($length)
{
    $key = '';
    for ($i = 0; $i < $length; $i++) {
        $key .= mt_rand(0, 9); //生成php随机数
    }
    return $key;
}

/**
 * PBKDF2 key derivation function
 *
 * @param string  $algorithm  The hash algorithm to use. Recommended: SHA256
 * @param string  $password   The password
 * @param string  $salt       A salt that is unique to the password
 * @param int     $count      Iteration count. Higher is better, but slower. Recommended: At least 1000
 * @param int     $key_length The length of the derived key in bytes
 * @param boolean $raw_output If true, the key is returned in raw binary format. Hex encoded otherwise
 *
 * @return string A $key_length-byte key derived from the password and salt
 */
function pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output = false)
{
    $algorithm = strtolower($algorithm);
    if (!in_array($algorithm, hash_algos(), true)) {
        trigger_error('PBKDF2 ERROR: Invalid hash algorithm.', E_USER_ERROR);
    }

    if ($count <= 0 || $key_length <= 0) {
        trigger_error('PBKDF2 ERROR: Invalid parameters.', E_USER_ERROR);
    }

    if (function_exists("hash_pbkdf2")) {
        // The output length is in NIBBLES (4-bits) if $raw_output is false!
        if (!$raw_output) {
            $key_length = $key_length * 2;
        }
        return hash_pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output);
    }

    $hash_length = strlen(hash($algorithm, "", true));
    $block_count = ceil($key_length / $hash_length);

    $output = "";
    for ($i = 1; $i <= $block_count; $i++) {
        // $i encoded as 4 bytes, big endian.
        $last = $salt . pack("N", $i);
        // first iteration
        $last = $xorsum = hash_hmac($algorithm, $last, $password, true);
        // perform the other $count - 1 iterations
        for ($j = 1; $j < $count; $j++) {
            $xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
        }
        $output .= $xorsum;
    }

    if ($raw_output) {
        return substr($output, 0, $key_length);
    } else {
        return bin2hex(substr($output, 0, $key_length));
    }
}

/**
 * 生成pbkdf2加密的密码和盐
 *
 * @param string $psw 待加密密码
 *
 * @return array 包含salt与passwd
 */
function encryptPsw($psw)
{
    $bytes = openssl_random_pseudo_bytes(32);
    $salt = bin2hex($bytes);
    $pswEncrypted = pbkdf2('SHA256', $psw, $salt, 1000, 32);
    return array(
        'salt' => $salt,
        'passwd' => $pswEncrypted,
    );
}

/**
 * 用星号隐藏字符串中间部分
 *
 * @param string $str 字符串
 *
 * @return string 处理后字符串
 */
function hideStr($str)
{
    //判断是否包含中文字符
    if (preg_match("/[\x{4e00}-\x{9fa5}]+/u", $str)) {
        //按照中文字符计算长度
        $len = mb_strlen($str, 'UTF-8');
        //echo '中文';
        if ($len >= 3) {
            //三个字符或三个字符以上掐头取尾，中间用*代替
            $str = mb_substr($str, 0, 1, 'UTF-8') . '*' . mb_substr($str, -1, 1, 'UTF-8');
        } elseif ($len == 2) {
            //两个字符
            $str = mb_substr($str, 0, 1, 'UTF-8') . '*';
        }
    } else {
        //按照英文字串计算长度
        $len = strlen($str);
        //echo 'English';
        if ($len >= 3) {
            //三个字符或三个字符以上掐头取尾，中间用*代替
            $str = substr($str, 0, 1) . '*' . substr($str, -1);
        } elseif ($len == 2) {
            //两个字符
            $str = substr($str, 0, 1) . '*';
        }
    }
    return $str;
}


use Qiniu\Storage\UploadManager;
use Qiniu\Auth;

/**
 * 上传文件至七牛云
 *
 * @param string $name 文件路径
 *
 * @return array 七牛云的key与文件md5
 */
function uploadFile($name)
{
    global $config;
    $qiniu_cfg = $config['qiniu-sdk'];
    $accessKey = $qiniu_cfg['accessKey'];
    $secretKey = $qiniu_cfg['secretKey'];
    $bucket = $qiniu_cfg['bucket'];

    //获取文件
    $extension = '.' . pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION);
    $tmp = $_FILES[$name]['tmp_name'];
    $md5 = md5_file($_FILES[$name]['tmp_name']);
    $key = (string) time() . '_' . random(10) . $extension;

    //上传
    $auth = new Auth($accessKey, $secretKey);
    $token = $auth->uploadToken($bucket);
    $uploadMgr = new UploadManager();
    $uploadMgr->putFile($token, $key, $tmp);

    //返回
    return array(
        'key' => $key,
        'md5' => $md5,
    );
}

/**
 * 文件下载
 *
 * @param string $file 服务器内文件路径
 *
 * @return bool
 */
function fileDownload($file)
{
    if (file_exists($file)) {
        header("Content-type:application/octet-stream");
        $filename = basename($file);
        header("Content-Disposition:attachment;filename = " . $filename);
        header("Accept-ranges:bytes");
        header("Accept-length:" . filesize($file));
        readfile($file);
        return true;
    } else {
        return false;
    }
}

/**
 * 构建压缩的列表型返回值
 *
 * @param array $res   列表型返回值
 * @param int   $total 总条数
 *
 * @return void
 */
function buildPackedRet($res, $total = null)
{
    $ret = array();
    if ($total) {
        $ret['total'] = $total;
    }
    $ret['keys'] = array_keys($res[0]);
    $ret['values'] = array();
    foreach ($res as $v) {
        $tmp = array();
        foreach ($ret['keys'] as $k) {
            array_push($tmp, $v[$k]);
        }
        array_push($ret['values'], $tmp);
    }
    return $ret;
}
