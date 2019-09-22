<?php

namespace asgrad\util;

class GetUpdate
{
    private $table = "software_update";

    public function run($ROLE)
    {
        global $db;
        $user_uid = $_POST['user_uid'];
        $user_id = $_POST['user_id'];

        if ($ROLE != 1 && $user_uid != $user_id) {
            msg(400, '用户id有误');
        } else {
            $db->where('user_id', $user_id);
            if (isset($_POST['version']) && $ROLE == 1) {
                $db->where('version', $_POST['version']);
            } else {
                $db->orderBy('add_time');
            }
            dbGetOne($this->table, 'res', '', 'version, version_id, description, qiniu_file_name, file_md5, add_time');
        }
    }
}
