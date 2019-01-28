<?php

namespace App\Http\Controllers;

use App\Models\Upload;

class UploadsController extends Controller
{
    public function show($uniqid)
    {
        $upload = Upload::where('uniqid', $uniqid)->select('url')->first();
        return redirect($upload->url);
    }
}
