<?php

class deleteCDK
{
    private $_table = "cd_key";

    public function run($ROLE)
    {
        global $db;
        $id = $_POST['id'];

        //-------------------【事务开始】-------------------
        $db->startTransaction();

        $db->setQueryOption('FOR UPDATE')->where('id', $id); //加锁
        $res = $db->getOne($this->_table, 'status');
        if (isset($res['status']) && $res['status'] == 0) {
            $updateData = array(
                "status" => 2,
            );
            $db->where('id', $id);
            if (!$db->update($this->_table, $updateData)) {
                $db->rollback();
                msg(402, $db->getLastError());
                return;
            }
        } else {
            $db->rollback();
            msg(401, '不合法的调用');
            return;
        }

        //-------------------【事务提交】-------------------
        $db->commit();
        msg(200);
    }
}
