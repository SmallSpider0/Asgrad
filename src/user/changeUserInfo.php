<?php
namespace asgrad\user;

class changeUserInfo
{
    private $table = 'user_info';

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $updateData = array();
        if (isset($_POST['retest_times'])) {
            $updateData['retest_times'] = $_POST['retest_times'];
        }
        if (isset($_POST['order_close_timeout'])) {
            $updateData['order_close_timeout'] = $_POST['order_close_timeout'];
        }
        $db->where('user_id', $user_id);
        if (!$db->update($this->table, $updateData)) {
            $db->rollback();
            msg(402, $db->getLastError());
            return;
        }
        msg(200);
    }
}
