<?php
/*
间隔固定时间 轮询调用，同时也可提供刷新按钮 用户手动刷新数据
(1)整体直通率，FPY(%)=p1*p2*p3(每一个测试站的首次良率乘积)
(2)整体Topissue，数量最多的三类问题，显示发生的测试站别、类型、数量。
(3)订单实际产出（折线图）
 */

class getOrderStat
{
    private $_table1 = "orders";
    private $_table2 = "order_info";
    private $_table3 = "stat_complete_quantity";
    private $_table4 = "error_code";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $order_num = $_POST['order_num'];
        $ret = array();

        //-------查询order表获取测试站数量，产品数量和判断调用合法性-------

        $db->where('user_id', $user_id)->where('order_num', $order_num);
        $res_orders = $db->getOne($this->_table1, 'station_cnt, quantity, status');
        if (!$res_orders) {
            msg(403, '不合法的调用');
            return;
        }

        //-------查询order_info表获取每个站的FPY-------

        //构建查询sql
        $tmp = array();
        for ($i = 1; $i <= $res_orders['station_cnt']; $i++) {
            array_push($tmp, "fp_cnt_$i");
        }
        $sqlStr = join(", ", $tmp);
        $db->where('order_num', $order_num);
        $res_order_info = $db->getOne($this->_table2, $sqlStr . ', top_err_order, top_err_order_time, top_err_order_end');
        if (!$res_order_info) {
            msg(402, $db->getLastError());
            return;
        }

        //计算整体直通率
        $ret['fpy'] = 1;
        foreach ($res_order_info as $key => $value) {
            if (!in_array($key, ['top_err_order', 'top_err_order_time', 'top_err_order_end'])) {
                $ret['fpy'] = $ret['fpy'] * $value / $res_orders['quantity'];
            }
        }

        //-------获取整体错误topissue-------

        //判断是否需要重新计算：
        //首次更新、订单执行中且与上次更新相差一定时间、订单执行完成单还未完成最后更新
        $t = $res_order_info['top_err_order_time'];
        $s = $res_orders['status'] == 1;
        $e = $res_order_info['top_err_order_end'];
        if (!$t || ($s == 1 && time() - strtotime($t) >= 60) || ($s > 1 && $e == 0)) { //重新计算
            $db->where('order_num', $order_num);
            $db->groupBy('station, error_code');
            $db->orderBy('count(*)');
            $ret['top_err_order'] = json_encode(build_packed_ret($db->get($this->_table4, 3, 'station, error_code, count(*)'), JSON_UNESCAPED_UNICODE));
            //写入
            $updateData = array(
                'top_err_order' => $ret['top_err_order'],
                'top_err_order_time' => date('Y-m-d H:i:s'),
            );
            if ($s > 1 && $e == 0) {
                $updateData['top_err_order_end'] = 1;
            }
            $db->where('order_num', $order_num);
            if (!$db->update($this->_table2, $updateData)) {
                msg(402, $db->getLastError());
                return;
            }
        } else { //直接返回
            $ret['top_err_order'] = $res_order_info['top_err_order'];
        }

        //-------历史数据表中获取订单实际产出折线图数据-------
        $db->where('order_num', $order_num);
        if (isset($_POST['last_timestamp_comp'])) {
            $db->where('add_time', $_POST['last_timestamp_comp'], '>');
        }

        $res = $db->get($this->_table3, null, "add_time, complete_quantity_" . $res_orders['station_cnt'] . " as complete_quantity");
        if (isset($res)) {
            $ret['stat_complete_quantity'] = build_packed_ret($res);
        } else {
            $ret['stat_complete_quantity'] = '';
        }

        msg(200, $ret);
    }
}
