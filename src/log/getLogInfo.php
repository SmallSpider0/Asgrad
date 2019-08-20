<?php
namespace asgrad\log;

class getLogInfo
{
    private $table1 = "log_test";
    private $table2 = "log_debug";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $mode = $_POST['mode'];
        $id = $_POST['id'];

        $db->where('user_id', $user_id);
        $db->where('id', $id);
        if ($mode == 0) {
            $tb = $this->table1;
        } else {
            $tb = $this->table2;
        }
        dbGetOne($tb, 'res', '', 'test_item, test_log');
    }
}
