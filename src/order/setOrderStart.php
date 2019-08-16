<?php

class setOrderStart
{
    private $table = "orders";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $order_num = $_POST['order_num'];

        //-------------------【事务开始】-------------------
        $db->startTransaction();

        $db->where('user_id', $user_id)->where('order_num', $order_num);
        $db->setQueryOption('FOR UPDATE');
        $res = $db->getOne($this->table, 'status');
        if (!$res) {
            $db->rollback();
            msg(403, '不合法的调用');
            return;
        }
        if ($res['status'] == 0) { //未执行
            $updateData = array(
                'status' => 1, //执行中
            );
        } else {
            $db->rollback();
            msg(403, '不合法的调用');
            return;
        }

        $db->where('order_num', $order_num);
        if (!$db->update($this->table, $updateData)) {
            $db->rollback();
            msg(402, $db->getLastError());
            return;
        }

        //-------------------【事务提交】-------------------
        $db->commit();
        msg(200);
    }
}
