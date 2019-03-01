<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WechatMedia extends Model
{
    protected $fillable = [
        'media_id',
        'create_time',
        'update_time',
        'title',
        'author',
        'digest',
        'content',
        'content_source_url',
        'thumb_media_id',
        'show_cover_pic',
        'url',
        'thumb_url',
    ];
}
