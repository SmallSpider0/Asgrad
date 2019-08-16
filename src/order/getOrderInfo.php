<?php

class getOrderInfo
{
    private $table = "orders";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $order_num = $_POST['order_num'];

        $db->where('user_id', $user_id)->where('order_num', $order_num);
        db_getone($this->table, 'res', '', 'working_procedure, test_seq');
    }
}
