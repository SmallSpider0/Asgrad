<?php
namespace asgrad\user;

class ChangePasswd
{
    private $table1 = 'user_login_web';
    private $table2 = 'user_login_pc';

    public function run($ROLE)
    {
        global $db;
        $mode = $_POST['mode']; //模式（0修改web密码 1修改pc密码）
        $user_id = $_POST['user_id'];
        $old_passwd = $_POST['old_passwd'];
        $new_passwd = $_POST['new_passwd'];

        if ($mode == 0) {
            $tb = $this->table1;
        } else {
            $tb = $this->table2;
        }

        //验证密码正确性
        $db->where('id', $user_id);
        $res = $db->getOne($tb, 'salt, passwd');
        if (pbkdf2('SHA256', $old_passwd, $res['salt'], 1000, 32) != $res['passwd']) {
            msg(403, '旧密码错误');
            return;
        }

        //修改密码
        $PSW = encryptPsw($new_passwd); //对密码进行加密
        $updateData = array(
            "salt" => $PSW['salt'],
            "passwd" => $PSW['passwd'],
        );
        $db->where('id', $user_id);
        dbUpdate($tb, $updateData);
    }
}
