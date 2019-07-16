<?php

class getOrderHIdList
{
    private $table1 = "admin_login";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];

        $db->where('id', $user_id)->where('time_out', time(), '>');//还没过期
        $updateData = array(
            'time_out' => time(),
        );
        
    }
}
