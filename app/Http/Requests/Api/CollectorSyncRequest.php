<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CollectorSyncRequest extends FormRequest
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
            'sn'=>'required',
            'data_id'=>'required'
        ];
    }

    public function attributes()
    {
        return [
//            'collectors' => 'collectors 数组格式,[{"sn":"60201326","data_id":"1936109425"}]，目前可支持2个探头查询，',
        ];
    }
}
