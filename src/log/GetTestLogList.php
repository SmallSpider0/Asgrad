<?php

namespace asgrad\log;

class GetTestLogList
{
    private $table = "log_test";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $page = $_POST['page'];
        $pageLimit = $_POST['pageLimit'];

        //获取总条数
        $db->where('user_id', $user_id);
        if (isset($_POST['sn'])) {
            $db->where('sn', '%' . $_POST['sn'] . '%', 'like');
        }
        if (isset($_POST['order_num'])) {
            $db->where('order_num', '%' . $_POST['order_num'] . '%', 'like');
        }
        if (isset($_POST['made_in'])) {
            $db->where('made_in', '%' . $_POST['made_in'] . '%', 'like');
        }
        if (isset($_POST['date_start'])) {
            $db->where('add_time', $_POST['date_start'], '>');
        }
        if (isset($_POST['date_end'])) {
            $db->where('add_time', $_POST['date_end'], '<');
        }
        if (isset($_POST['error_code'])) {
            $db->where('error_code', $_POST['error_code']);
        }
        if (isset($_POST['station'])) {
            $db->where('station', $_POST['station']);
        }
        $total = $db->getValue($this->table, "count(*)");

        //查询
        $db->where('user_id', $user_id);
        if (isset($_POST['sn'])) {
            $db->where('sn', '%' . $_POST['sn'] . '%', 'like');
        }
        if (isset($_POST['order_num'])) {
            $db->where('order_num', '%' . $_POST['order_num'] . '%', 'like');
        }
        if (isset($_POST['made_in'])) {
            $db->where('made_in', '%' . $_POST['made_in'] . '%', 'like');
        }
        if (isset($_POST['date_start'])) {
            $db->where('add_time', $_POST['date_start'], '>');
        }
        if (isset($_POST['date_end'])) {
            $db->where('add_time', $_POST['date_end'], '<');
        }
        if (isset($_POST['error_code'])) {
            $db->where('error_code', $_POST['error_code']);
        }
        if (isset($_POST['station'])) {
            $db->where('station', $_POST['station']);
        }

        $db->orderBy('add_time');
        $db->pageLimit = $pageLimit;
        $res = $db->arraybuilder()->paginate($this->table, $page, 'id, order_num, sn, station, made_in, result, error_code, start_time, end_time, add_time');
        if (!$res) {
            msg(402, $db->getLastError());
            return;
        }

        //构建返回值
        $ret = buildPackedRet($res, $total);
        msg(200, $ret);
    }
}
