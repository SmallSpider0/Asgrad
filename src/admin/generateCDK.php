<?php

class generateCDK
{
    private $table = "cd_key";

    public function run($ROLE)
    {
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $grant_time = $_POST['grant_time'];

        global $db;
        //验证是否存在
        $db->where('phone', $phone)->where('email', $email);
        $res = $db->getOne($this->table, 'id');
        if ($res) {
            msg(400, '手机号或邮箱重复');
            return;
        }

        $inData = array(
            "phone" => $phone,
            "email" => $email,
            "grant_time" => $grant_time,
        );
        db_insert($this->table, $inData);
    }
}
