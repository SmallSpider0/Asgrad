<?php

namespace asgrad\log;

/*
间隔固定时间 轮询调用，同时也可提供刷新按钮 用户手动刷新数据
(1)各站计划产量 【与订单中的数量字段相同】
(2)各站实际产出(达成率)(良品) 【通过该测试站测试的产品数量】（折线图）
(3)各站不良品数量(不良率) 【首次超过重测次数进入维修的产品数量】（折线图）
(4)各站测试一次通过率FPY(first pass field)(%)(首次良率) 【首次过站直接通过的产品数量】
(5)各站最多的不良问题类型Topissue(根据上传日志中的ErrorCode字段统计)，显示类型和数量
 */

class GetTestStationStat
{
    private $table1 = "orders";
    private $table2 = "order_info";
    private $table3 = "stat_complete_quantity";
    private $table4 = "error_code";
    private $table5 = "stat_reject_cnt";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $order_num = $_POST['order_num'];
        $ret = array();

        //-------查询order表获取测试站数量，产品数量和判断调用合法性-------

        $db->where('user_id', $user_id)->where('order_num', $order_num);
        $res_orders = $db->getOne($this->table1, 'station_cnt, quantity, status');
        if (!$res_orders) {
            msg(403, '不合法的调用');
            return;
        }
        $ret['quantity'] = $res_orders['quantity'];

        //-------查询各站实际产出-------
        $db->where('order_num', $order_num);
        if (isset($_POST['last_timestamp_comp'])) {
            $db->where('add_time', $_POST['last_timestamp_comp'], '>');
        }

        $tmp = array();
        for ($i = 1; $i <= $res_orders['station_cnt']; $i++) {
            array_push($tmp, "complete_quantity_$i");
        }
        $sqlStr = join(", ", $tmp);
        $res = $db->get($this->table3, null, "add_time, $sqlStr");
        if ($res) {
            $ret['stat_complete_quantity'] = buildPackedRet($res);
        } else {
            $ret['stat_complete_quantity'] = '';
        }

        //-------查询各站不良品数量-------
        $db->where('order_num', $order_num);
        if (isset($_POST['last_timestamp_reject'])) {
            $db->where('add_time', $_POST['last_timestamp_reject'], '>');
        }

        $tmp = array();
        for ($i = 1; $i <= $res_orders['station_cnt']; $i++) {
            array_push($tmp, "reject_cnt_$i");
        }
        $sqlStr = join(", ", $tmp);
        $res = $db->get($this->table5, null, "add_time, $sqlStr");
        if (isset($res)) {
            $ret['stat_reject_cnt'] = buildPackedRet($res);
        } else {
            $ret['stat_reject_cnt'] = '';
        }

        //-------查询order_info表获取每个站的FPY-------

        //构建查询sql
        $tmp = array();
        for ($i = 1; $i <= $res_orders['station_cnt']; $i++) {
            array_push($tmp, "fp_cnt_$i");
        }
        $sqlStr = join(", ", $tmp);
        $db->where('order_num', $order_num);
        $res_order_info = $db->getOne($this->table2, $sqlStr . ', top_err_station, top_err_station_time, top_err_station_end');
        if (!$res_order_info) {
            msg(402, $db->getLastError());
            return;
        }

        //各站直通率
        $ret['fpy_station'] = array();
        foreach ($res_order_info as $key => $value) {
            if (!in_array($key, ['top_err_station', 'top_err_station_time', 'top_err_station_end'])) {
                $ret['fpy_station'][$key] = $value / $res_orders['quantity'];
            }
        }

        //-------获取各站错误topissue-------

        //判断是否需要重新计算：
        //首次更新、订单执行中且与上次更新相差一定时间、订单执行完成单还未完成最后更新
        $t = $res_order_info['top_err_station_time'];
        $s = $res_orders['status'] == 1;
        $e = $res_order_info['top_err_station_end'];
        if (!$t || ($s == 1 && time() - strtotime($t) >= 60) || ($s > 1 && $e == 0)) { //重新计算
            //计算
            $ret['top_err_station'] = array();
            for ($i = 1; $i <= $res_orders['station_cnt']; $i++) {
                $db->where('order_num', $order_num)->where('station', "FT$i");
                $db->groupBy('error_code');
                $db->orderBy('count(*)');
                $ret['top_err_station']["FT$i"] = json_encode(buildPackedRet($db->get($this->table4, 3, 'error_code, count(*)')), JSON_UNESCAPED_UNICODE);
            }

            //写入
            $updateData = array(
                'top_err_station' => json_encode($ret['top_err_station'], JSON_UNESCAPED_UNICODE),
                'top_err_station_time' => date('Y-m-d H:i:s'),
            );
            if ($s > 1 && $e == 0) {
                $updateData['top_err_station_end'] = 1;
            }
            $db->where('order_num', $order_num);
            if (!$db->update($this->table2, $updateData)) {
                msg(402, $db->getLastError());
                return;
            }
        } else { //直接返回
            $ret['top_err_station'] = $res_order_info['top_err_station'];
        }

        msg(200, $ret);
    }
}
