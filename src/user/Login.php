<?php
namespace asgrad\user;

class Login
{
    private $table1 = "user_login_web";
    private $table2 = "user_login_pc";

    public function run($ROLE)
    {
        global $db;
        global $config;
        $mode1 = $_POST['mode1'];
        $mode2 = $_POST['mode2'];
        $account = $_POST['account'];
        $passwd = $_POST['passwd'];

        if ($mode1 == '0') {
            $db->where('phone', $account);
        } else {
            $db->where('email', $account);
        }

        if ($mode2 == '0') { //web登录
            $tb = $this->table1;
            $res = $db->getOne($tb, 'id, salt, passwd, day_login_err_count, grant_time_out');
        } else { //pc登录
            $tb = $this->table2;
            $res_web = $db->getOne($this->table1, 'id, grant_time_out'); //从web表获取用户id和授权时间
            $db->where('id', $res_web['id']);
            $res = $db->getOne($tb, 'id, salt, passwd, day_login_err_count'); //从与web用户绑定的pc用户表获取登录信息
            $res['grant_time_out'] = $res_web['grant_time_out'];
        }
        //判断授权时间
        if ($res['grant_time_out'] < time()) {
            msg(404, '授权已过期');
            return;
        }

        //判断连续登陆错误次数
        if ($res['day_login_err_count'] <= $config['security']['login']) {
            //判断密码正确性
            if (pbkdf2('SHA256', $passwd, $res['salt'], 1000, 32) != $res['passwd']) {
                $db->rawQuery('update ' . $tb . ' set day_login_err_count = day_login_err_count + 1 where `id` = ?', [$res['id']]);
                msg(401, '账号或密码错误');
            } else {
                //开始事务
                $db->startTransaction();
                $api_key = random(24);
                $security_key = random(32);
                $updateData = array(
                    "api_key" => $api_key,
                    "security_key" => $security_key,
                    'last_heartbeat' => time(),
                    "time_out" => time() + 7200, //2小时
                    "day_login_err_count" => 0,
                );
                $db->where('id', $res['id']);
                if (!$db->update($tb, $updateData)) {
                    $db->rollback();
                    msg(402, $db->getLastError());
                    return;
                }

                //提交事务
                $retData = array(
                    "id" => $res['id'],
                    "api_key" => $api_key,
                    "security_key" => $security_key,
                );
                $db->commit();
                msg(200, $retData);
            }
        } else {
            msg(405, '当日连续登陆失败次数超限');
        }
    }
}
