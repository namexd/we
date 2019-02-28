<?php
namespace App\Http\Requests\Api;

use Dingo\Api\Auth\Auth;
use Dingo\Api\Http\FormRequest;

class VerificationCodeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
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
    }

    public function messages()
    {
        return [
            'phone.required'=> '手机号码未录入。',
            'phone.regex'=> '手机号码录入不正确。',
        ];
    }
}