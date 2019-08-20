<?php
namespace asgrad\admin;

class Login
{
    private $table = "admin_login";

    public function run($ROLE)
    {
        global $db;
        global $config;
        $account = $_POST['account'];
        $passwd = $_POST['passwd'];

        $db->where('account', $account);
        $res = $db->getOne($this->table, 'id, salt, passwd, day_login_err_count');

        //判断连续登陆错误次数
        if ($res['day_login_err_count'] <= $config['security']['login']) {
            //判断密码正确性
            if (pbkdf2('SHA256', $passwd, $res['salt'], 1000, 32) != $res['passwd']) {
                $db->rawQuery('update ' . $this->table . ' set day_login_err_count = day_login_err_count + 1 where `id` = ?', [$res['id']]);
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
                if (!$db->update($this->table, $updateData)) {
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
            msg(404, '当日连续登陆失败次数超限');
        }
    }
}
