<?php

class getCDKList
{
    private $table = "cd_key";

    public function run($ROLE)
    {
        global $db;
        $page = $_POST['page'];
        $pageLimit = $_POST['pageLimit'];

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
        $res = $db->arraybuilder()->paginate($this->table, $page);
        if (!$res) {
            msg(402, $db->getLastError());
            return;
        }
        msg(200, $res);
    }
}
