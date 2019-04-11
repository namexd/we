<?php

namespace App\Http\Requests\Api;

class VaccineDetailRequest extends Request
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
            'year'=>'required',
            'month'=>'required',
            'code'=>'required'
        ];
    }
    public function attributes()
    {
        return [
            'year'=>'年份',
            'month'=>'月份',
            'code'=>'产品编码'
        ];
    }
}
