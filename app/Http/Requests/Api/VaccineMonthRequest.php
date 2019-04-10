<?php

namespace App\Http\Requests\Api;

class VaccineMonthRequest extends Request
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
            'year'=>'required|date_format:Y',
            'code'=>'required'
        ];
    }
    public function attributes()
    {
        return [
            'year'=>'年份',
            'code'=>'产品编码'
        ];
    }
}
