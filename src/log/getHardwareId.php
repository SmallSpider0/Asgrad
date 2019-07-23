<?php

//!!!!!!!!!!未完成！！！！！！！！
class getHardwareId
{
    private $table1 = "orders";
    private $table2 = "hardware_id";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $order_num = $_POST['order_num'];
        $type = $_POST['type'];

        //判断是否合法
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


        //-------------------【事务开始】-------------------
        $db->startTransaction();

        //加锁
        $db->setQueryOption('FOR UPDATE')->where('status', 0);
        $res = $db->getOne($this->table2, 'data');
        if (!$res) {
            msg(404, '硬件id不足');
            msg(402, $db->getLastError());
            return;
        }

        //-------------------【事务提交】-------------------
        $db->commit();
        msg(200, $res);
    }
}
