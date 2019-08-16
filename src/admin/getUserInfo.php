<?php

class getUserInfo
{
    private $table = "user_info_all";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_uid'];

        $db->where('user_id', $user_id);
        db_getone($this->table, 'res');
    }
}
