<?php

class getUpdate
{
    private $table = "software_update";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_uid'];

        $db->where('user_id', $user_id);
        if (isset($_POST['version']) && $ROLE == 1) {
            $db->where('version', $_POST['version']);
        } else {
            $db->orderBy('add_time');
        }

        db_getone($this->table, 'res', '', 'version, description, qiniu_file_name, file_md5, add_time');
    }
}
