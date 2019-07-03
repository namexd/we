<?php

namespace App\Models\Bpms;

use Illuminate\Support\Facades\DB;

class User extends BpmsAuthAPI
{


    const STATUSES = ['0' => '禁用', '1' => '正常'];

    //验证用户名是否正确
    public function checkUsername($username)
    {
        $check = $this->action('GET','checkUsername',['username'=>$username]);
        if($res = $this->getResponse($check))
        {
            if($res) {
                return true;
            }
        }
        return false;
    }

    //验证密码是否正确
    public function checkPassword($username, $password)
    {
        $check = $this->action('GET','checkPassword',['username'=>$username,'password'=>$password]);

        if($res = $this->getResponse($check))
        {
            if($res) {
                $obj = new \StdClass();
                $obj->id = $res['id'];
                $obj->unitid = $res['ownerid'];
                $obj->username =$username;
                return $obj;
            }
        }
        return false;
    }

    //通过手机号验证是否实名认证
    public function checkPhone($username, $phone)
    {
        return true;
    }

    public function getByUsername($username)
    {
        return false;
    }
}
