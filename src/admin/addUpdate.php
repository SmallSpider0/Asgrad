<?php

class addUpdate
{
    private $_table1 = "user_info";
    private $_table2 = "software_update";

    public function run($ROLE)
    {
        global $db;
        $user_id = $_POST['user_uid'];
        $description = $_POST['description'];
        $version_id = $_POST['version_id'];

        //-------------------【事务开始】-------------------
        $db->startTransaction();

        //插入一条软件更新

        $db->setQueryOption('FOR UPDATE')->where('user_id', $user_id); //加锁
        $res = $db->getOne($this->_table1, 'software_version, version_id');

        //判断版本号是否正确
        if (!$this->chech_version($version_id, $res['version_id'])) {
            $db->rollback();
            msg(400, '版本号有误');
            return;
        };
        $upRet = uploadFile('file');
        $inData = array(
            "user_id" => $user_id,
            "version" => $res['software_version'] + 1,
            "version_id" => $version_id,
            "description" => $description,
            "qiniu_file_name" => $upRet['key'],
            "file_md5" => $upRet['md5'],
        );
        if (!$db->insert($this->_table2, $inData)) {
            $db->rollback();
            msg(402, $db->getLastError());
            return;
        }

        //更新用户最新版本号
        $db->where('user_id', $user_id);
        $updateData = array(
            'software_version' => $res['software_version'] + 1,
            "version_id" => $version_id,
        );
        if (!$db->update($this->_table1, $updateData)) {
            $db->rollback();
            msg(402, $db->getLastError());
            return;
        }

        //-------------------【事务提交】-------------------
        $db->commit();
        msg(200);
    }

    private function chech_version($v, $old_v)
    {
        if (preg_match('/(\d+)\.(\d+)\.(\d+)/', $v, $matches) && preg_match('/(\d+)\.(\d+)\.(\d+)/', $old_v, $matches_old)) {
            if (!isset($matches[3]) || !isset($matches_old[3])) {
                return false;
            }
            if ($matches[1] > $matches_old[1]) {
                return true;
            }

            if ($matches[1] < $matches_old[1]) {
                return false;
            }

            if ($matches[2] > $matches_old[2]) {
                return true;
            }

            if ($matches[2] < $matches_old[2]) {
                return false;
            }

            if ($matches[3] > $matches_old[3]) {
                return true;
            }

            if ($matches[3] <= $matches_old[3]) {
                return false;
            }

        }
        return false;
    }
}
