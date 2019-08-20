<?php

class getLogInfo
{
    private $_table1 = "log_test";
    private $_table2 = "log_debug";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $mode = $_POST['mode'];
        $id = $_POST['id'];

        $db->where('user_id', $user_id);
        $db->where('id', $id);
        if ($mode == 0) {
            $tb = $this->_table1;
        } else {
            $tb = $this->_table2;
        }
        db_getone($tb, 'res', '', 'test_item, test_log');
    }
}
