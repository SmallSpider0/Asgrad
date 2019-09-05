<?php

namespace asgrad\order;

class DownloadOrderFile
{
    private $table1 = "orders";
    private $table2 = "order_files";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $order_num = $_POST['order_num'];
        $file_name = $_POST['file_name'];

        //判断是否允许下载
        $db->where('user_id', $user_id)->where('order_num', $order_num);
        $res = $db->getOne($this->table1, 'status');
        if (!$res) {
            msg(403, '不合法的调用');
            return;
        }
        if ($res['status'] != 1) {
            msg(403, '不合法的调用');
            return;
        }
        $db->where('order_num', $order_num)->where('file_name', $file_name);
        $file_res = $db->getOne($this->table2, 'file_ori_name');
        if (!$file_res) {
            msg(403, '不合法的调用');
            return;
        }

        //提供下载
        global $config;
        $url = './' . $config['order_files_url'] . '/' . $file_name;
        if (!fileDownload($url, $file_res['file_ori_name'])) {
            msg(403, '不合法的调用');
            return;
        }
    }
}
