<?php
namespace asgrad\util;

class heartBeat
{
    private $table1 = "admin_login";
    private $table2 = "user_login_web";
    private $table3 = "user_login_pc";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];

        //更新心跳时间
        $db->where('id', $user_id);
        $updateData = array(
            'last_heartbeat' => time(),
        );
        //1 管理员 2 web 3 pc
        if ($ROLE == 1) {
            $tb = $this->table1;
        } elseif ($ROLE == 2) {
            $tb = $this->table2;
        } elseif ($ROLE == 3) {
            $tb = $this->table3;
        }
        dbUpdate($tb, $updateData);
    }

}
