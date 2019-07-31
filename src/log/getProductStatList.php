<?php

class getProductStatList
{
    private $table1 = "orders";
    private $table2 = "product";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $order_num = $_POST['order_num'];
        $page = $_POST['page'];
        $pageLimit = $_POST['pageLimit'];

        //订单存在
        $db->where('user_id', $user_id)->where('order_num', $order_num);
        $res_orders = $db->getOne($this->table1, 'station_cnt');
        if (!$res_orders) {
            msg(403, '不合法的调用');
            return;
        }

        //获取总条数
        $db->where('order_num', $order_num);
        if (isset($_POST['sn'])) {
            $db->where('sn', $_POST['sn']);
        } else {
            if (isset($_POST['at_station'])) {
                $db->where('at_station', $_POST['at_station']);
            }
            if (isset($_POST['status'])) {
                $db->where('status', $_POST['status']);
            }
        }
        $total = $db->getValue($this->table2, "count(*)");

        //查询
        $db->where('order_num', $order_num);
        if (isset($_POST['sn'])) {
            $db->where('sn', $_POST['sn']);
        } else {
            if (isset($_POST['at_station'])) {
                $db->where('at_station', $_POST['at_station']);
            }
            if (isset($_POST['status'])) {
                $db->where('status', $_POST['status']);
            }
        }


        //构建查询sql
        $tmp = array();
        for ($i = 1; $i <= $res_orders['station_cnt']; $i++) {
            array_push($tmp, "test_cnt_$i, test_time_$i");
        }
        $sqlStr = join(", ", $tmp);

        //分页
        $db->pageLimit = $pageLimit;
        $res = $db->arraybuilder()->paginate($this->table2, $page, $sqlStr);
        if (!$res) {
            msg(402, $db->getLastError());
            return;
        }

        //构建返回值
        $ret = build_packed_ret($res, $total);
        msg(200, $ret);
    }
}
