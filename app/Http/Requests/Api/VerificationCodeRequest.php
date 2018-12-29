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
}