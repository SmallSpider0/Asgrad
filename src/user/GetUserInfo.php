<?php
namespace asgrad\user;

class GetUserInfo
{
    private $table = "user_info";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $db->where('user_id', $user_id);
        dbGetOne($this->table, 'res', '', 'company_name, register_time, order_cnt, retest_times, order_close_timeout');
    }
}
