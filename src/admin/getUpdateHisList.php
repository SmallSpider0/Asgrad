<?php

class getUpdateHisList
{
    private $table = "software_update";

    public function run($ROLE)
    {
        global $db;
        $page = $_POST['page'];
        $pageLimit = $_POST['pageLimit'];

        if (isset($_POST['user_id'])) {
            $db->where('user_id', $_POST['user_id']);
        }
        $db->orderBy('add_time');

        $db->pageLimit = $pageLimit;
        $res = $db->arraybuilder()->paginate($this->table, $page);
        if (!$res) {
            msg(402, $db->getLastError());
            return;
        }
        msg(200, $res);
    }
}
