<?php
/*
	间隔固定时间 轮询调用，同时也可提供刷新按钮 用户手动刷新数据
	(1)整体直通率，FPY(%)=p1*p2*p3(每一个测试站的首次良率乘积)
	(2)整体Topissue，数量最多的三类问题，显示发生的测试站别、类型、数量。
	(3)订单实际产出（折线图）
*/

class getOrderStat
{
    private $table1 = "orders";
    private $table2 = "order_info";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $order_num = $_POST['order_num'];
        $last_timestamp_comp = $_POST['last_timestamp_comp'];

        //查询order表获取总产品数量和判断调用合法性

        //查询order_info表获取每个站的
    }
}
