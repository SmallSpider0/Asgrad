<?php
namespace asgrad\order;

class GetOrderInfo
{
    private $table1 = "orders";
    private $table2 = "order_files";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $order_num = $_POST['order_num'];

        $db->where('user_id', $user_id)->where('order_num', $order_num);
        $ret = $db->getOne($this->table1, 'working_procedure');
        $db->where('order_num', $order_num);
        $files = $db->get($this->table2, null, 'file_name, file_ori_name, file_md5');
        $ret['files'] = $files;
        msg(200, $ret);
    }
}
