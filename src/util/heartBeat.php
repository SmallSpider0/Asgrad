<?php

class heartBeat
{
    private $_table1 = "admin_login";
    private $_table2 = "user_login_web";
    private $_table3 = "user_login_pc";

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
            $tb = $this->_table1;
        } elseif ($ROLE == 2) {
            $tb = $this->_table2;
        } elseif ($ROLE == 3) {
            $tb = $this->_table3;
        }
        db_update($tb, $updateData);
    }

}
