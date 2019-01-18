<?php

namespace App\Http\Requests\Api;


class UserRequest extends Request
{
    public function rules()
    {

        switch ($this->method()) {
            case 'PUT':
                $rule =[
                    'phone' => [
                        'required',
                        'regex:/^(1[0-9])\d{9}$/'
                    ]
                ];
                if (auth('api')->user()) {
                    $rules['phone'][]  = 'unique:users';
                }
                return $rule;
                break;
        }
    }

    public function attributes()
    {
        return [
            'phone' => '手机号',
        ];
    }

    public function messages()
    {
        return [
            'required.required' => '密码不能为空',
        ];
    }
}
