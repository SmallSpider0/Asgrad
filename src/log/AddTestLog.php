<?php

namespace asgrad\log;

class AddTestLog
{
    private $table1 = "orders";
    private $table2 = "log_test";
    private $table3 = "product";
    private $table4 = "user_info";
    private $table5 = "error_code";
    private $table6 = "order_info";

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

        //-------判断接口调用合法性-------

        $db->where('user_id', $user_id)->where('order_num', $order_num);
        $res_orders = $db->getOne($this->table1, 'status, station_cnt, made_in, rt_list');
        if (!$res_orders) {
            msg(403, '不合法的调用');
            return;
        }
        if ($res_orders['status'] != 1) {
            msg(403, '不合法的调用');
            return;
        }
        $rt_list = explode(",", $res_orders['rt_list']);

        //-------解析输入数据相关字段-------

        if (preg_match('/(\D{2})(\d{1,2})/', $station, $matches)) {
            if (isset($matches[1]) && isset($matches[2]) && in_array($matches[1], ['FT', 'RT'])) {
                $station_now = $matches;
            } else {
                msg(400, 'station有误');
                return;
            }
        }
        $test_dur = bcsub($end_time, $start_time);
        if (bccomp($test_dur, 0) < 0) {
            msg(400, '测试开始结束时间有误');
            return;
        }
        if ($station_now[2] > $res_orders['station_cnt']) {
            msg(400, 'station有误');
            return;
        }

        //-------日志写入日志表（去重）-------

        $inData1 = array(
            'user_id' => $user_id,
            'order_num' => $order_num,
            'sn' => $sn,
            'station' => $station,
            'made_in' => $res_orders['made_in'],
            'result' => $result,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'test_item' => $test_item,
            'test_log' => $test_log,
        );
        if (isset($_POST['error_code']) && $result == '0') { //结果为失败且有错误代码才插入错误代码
            $inData1['error_code'] = $_POST['error_code'];
            $error_code = $_POST['error_code'];
        }
        if (!$db->insert($this->table2, $inData1)) {
            msg(402, $db->getLastError());
            return;
        }

        //-------------------【事务开始】-------------------
        $db->startTransaction();

        $db->setQueryOption('FOR UPDATE'); //加锁
        $db->where('user_id', $user_id)->where('order_num', $order_num);
        $res_orders = $db->getOne($this->table1, 'status, station_cnt, complete_quantity, made_in');

        //-------查询产品表获取相应信息（并加锁）-------

        $sqlstr = ''; //构建查询sql
        if ($station_now[1] == 'FT') {
            $test_time = 'test_time_' . $station_now[2];
            $test_cnt = 'test_cnt_' . $station_now[2];
            $sqlstr = ", $test_time, $test_cnt";
        }
        $db->setQueryOption('FOR UPDATE'); //加锁
        $db->where('order_num', $order_num)->where('sn', $sn);
        $res_product = $db->getOne($this->table3, 'status, at_station' . $sqlstr);

        //-------RT日志：若目前状态为返修中，且日志为该测试站对应返修站的测试成功日志，则更新【产品表】产品状态为返修待复测，否则不更新。结束-------

        if ($station_now[1] == 'RT') { //返修日志
            preg_match('/\D{2}(\d{1,2})/', $res_product['at_station'], $matches);
            $tmp = $station_now[2] - $matches[1];
            $st = $res_product['status'];
            if (!($st == 2 && $tmp == 0)) { //返修日志必须状态为返修 且和当前所处测试站相同
                $db->rollback();
                msg(403, '测试站有误');
                return;
            }

            if ($res_product && $res_product['status'] == 2 && $matches[1] == $station_now[2] && $result == 1) { //测试站序号等于返修站序号，结果为成功
                $updateData1 = array(
                    'status' => 3,
                );
                $db->where('order_num', $order_num)->where('sn', $sn);
                if (!$db->update($this->table3, $updateData1)) {
                    $db->rollback();
                    msg(402, $db->getLastError());
                    return;
                }
            }
            $db->commit();
            msg(200); //结束
            return;
        }

        //获取该用户不良品重测次数
        $db->where('user_id', $user_id);
        $res_user = $db->getOne($this->table4, 'retest_times');
        if (!$res_user) {
            $db->rollback();
            msg(402, $db->getLastError());
            return;
        }

        //-------FT日志：根据是否为产品首条日志分别处理(插入或更新产品表)-------
        if (!$res_product) { //测试日志 产品信息还未写入数据库
            $test_cnt_now = 1;
            //判断数据正确性
            if ($station_now[2] != 1) { //首个日志的测试站必须为FT1
                $db->rollback();
                msg(403, '测试站有误');
                return;
            }

            //状态
            $status = null;
            if ($result == 1) {
                $status = 0;
            } else {
                if ($res_user['retest_times'] == 0) {
                    $status = 2;
                } else {
                    $status = 1;
                }
            }
            //构建插入数据
            $inData2 = array(
                'order_num' => $order_num,
                'sn' => $sn,
                'at_station' => $station,
                $test_cnt => 1,
                $test_time => $test_dur,
                'status' => $status,
            );

            //插入数据库
            if (!$db->insert($this->table3, $inData2)) {
                $db->rollback();
                msg(402, $db->getLastError());
                return;
            }
        } else { //更新
            //判断数据正确性
            preg_match('/\D{2}(\d{1,2})/', $res_product['at_station'], $matches);
            $tmp = $station_now[2] - $matches[1];
            $st = $res_product['status'];
            if (!(($tmp == 0 && ($st == 1 || $st == 3)) || ($tmp == 1 && $st == 0))) { //测试站必须按顺序执行
                $db->rollback();
                msg(403, '测试站有误');
                return;
            }

            $test_cnt_now = $res_product[$test_cnt] + 1;
            //构建更新数据
            $status = null;
            //状态
            if ($result == 1) {
                $status = 0;
            } else {
                if (count($rt_list) > 0 && in_array($station_now[2], $rt_list) && ($test_cnt_now % ($res_user['retest_times'] + 1) == 0)) {
                    //有重测站
                    $status = 2;
                } else {
                    $status = 1;
                }
            }

            $updateData2 = array(
                'order_num' => $order_num,
                'sn' => $sn,
                'at_station' => $station,
                'status' => $status,
                $test_cnt => $test_cnt_now,
                $test_time => isset($res_product[$test_time]) ? $res_product[$test_time] . ',' . $test_dur : $test_dur,
            );

            //更新数据库
            $db->where('order_num', $order_num)->where('sn', $sn);
            if (!$db->update($this->table3, $updateData2)) {
                $db->rollback();
                msg(402, $db->getLastError());
                return;
            }
        }

        //-------插入测试错误代码表-------
        //      若日志测试结果为失败，insert 错误代码与测试站别

        if (isset($_POST['error_code']) && $result == '0') { //结果为失败且有错误代码才插入错误代码
            $inData3 = array(
                'order_num' => $order_num,
                'station' => $station,
                'error_code' => $error_code,
            );
            //插入数据库
            if (!$db->insert($this->table5, $inData3)) {
                $db->rollback();
                msg(402, $db->getLastError());
                return;
            }
        }

        //-------更新订单统计表-------
        //        若日志测试结果为成功，该测试站实际产出+1
        //      若日志测试结果为成功，且（当前测试站测试次数=1）则该站首次通过数量+1
        //      若日志测试结果为失败，且 (当前测试站测试次数=测试不良品重测次数+1) 则该站不良品数量+1

        //构建查询sql
        $good_cnt = 'good_cnt_' . $station_now[2];
        $reject_cnt = 'reject_cnt_' . $station_now[2];
        $fp_cnt = 'fp_cnt_' . $station_now[2];
        $sqlstr = "$good_cnt, $reject_cnt, $fp_cnt";

        //查询
        $db->setQueryOption('FOR UPDATE'); //加锁
        $db->where('order_num', $order_num);
        $res_order_info = $db->getOne($this->table6, $sqlstr);

        //构建返回值
        $data = array(
            'rt' => 'false',
        );

        //构建更新数据
        $updateData3 = array();
        if ($result == 1) { //成功
            $updateData3[$good_cnt] = $res_order_info[$good_cnt] + 1; //测试站实际产出
            if ($test_cnt_now == 1) {
                $updateData3[$fp_cnt] = $res_order_info[$fp_cnt] + 1; //测试站首次通过数量
            }
        } else { //失败
            if (count($rt_list) > 0 && in_array($station_now[2], $rt_list) && $test_cnt_now == $res_user['retest_times'] + 1) {
                $updateData3[$reject_cnt] = $res_order_info[$reject_cnt] + 1; //测试站不良品数量
                $data['rt'] = 'true'; //需要返修
            }
        }

        //更新数据库
        if ($updateData3) {
            $db->where('order_num', $order_num);
            if (!$db->update($this->table6, $updateData3)) {
                $db->rollback();
                msg(402, $db->getLastError());
                return;
            }
        }

        //-------更新订单表已完成数量-------
        //      若该测试站为最后一个测试站，且测试结果为成功，则已完成数量+1
        if ($station_now[2] == $res_orders['station_cnt'] && $result == 1) {
            $updateData3 = array(
                'complete_quantity' => $res_orders['complete_quantity'] + 1,
            );
            $db->where('user_id', $user_id)->where('order_num', $order_num);
            if (!$db->update($this->table1, $updateData3)) {
                $db->rollback();
                msg(402, $db->getLastError());
                return;
            }
        }

        //-------------------【事务结束】-------------------
        $db->commit();
        msg(200, $data);
    }
}
