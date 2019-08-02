<?php

class addUpdate
{
    private $table1 = "user_info";
    private $table2 = "software_update";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_uid'];
        $description = $_POST['description'];

        //-------------------【事务开始】-------------------
        $db->startTransaction();

        //插入一条软件更新

        $db->setQueryOption('FOR UPDATE')->where('user_id', $user_id); //加锁
        $res = $db->getOne($this->table1, 'software_version');
        $upRet = uploadFile('file');
        $inData = array(
            "user_id" => $user_id,
            "version" => $res['software_version'] + 1,
            "description" => $description,
            "qiniu_file_name" => $upRet['key'],
            "file_md5" => $upRet['md5'],
        );
        if (!$db->insert($this->table2, $inData)) {
            $db->rollback();
            msg(402, $db->getLastError());
            return;
        }

        //更新用户最新版本号
        $db->where('user_id', $user_id);
        $updateData = array(
            'software_version' => $res['software_version'] + 1
        );
        if (!$db->update($this->table1, $updateData)) {
            $db->rollback();
            msg(402, $db->getLastError());
            return;
        }

        //-------------------【事务提交】-------------------
        $db->commit();
        msg(200);
    }
}
