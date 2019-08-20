<?php
namespace asgrad\log;

class getProductStatus
{
    private $_table1 = "orders";
    private $_table2 = "product";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $order_num = $_POST['order_num'];
        $sn = $_POST['sn'];

        //订单状态为执行中
        $db->where('user_id', $user_id)->where('order_num', $order_num);
        $res = $db->getOne($this->_table1, 'status');
        if (!$res) {
            msg(403, '不合法的调用');
            return;
        }
        if ($res['status'] != 1) {
            msg(403, '不合法的调用');
            return;
        }

        //查询
        $db->where('order_num', $order_num)->where('sn', $sn);
        db_getone($this->_table2, 'res', '', 'status, at_station');
    }
}
