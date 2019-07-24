<?php

class confirmHardwareId
{
    private $table1 = "orders";
    private $table2 = "hardware_id";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $order_num = $_POST['order_num'];
        $id = $_POST['id'];

        //订单状态为执行中
        $db->where('user_id', $user_id)->where('order_num', $order_num);
        $res = $db->getOne($this->table1, 'status');
        if (!$res) {
            msg(403, '不合法的调用');
            return;
        }
        if ($res['status'] != 1) {
            msg(403, '不合法的调用');
            return;
        }

        //更新
        $updateData = array(
            'status' => 2,
        );
        $db->where('status', 1)->where('id', $id);
        if (!$db->update($this->table2, $updateData)) {
            msg(402, $db->getLastError());
            return;
        }

        msg(200);
    }
}
