<?php

namespace App\Http\Requests\Api;

class UploadRequest extends Request
{
    public function rules()
    {
        return [
            'file' => 'required|file',
            'app' => 'required',
            'action' => 'required',
            'unit_id' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'file' => '文件',
            'app' => '应用系统',
            'action' => '操作方法',
            'unit_id' => '单位id',
        ];
    }

    public function messages()
    {
        return [
            'file.required' => '文件不能为空',
            'file.file' => '需要上传文件哦',
            'app.required' => '应用系统不能为空，如：ccrp',
            'action.required' => '操作方法不能为空，如：sign',
            'unit_id.required' => '单位id不能为空',
        ];
    }
}
