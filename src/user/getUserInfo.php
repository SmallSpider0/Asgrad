<?php

class getUserInfo
{
    private $table = "user_info";

    public function run($ROLE)
    {
        echo $ROLE;
        global $db;
        $user_id = $_POST['user_id'];
        $db->where('user_id', $user_id);
        db_getone($this->table, 'res', '', 'company_name, register_time, order_cnt, retest_times, order_close_timeout');
    }
}
