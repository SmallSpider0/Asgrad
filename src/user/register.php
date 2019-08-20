<?php

class register
{
    private $_table1 = "user_login_web";
    private $_table2 = "user_login_pc";
    private $_table3 = "user_info";
    private $_table4 = "cd_key";

    public function run($ROLE)
    {
        global $db;
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $company_name = $_POST['company_name'];
        $passwd_web = $_POST['passwd_web'];
        $passwd_pc = $_POST['passwd_pc'];

        //开始事务
        $db->startTransaction();

        //验证是否激活
        $db->where('phone', $phone)->where('email', $email);
        $res = $db->getOne($this->_table4, 'grant_time, status');
        if (!$res) {
            msg(403, '你的账号还未激活');
            return;
        }
        if ($res['status'] != 0) {
            msg(404, '激活码已使用或已失效');
            return;
        }
        $db->where('phone', $phone)->where('email', $email);
        $updateData = array(
            'status' => 1,
        );
        if (!$db->update($this->_table4, $updateData)) {
            $db->rollback();
            msg(402, $db->getLastError());
            return;
        }
        $grant_time_out = time() + $res['grant_time'] * 86400;

        //添加用户
        $db->where('phone', $phone)->orWhere('email', $email);
        $res = $db->getOne($this->_table1, 'COUNT(*)');
        if ((int) $res['COUNT(*)'] > 0) {
            msg(401, '手机号或邮箱已被使用');
            return;
        } else {

            //生成web端加密登录信息
            $PSW_web = encryptPsw($passwd_web); //密码加密
            $inData = array(
                "grant_time_out" => $grant_time_out,
                "phone" => $phone,
                "email" => $email,
                "salt" => $PSW_web['salt'],
                "passwd" => $PSW_web['passwd'],
            );
            $id_web = $db->insert($this->_table1, $inData);
            if (!$id_web) {
                $db->rollback();
                msg(402, $db->getLastError());
                return;
            }

            //生成pc端加密登录信息
            $PSW_pc = encryptPsw($passwd_pc); //密码加密
            $inData = array(
                "id" => $id_web,
                "salt" => $PSW_pc['salt'],
                "passwd" => $PSW_pc['passwd'],
            );
            if (!$db->insert($this->_table2, $inData)) {
                $db->rollback();
                msg(402, $db->getLastError());
                return;
            }
        }

        //插入用户信息表
        $inData = array(
            'user_id' => $id_web,
            'company_name' => $company_name,
        );
        if (!$db->insert($this->_table3, $inData)) {
            $db->rollback();
            msg(402, $db->getLastError());
            return;
        }
        //提交事务
        $db->commit();
        msg(200);
    }
}
