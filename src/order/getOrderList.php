<?php

class getOrderList
{
    private $table = "orders";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $page = $_POST['page'];
        $pageLimit = $_POST['pageLimit'];

        //获取总条数
        $db->where('user_id', $user_id);
        if (isset($_POST['order_num'])) {
            $db->where('order_num', $_POST['order_num']);
        } else {
            if (isset($_POST['status'])) {
                $db->where('status', $_POST['status']);
            }
            if (isset($_POST['made_in'])) {
                $db->where('made_in', $_POST['made_in']);
            }
            if (isset($_POST['date_start'])) {
                $db->where('add_time', $_POST['date_start'], '>');
            }
            if (isset($_POST['date_end'])) {
                $db->where('add_time', $_POST['date_end'], '<');
            }
        }
        $total = $db->getValue($this->table, "count(*)");

        //查询
        $db->where('user_id', $user_id);
        if (isset($_POST['order_num'])) {
            $db->where('order_num', $_POST['order_num']);
        } else {
            if (isset($_POST['status'])) {
                $db->where('status', $_POST['status']);
            }
            if (isset($_POST['made_in'])) {
                $db->where('made_in', $_POST['made_in']);
            }
            if (isset($_POST['date_start'])) {
                $db->where('add_time', $_POST['date_start'], '>');
            }
            if (isset($_POST['date_end'])) {
                $db->where('add_time', $_POST['date_end'], '<');
            }
        }

        $db->orderBy('add_time');
        $db->pageLimit = $pageLimit;
        $res = $db->arraybuilder()->paginate($this->table, $page, 'order_num, status, made_in, product_name, product_model, quantity, complete_quantity, station_cnt, plan_online_time, add_time');
        if (!$res) {
            msg(402, $db->getLastError());
            return;
        }
        $ret = build_packed_ret($res, $total);
        msg(200, $ret);
    }
}
