<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $fillable = ['uniqid', 'user_id', 'app', 'action', 'unit_id', 'filename', 'url', 'ext', 'type', 'note'];

    const ALLOW_EXT = [
        'jpg', 'jpeg', 'png', 'gif', 'bmp',
        'pdf',
        'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'wps'
    ];
}
