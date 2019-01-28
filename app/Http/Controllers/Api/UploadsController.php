<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UploadRequest;
use App\Models\Upload;
use App\Transformers\UploadTransformer;
use OSS\Core\OssException;
use Storage;

class UploadsController extends Controller
{

    public function show($uniqid)
    {
        $upload = Upload::where('uniqid', $uniqid)->first();
        return $this->response->item($upload, new UploadTransformer());
    }

    public function store(UploadRequest $request, $app = 'ccrp', $action = 'images', $unit_id = 0)
    {
        $file = $request->file('file');
        if ($file->isValid()) {
            //获取文件的原文件名 包括扩展名
            $original_name = $file->getClientOriginalName();
            //允许后缀
            $fileTypes = Upload::ALLOW_EXT;
            //获取文件类型后缀
            $extension = $file->getClientOriginalExtension();
            //是否是要求的文件
            $isInFileType = in_array($extension, $fileTypes);
            if (!$isInFileType) {
                return $this->response->error('文件格式不合法', 430);

            }
            //获取文件的类型
            $type = $file->getClientMimeType();
            //获取文件的绝对路径，但是获取到的在本地不能打开
            $folder = config('filesystems.disks.oss.folder') . $app . DIRECTORY_SEPARATOR . $action . DIRECTORY_SEPARATOR . $unit_id;
            $path = $file->getRealPath();
            //要保存的文件名 时间+扩展名
            $uniqid = uniqid($app, true);
            $filename = $folder . DIRECTORY_SEPARATOR . date('Y-m-d') . DIRECTORY_SEPARATOR . $uniqid . '.' . $extension;
            //保存文件          配置文件存放文件的名字  ，文件名，路径
            try {
                $bool = Storage::disk('oss')->put($filename, file_get_contents($path));
                if ($bool) {
                    $url = Storage::disk('oss')->url($filename);
                    $size = Storage::disk('oss')->size($filename);
                    $upload = new Upload();
                    $upload->uniqid = $uniqid;
                    $upload->user_id = $this->user()->id;
                    $upload->app = $app;
                    $upload->action = $action;
                    $upload->unit_id = $unit_id;
                    $upload->filename = substr($original_name, 0, 20);
                    $upload->url = $url;
                    $upload->ext = $extension;
                    $upload->type = $type;
                    $upload->size = $size;
                    $upload->save();
                    return $this->response->item($upload, new UploadTransformer());
                }
            } catch (OssException $exception) {
                return $this->response->error($exception->getMessage(), 430);
            }
        }
        return $this->response->noContent();
    }

}
