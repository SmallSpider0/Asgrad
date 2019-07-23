<?php

class getDebugLogList
{
    private $table = "log_debug";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $page = $_POST['page'];
        $pageLimit = $_POST['pageLimit'];

        $db->where('user_id', $user_id);
        if (isset($_POST['log_id'])) {
            $db->where('log_id', $_POST['log_id']);
        }
        if (isset($_POST['date_start'])) {
            $db->where('add_time', $_POST['date_start'], '>');
        }
        if (isset($_POST['date_end'])) {
            $db->where('add_time', $_POST['date_end'], '<');
        }

        $db->orderBy('add_time');
        $db->pageLimit = $pageLimit;
        $res = $db->arraybuilder()->paginate($this->table, $page, 'id, log_id, station, result, error_code, start_time, end_time, add_time');
        if (!$res) {
            msg(402, $db->getLastError());
            return;
        }

        //构建返回值
        $ret = build_packed_ret($res);
        msg(200, $ret);
    }
}
