<?php

namespace App\Transformers;

use App\Models\App;
use App\Models\Upload;
use League\Fractal\TransformerAbstract;

class UploadTransformer extends TransformerAbstract
{
    public function transform(Upload $upload)
    {
        return [
            'id' => $upload->id,
            'uniqid' => $upload->uniqid,
            'sluuser_idg' => $upload->user_id,
            'app' => $upload->app,
            'action' => $upload->action,
            'unit_id' => $upload->unit_id,
            'filename' => $upload->filename,
            'url' => $upload->url,
            'ext' => $upload->ext,
            'type' => $upload->type,
            'size' => $upload->size,
            'note' => $upload->note,
            'created_at' => $upload->created_at->toDateTimeString(),
            'updated_at' => $upload->updated_at->toDateTimeString(),
        ];
    }
}