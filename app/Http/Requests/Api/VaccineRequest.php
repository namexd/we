<?php

namespace App\Http\Requests\Api;


class VaccineRequest extends Request
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
            'vaccine_id'=>'required'
        ];
    }
    public function attributes()
    {
        return [
            'vaccine_id'=>'疫苗种类'
        ];
    }
}
