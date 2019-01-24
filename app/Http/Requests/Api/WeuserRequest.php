<?php

namespace App\Http\Requests\Api;


class WeuserRequest extends Request
{
    public function rules()
    {
        switch ($this->method()) {
            case 'PUT':
                $rule =[
                    'userInfo' => [
                        'required'
                    ]
                ];
                return $rule;
                break;
        }
    }

    public function attributes()
    {
        return [
            'userInfo' => '微信信息',
        ];
    }

    public function messages()
    {
        return [
            'userInfo.required' => '微信信息不能为空',
        ];
    }
}
