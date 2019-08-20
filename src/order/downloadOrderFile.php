<?php

class downloadOrderFile
{
    private $_table1 = "orders";
    private $_table2 = "order_files";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $order_num = $_POST['order_num'];
        $file_name = $_POST['file_name'];

        //判断是否允许下载
        $db->where('user_id', $user_id)->where('order_num', $order_num);
        $res = $db->getOne($this->_table1, 'status');
        if (!$res) {
            msg(403, '不合法的调用');
            return;
        }
        if ($res['status'] != 1) {
            msg(403, '不合法的调用');
            return;
        }
        $db->where('order_num', $order_num)->where('file_name', $file_name);
        if (!$db->getOne($this->_table2, 'file_name')) {
            msg(403, '不合法的调用');
            return;
        }

        //提供下载
        global $config;
        $url = './' . $config['order_files_url'] . '/' . $file_name;
        if (!fileDownload($url)) {
            msg(403, '不合法的调用');
            return;
        }
    }
}
