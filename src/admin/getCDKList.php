<?php
namespace asgrad\admin;

class getCDKList
{
    private $_table = "cd_key";

    public function run($ROLE)
    {
        global $db;
        $page = $_POST['page'];
        $pageLimit = $_POST['pageLimit'];

        //获取总条数
        if (isset($_POST['phone'])) {
            $db->where('phone', $_POST['phone']);
        }
        if (isset($_POST['email'])) {
            $db->where('email', $_POST['email']);
        }
        if (isset($_POST['status'])) {
            $db->where('status', $_POST['status']);
        }
        $total = $db->getValue($this->_table, "count(*)");

        //查询
        if (isset($_POST['phone'])) {
            $db->where('phone', $_POST['phone']);
        }
        if (isset($_POST['email'])) {
            $db->where('email', $_POST['email']);
        }
        if (isset($_POST['status'])) {
            $db->where('status', $_POST['status']);
        }

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
