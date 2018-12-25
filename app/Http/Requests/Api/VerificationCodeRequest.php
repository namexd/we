<?php
namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class VerificationCodeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'phone' => [
                'required',
                'regex:/^(1[0-9])\d{9}$/',
                'unique:users'
            ]
        ];
    }
}