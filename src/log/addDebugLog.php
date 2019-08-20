<?php
namespace asgrad\log;

class addDebugLog
{
    private $table = "log_debug";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $log_id = $_POST['log_id'];
        $station = $_POST['station'];
        $result = $_POST['result'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $test_item = $_POST['test_item'];
        $test_log = $_POST['test_log'];

        //插入用户信息表
        $inData = array(
            'user_id' => $user_id,
            'log_id' => $log_id,
            'station' => $station,
            'result' => $result,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'test_item' => $test_item,
            'test_log' => $test_log,
        );

        //错误代码
        if (isset($_POST['error_code']) && $result == '0') {
            $inData['error_code'] = $_POST['error_code'];
        }

        if (!$db->insert($this->table, $inData)) {
            msg(402, $db->getLastError());
            return;
        }
        msg(200);
    }
}
