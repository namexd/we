<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class WarningEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'handler'=>'required|string|max:30',
            'handler_note'=>'required|string|max:100'
        ];
    }

    public function attributes()
    {
        return [
            'handler' => '处理人',
            'handler_note' => '处理备注',
        ];
    }
}