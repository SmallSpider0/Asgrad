<?php

class getUpdateHisList
{
    private $_table = "software_update";

    public function run($ROLE)
    {
        global $db;
        $page = $_POST['page'];
        $pageLimit = $_POST['pageLimit'];

        //获取总条数
        if (isset($_POST['user_uid'])) {
            $db->where('user_id', $_POST['user_uid']);
        }
        $total = $db->getValue($this->_table, "count(*)");

        //查询
        if (isset($_POST['user_uid'])) {
            $db->where('user_id', $_POST['user_uid']);
        }
        $db->orderBy('add_time');

        $db->pageLimit = $pageLimit;
        $res = $db->arraybuilder()->paginate($this->_table, $page);
        if (!$res) {
            msg(402, $db->getLastError());
            return;
        }
        //构建返回值
        $ret = build_packed_ret($res, $total);
        msg(200, $ret);
    }
}
