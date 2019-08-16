<?php

class submitOrder
{
    private $table1 = "user_info";
    private $table2 = "orders";
    private $table3 = "order_info";
    private $table4 = "order_files";
    private $table5 = "hardware_id";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_id'];
        $made_in = $_POST['made_in'];
        $product_name = $_POST['product_name'];
        $product_model = $_POST['product_model'];
        $quantity = $_POST['quantity'];
        $station_cnt = $_POST['station_cnt'];
        $plan_online_time = $_POST['plan_online_time'];
        $working_procedure = $_POST['working_procedure'];
        $test_seq = $_POST['test_seq'];
        $hid_list = $_POST['hid_list'];

        //--------开始事务------------
        $db->startTransaction();

        //生成订单号
        $order_num = date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);

        //插入order表
        $inData = array(
            'order_num' => $order_num,
            'user_id' => $user_id,
            'made_in' => $made_in,
            'product_name' => $product_name,
            'product_model' => $product_model,
            'quantity' => $quantity,
            'station_cnt' => $station_cnt,
            'plan_online_time' => $plan_online_time,
            'working_procedure' => $working_procedure,
            'test_seq' => $test_seq,
        );
        if (!$db->insert($this->table2, $inData)) {
            $db->rollback();
            msg(402, $db->getLastError());
            return;
        }

        //插入order_info表
        $inData = array(
            'order_num' => $order_num,
        );
        if (!$db->insert($this->table3, $inData)) {
            $db->rollback();
            msg(402, $db->getLastError());
            return;
        }

        //更新user_info表已生成订单数
        $db->rawQuery('update ' . $this->table1 . ' set order_cnt = order_cnt + 1 where `user_id` = ?', [$user_id]);

        //硬件id插入hardware_id表
        $processed_hid = $this->process_hid($hid_list);
        if ($processed_hid) {
            $inData = array();
            foreach ($processed_hid as $tmp) {
                foreach ($tmp['data'] as $hid) {
                    array_push($inData, array($order_num, $tmp['hid_type'], $hid));
                }
            }
        } else {
            $db->rollback();
            msg(404, '硬件id格式有误');
            return;
        }

        $keys = array("order_num", "type", "data");
        if (!$db->insertMulti($this->table5, $inData, $keys)) {
            $db->rollback();
            msg(402, $db->getLastError());
            return;
        }

        //测试相关文件存本地磁盘，order_files表
        //保存文件
        $upload = new \Dj\Upload();
        global $config;
        $filelist = $upload->save('./' . $config['order_files_url']);
        if (!is_array($filelist)) { //失败
            $error_msg = [-1 => '上传失败', -2 => '文件存储路径不合法', -3 => '上传非法格式文件', -4 => '文件大小不合符规定'];
            $db->rollback();
            msg(403, '测试相关文件上传错误' . $error_msg[$filelist]);
            return;
        }
        //文件信息存入数据库
        $inData = array();
        if (isset($filelist['name'])) {
            array_push($inData, array($filelist['md5'], $filelist['savename'], $order_num));
        } else {
            foreach ($filelist as $file) {
                array_push($inData, array($file['md5'], $file['savename'], $order_num));
            }
        }
        $keys = array("file_md5", "file_name", "order_num");
        if (!$db->insertMulti($this->table4, $inData, $keys)) {
            $db->rollback();
            msg(402, $db->getLastError());
            return;
        }

        //--------提交事务-------------
        $db->commit();
        msg(200);
    }

    //输入字符串形式的hid列表
    //返回：解析好的数组 或 格式有误无法解析false
    private function process_hid($hid_list)
    {
        global $config;
        $ret = array();
        $lists = explode("\n-------\n", $hid_list);
        foreach ($lists as $list) {
            $tmp = array();
            $hids = explode("\n", $list);
            //类型
            if (isset($hids[0]) && in_array(strtolower($hids[0]), $config['hid_types'])) {
                $tmp['hid_type'] = strtolower($hids[0]);
            } else {
                return false;
            }
            //数量
            if (!(isset($hids[1]) && is_numeric($hids[1]) && (count($hids) - 2 == $hids[1]))) {
                return false;
            }
            $tmp['data'] = array_slice($hids, 2);
            array_push($ret, $tmp);
        }
        return $ret;
    }
}
