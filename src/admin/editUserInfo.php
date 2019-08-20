<?php
namespace asgrad\admin;

class editUserInfo
{
    private $_table1 = "user_login_web";
    private $_table2 = "user_info";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_uid'];

        $updateData1 = array();
        $updateData2 = array();
        if (isset($_POST['grant_time_out'])) {
            $updateData1['grant_time_out'] = strtotime($_POST['grant_time_out']);
        }
        if (isset($_POST['remark'])) {
            $updateData2['remark'] = $_POST['remark'];
        }

        //-------------------【事务开始】-------------------
        $db->startTransaction();

        //更新用户登录表
        if (count($updateData1) > 0) {
            $db->where('id', $user_id);
            if (!$db->update($this->_table1, $updateData1)) {
                $db->rollback();
                msg(402, $db->getLastError());
                return;
            }
        }

        //更新用户信息表
        if (count($updateData2) > 0) {
            $db->where('user_id', $user_id);
            if (!$db->update($this->_table2, $updateData2)) {
                $db->rollback();
                msg(402, $db->getLastError());
                return;
            }
        }

        //-------------------【事务提交】-------------------
        $db->commit();
        msg(200);
    }
}
