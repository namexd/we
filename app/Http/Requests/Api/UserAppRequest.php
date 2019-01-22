<?php

namespace App\Http\Requests\Api;


class UserAppRequest extends Request
{
    public function rules()
    {
        switch($this->method()) {
            case 'PUT':
                return [
                    'username' => 'required|between:3,25|regex:/^[A-Za-z0-9\-\_]+$/',
                    'password' => 'required|string|min:6',
                    'app_id' => 'required',
                ];
                break;
            case 'DELETE':
                return [
                    'app_id' => 'required',
                ];
                break;
        }
    }
    public function attributes()
    {
        return [
            'username' => '系统登录名',
            'password' => '系统登录密码',
            'app_id' => '管理系统',
        ];
    }

    public function messages()
    {
        return [
            'name.regex' => '用户名格式错误',
            'name.between' => '用户名长度错误',
            'name.required' => '原系统登录名',
            'password.required' => '密码不能为空',
            'app_id.required' => '请选择管理系统',
        ];
    }
}
