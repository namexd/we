<?php

namespace App\Http\Requests\Api;


use Dingo\Api\Http\FormRequest;

class UserPhoneRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        $rules = [
            'verification_key' => 'required|string',
            'verification_code' => 'required|string|min:4|max:4',
            'realname' => 'required|string',
        ];
        return $rules;
    }
    public function attributes()
    {
        return [
            'verification_key' => '短信验证码 key',
            'verification_code' => '短信验证码',
            'realname' => '姓名',
        ];
    }
}
