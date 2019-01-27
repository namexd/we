<?php

namespace App\Http\Requests\Api\Ccrp;

class StatManualRecordRequest extends Request
{
    public function rules()
    {
        return [
            'records'=>'required',
            'sign'=>'required|file'
        ];
    }

    public function attributes()
    {
        return [
            'records' => '温度数据',
            'sign' => '手写签名签名',
        ];
    }
}
