<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use OSS\Core\OssException;
use Storage;

class Upload extends Model
{
    protected $fillable = ['uniqid', 'user_id', 'app', 'action', 'unit_id', 'filename', 'url', 'ext', 'type', 'note'];

    public function upload($file, $user_id, $app = 'ccrp', $action = 'images', $unit_id = 0)
    {
        //获取文件类型后缀
        $extension = $file->getClientOriginalExtension();
        //获取文件的原文件名 包括扩展名
        $original_name = $file->getClientOriginalName();
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
                $this->uniqid = $uniqid;
                $this->user_id = $user_id;
                $this->app = $app;
                $this->action = $action;
                $this->unit_id = $unit_id;
                $this->filename = substr($original_name, 0, 20);
                $this->url = $url;
                $this->ext = $extension;
                $this->type = $type;
                $this->size = $size;
                $this->save();
                return $this;
            }
        } catch (OssException $exception) {
            return $exception;
        }
    }
}
