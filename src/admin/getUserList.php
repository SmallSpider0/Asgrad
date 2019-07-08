<?php

class getUserList
{
    private $table = "user_info";

    public function run($ROLE)
    {
        global $db;
        $page = $_POST['page'];
        $pageLimit = $_POST['pageLimit'];

        if (isset($_POST['company_name'])) {
            $db->where('company_name', $_POST['company_name']);
        }
        $db->orderBy('register_time');

        $db->pageLimit = $pageLimit;
        $res = $db->arraybuilder()->paginate($this->table, $page,'user_id, company_name, register_time, order_cnt, software_version');
        if (!$res) {
            msg(402, $db->getLastError());
            return;
        }
        msg(200, $res);
    }
}
