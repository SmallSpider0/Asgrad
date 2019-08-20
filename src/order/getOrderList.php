<?php

class getOrderList
{
    private $_table = "orders";

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
            if (isset($_POST['product_name'])) {
                $db->where('product_name', $_POST['product_name']);
            }
            if (isset($_POST['product_model'])) {
                $db->where('product_model', $_POST['product_model']);
            }
            if (isset($_POST['status'])) {
                $db->where('status', $_POST['status']);
            }
            if (isset($_POST['made_in'])) {
                $db->where('made_in', $_POST['made_in']);
            }
            if (isset($_POST['date_start'])) {
                $db->where('plan_online_time', $_POST['date_start'], '>');
            }
            if (isset($_POST['date_end'])) {
                $db->where('plan_online_time', $_POST['date_end'], '<');
            }
        }
        $total = $db->getValue($this->_table, "count(*)");

        //查询
        $db->where('user_id', $user_id);
        if (isset($_POST['order_num'])) {
            $db->where('order_num', $_POST['order_num']);
        } else {
            if (isset($_POST['product_name'])) {
                $db->where('product_name', $_POST['product_name']);
            }
            if (isset($_POST['product_model'])) {
                $db->where('product_model', $_POST['product_model']);
            }
            if (isset($_POST['status'])) {
                $db->where('status', $_POST['status']);
            }
            if (isset($_POST['made_in'])) {
                $db->where('made_in', $_POST['made_in']);
            }
            if (isset($_POST['date_start'])) {
                $db->where('plan_online_time', $_POST['date_start'], '>');
            }
            if (isset($_POST['date_end'])) {
                $db->where('plan_online_time', $_POST['date_end'], '<');
            }
        }

        $db->orderBy('plan_online_time');
        $db->pageLimit = $pageLimit;
        $res = $db->arraybuilder()->paginate($this->_table, $page, 'order_num, status, made_in, product_name, product_model, quantity, complete_quantity, station_cnt, plan_online_time, add_time');
        if (!$res) {
            msg(402, $db->getLastError());
            return;
        }
        $ret = build_packed_ret($res, $total);
        msg(200, $ret);
    }
}
