<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class WeAuthorizationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules['openid'] = 'required|string';
        return $rules;
    }
}