<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UploadRequest;
use App\Models\Upload;
use App\Transformers\UploadTransformer;
use OSS\Core\OssException;

class UploadsController extends Controller
{

    public function show($uniqid)
    {
        $upload = Upload::where('uniqid', $uniqid)->first();
        return $this->response->item($upload, new UploadTransformer());
    }

    public function store(UploadRequest $request)
    {
        $file = $request->file('file');
        $app = $request->app;
        $action = $request->action;
        $unit_id = $request->unit_id;
        if ($file->isValid()) {
            try {
                $upload = new Upload();
                $uploaded = $upload->upload($file, $this->user()->id, $app, $action, $unit_id);
                return $this->response->item($uploaded, new UploadTransformer());
            } catch (OssException $exception) {
                return $this->response->error($exception->getMessage(), 422);
            }
        }
        return $this->response->noContent();
    }

}
