<?php
header('Access-Control-Allow-Origin:*');

require_once Root_Path . '/config.php';
require_once Root_Path . '/input_config.php';
require_once Root_Path . '/au_config.php';
require_once Root_Path . "/vendor/autoload.php";

use WebGeeker\Validation\Validation;

global $db;

db_connect();

function db_getone($table, $ret = '', $err_ret = '', $cl = '*')
{
    global $db;
    if ($res = $db->getOne($table, $cl)) {
        if ($ret == 'res') {
            $ret = $res;
        }
        msg(200, $ret);
    } else {
        msg(401, $err_ret);
    }
}

function db_insert($table, $data, $ret = '')
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

function db_update($table, $data, $ret = '')
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

function db_delete($table)
{
    global $db;
    if ($db->delete($table)) {
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


function check_token($id, $api_key, $timestamp, $sign, $url)
{
    $table = "user";
    global $db;
    global $config;
    if (check_var($id) and check_var($api_key) and check_var($timestamp) and check_var($sign)) {
        $db->where('id', $id)->where('api_key', $api_key);
        $res = $db->getOne($table, "security_key, time_out");
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
                return true;
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
        Validation::validate($_POST, $para);
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
        'password' => $pswEncrypted,
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
