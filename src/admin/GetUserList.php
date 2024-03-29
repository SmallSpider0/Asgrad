<?php
namespace asgrad\admin;

class GetUserList
{
    private $table = "user_info_all";

    public function run($ROLE)
    {
        global $db;
        $page = $_POST['page'];
        $pageLimit = $_POST['pageLimit'];

        //获取总条数
        if (isset($_POST['company_name'])) {
            $db->where('company_name', $_POST['company_name']);
        }
        $total = $db->getValue($this->table, "count(*)");

        //查询
        if (isset($_POST['company_name'])) {
            $db->where('company_name', $_POST['company_name']);
        }
        $db->orderBy('register_time');

        $db->pageLimit = $pageLimit;
        $res = $db->arraybuilder()->paginate($this->table, $page, 'user_id, company_name, register_time, grant_time_out, order_cnt, software_version');
        if (!$res) {
            msg(402, $db->getLastError());
            return;
        }
        //构建返回值
        $ret = buildPackedRet($res, $total);
        msg(200, $ret);
    }
}
