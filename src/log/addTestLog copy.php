<?php

class addTestLog
{
    private $table1 = "product";
    private $table2 = "orders";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $order_num = $_POST['order_num'];
        $sn = $_POST['sn'];
        $station = strtoupper($_POST['station']);
        $result = $_POST['result'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $test_item = $_POST['test_item'];
        $test_log = $_POST['test_log'];

        //订单状态为执行中
        $db->where('user_id', $user_id)->where('order_num', $order_num);
        $res = $db->getOne($this->table2, 'status');
        if (!$res) {
            msg(403, '不合法的调用');
            return;
        }
        if ($res['status'] != 1) {
            msg(403, '不合法的调用');
            return;
        }

        //解析日志中测试站
        if (preg_match('/(\D{2})(\d{1,2})/', $station, $matches)) {
            if (isset($matches[1]) && isset($matches[2])) {
                $station_now = $matches;
            } else {
                msg(400, 'station格式有误');
                return;
            }
        }

        //-------------------【事务开始】-------------------
        $db->startTransaction();

        //0.-----------------插入日志表-----------------
        $inData = array(
            'user_id' => $user_id,
            'order_num' => $order_num,
            'sn' => $sn,
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
            $db->rollback();
            msg(402, $db->getLastError());
            return;
        }

        //获取当前测试站
        $sqlstr = '';
        if ($station_now[1] == 'FT') {
            $sqlstr = ', test_time' . $station_now[2] . ', test_cnt' . $station_now[2];
        }
        $db->setQueryOption('FOR UPDATE'); //加锁
        $db->where('order_num', $order_num)->where('sn', $sn);
        $res_product = $db->getOne($this->table1, 'status, at_station' . $sqlstr);
        if (!$res_product) {
            //产品信息还未写入数据库
            $inData = array(
                'order_num' => $order_num,
                'sn' => $sn,
                'at_station' => $station,
                'status' => !$result,
            );
            //插入数据库
            if (!$db->insert($this->table1, $inData)) {
                $db->rollback();
                msg(402, $db->getLastError());
                return;
            }
        }

        //1.-----------------若目前状态为返修中，且日志为该测试站对应返修站的测试成功日志，则更新【产品表】产品状态为返修待复测-----------------

        preg_match('/\D{2}(\d{1,2})/', $res_product['at_station'], $matches);
        if ($station_now[1] == 'RT') { //返修日志
            if ($res_product['status'] == 1 && $matches[1] == $station_now[2]) { //测试站序号等于返修站序号
                $updateData = array(
                    'status' => 3
                );
                if (!$db->update($this->table1, $updateData)) {
                    $db->rollback();
                    msg(402, $db->getLastError());
                    return;
                }
            }
        } else {
            //2.-----------------测试日志-----------------

        }





        //-------------------【事务提交】-------------------
        $db->commit();
        msg(200);
    }
}
