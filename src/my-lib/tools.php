<?php
// 制定允许其他域名访问
header("Access-Control-Allow-Origin:*");
// 响应类型
header('Access-Control-Allow-Methods:POST');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with, content-type');

require_once Root_Path . '/config.php';
require_once Root_Path . '/input_config.php';
require_once Root_Path . '/au_config.php';
require_once Root_Path . "/vendor/autoload.php";

use Qiniu\Auth;

global $db;

db_connect();

function db_getone($_table, $ret = '', $err_ret = '', $cl = '*')
{
    global $db;
    $res = $db->getOne($_table, $cl);
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

function db_insert($_table, $data, $ret = '')
{
    global $db;
    if ($res = $db->insert($_table, $data)) {
        if ($ret == 'res') {
            $ret = $res;
        }
        msg(200, $ret);
    } else {
        msg(402, $db->getLastError());
    }
}

function db_update($_table, $data, $ret = '')
{
    global $db;
    if ($res = $db->update($_table, $data)) {
        if ($ret == 'res') {
            $ret = $res;
        }
        msg(200, $ret);
    } else {
        msg(402, $db->getLastError());
    }
}

function db_delete($_table)
{
    global $db;
    if ($db->delete($_table)) {
        msg(200);
    } else {
        msg(402, $db->getLastError());
    }
}

function db_connect()
{
    global $db;
    global $config;
    $dbcfg = $config['dbconfig'];
    $db = new MysqliDb($dbcfg['dbhost'], $dbcfg['dbuser'], $dbcfg['dbpsw'], $dbcfg['dbname']);
}

function check_var(&$var, $default = '')
{
    if (!isset($var) and $default != '') {
        $var = $default;
    }
    return isset($var);
}

function check_token($id, $api_key, $timestamp, $sign, $url, $auth)
{
    $_tables = ["admin_login", "user_login_web", "user_login_pc"];
    global $db;
    global $config;
    if (check_var($id) and check_var($api_key) and check_var($timestamp) and check_var($sign)) {
        //判断调用接口的角色
        foreach ($auth as $value) {
            $db->where('id', $id)->where('api_key', $api_key);
            $res = $db->getOne($_tables[$value - 1], "security_key, time_out");
            if ($res) {
                $_table = $_tables[$value - 1];
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
                    $db->update($_table, $updateData);
                }
                return $role; //1 管理员 2 web 3 pc
            }
        }
    }
    return false;
}

function msg($code, $msg = 'success')
{
    $arr = array(
        'code' => $code,
        'msg' => $msg,
    );
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
}

function check_input($path_pieces)
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

function randomNum($length)
{
    $key = '';
    for ($i = 0; $i < $length; $i++) {
        $key .= mt_rand(0, 9); //生成php随机数
    }
    return $key;
}

/*
 * PBKDF2 key derivation function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
 * $algorithm - The hash algorithm to use. Recommended: SHA256
 * $password - The password.
 * $salt - A salt that is unique to the password.
 * $count - Iteration count. Higher is better, but slower. Recommended: At least 1000.
 * $key_length - The length of the derived key in bytes.
 * $raw_output - If true, the key is returned in raw binary format. Hex encoded otherwise.
 * Returns: A $key_length-byte key derived from the password and salt.
 *
 * Test vectors can be found here: https://www.ietf.org/rfc/rfc6070.txt
 *
 * This implementation of PBKDF2 was originally created by https://defuse.ca
 * With improvements by http://www.variations-of-shadow.com
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
use WebGeeker\Validation\Validation;

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

function build_packed_ret($res, $total = null)
{
    $ret = array();
    if ($total) {
        $ret['total'] = $total;
    }
    ;
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
