<?php

namespace App\Http\Controllers;

use App\Models\Upload;

class UploadsController extends Controller
{
    public function show($uniqid)
    {
        $upload = Upload::where('uniqid', $uniqid)->select('url')->first();
        $url = strrpos($upload->url, 'https') !== false ? $upload->url : config('filesystems.disks.oss.url').'/'.$upload->url;
        return redirect($url);
    }
}
