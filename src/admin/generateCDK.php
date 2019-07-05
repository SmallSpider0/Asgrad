<?php

class generateCDK
{
    private $table = "cd_key";

    public function run($ROLE)
    {
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $grant_time = $_POST['grant_time'];

        $inData = array(
            "phone" => $phone,
            "email" => $email,
            "grant_time" => $grant_time,
        );
        db_insert($this->table, $inData);
    }
}
