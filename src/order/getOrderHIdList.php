<?php
namespace asgrad\order;

class getOrderHIdList
{
    private $table1 = "orders";
    private $table2 = "hardware_id";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $order_num = $_POST['order_num'];
        $page = $_POST['page'];
        $pageLimit = $_POST['pageLimit'];

        //确认订单是该用户的
        $db->where('user_id', $user_id)->where('order_num', $order_num);
        if (!$db->getOne($this->table1, 'order_num')) {
            msg(403, '不合法的调用');
            return;
        }

        //获取总条数
        $db->where('order_num', $order_num);
        if (isset($_POST['status'])) {
            $db->where('status', $_POST['status']);
        }
        $total = $db->getValue($this->table2, "count(*)");

        //获取硬件id列表
        $db->where('order_num', $order_num);
        if (isset($_POST['status'])) {
            $db->where('status', $_POST['status']);
        }

        $db->pageLimit = $pageLimit;
        $res = $db->arraybuilder()->paginate($this->table2, $page);
        if (!$res) {
            msg(402, $db->getLastError());
            return;
        }
        //构建返回值
        $ret = buildPackedRet($res, $total);
        msg(200, $ret);
    }
}
