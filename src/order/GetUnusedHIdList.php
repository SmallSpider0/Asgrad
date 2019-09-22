<?php

namespace asgrad\order;

class GetUnusedHIdList
{
    private $table1 = "orders";
    private $table2 = "hardware_id";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $order_num = $_POST['order_num'];

        //确认订单是该用户的
        $db->where('user_id', $user_id)->where('order_num', $order_num);
        if (!$db->getOne($this->table1, 'order_num')) {
            msg(403, '不合法的调用');
            return;
        }

        //获取未使用id列表
        $db->where('status', 0)->where('order_num', $order_num)->groupBy('type');
        $cnt_map = [];
        foreach ($db->get($this->table2, null, 'type, count(*) as cnt') as $value) {
            $cnt_map[$value['type']] = $value['cnt'];
        }

        $db->where('status', 0)->where('order_num', $order_num);
        $db->orderBy('type');
        $res1 = $db->get($this->table2, null, 'type,data');
        $ret1 = '';
        $flag1 = false;
        $type = '';
        foreach ($res1 as $value) {
            if ($value['type'] != $type) {
                $type = $value['type'];
                if (!$flag1) {
                    $flag1 = true;
                } else {
                    $ret1 .= PHP_EOL . '-------';
                    $ret1 .= PHP_EOL;
                }
                $ret1 .= $type . PHP_EOL . $cnt_map[$type] . PHP_EOL . $value['data'];
            } else {
                $ret1 .= PHP_EOL . $value['data'];
            }
        }



        //获取已下发但未反馈id列表
        $db->where('status', 1)->where('order_num', $order_num);
        $db->orderBy('type');
        $res2 = $db->get($this->table2, null, 'type,data');
        $ret2 = '';
        $flag2 = false;
        $type = '';
        foreach ($res2 as $value) {
            if ($value['type'] != $type) {
                $type = $value['type'];
                if (!$flag2) {
                    $flag2 = true;
                } else {
                    $ret2 .= PHP_EOL . '-------';
                    $ret2 .= PHP_EOL;
                }
                $ret2 .= $type . PHP_EOL . $cnt_map[$type] . PHP_EOL . $value['data'];
            } else {
                $ret2 .= PHP_EOL . $value['data'];
            }
        }

        $ret = [
            'status0' => $ret1,
            'status1' => $ret2
        ];
        msg(200, $ret);
    }
}
