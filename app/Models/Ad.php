<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable=[
        'category_id',
        'title',
        'image',
        'link',
        'slug',
        'type',
        'online_time',
        'offline_time',
        'order',
        'status',
        ];
    public function category()
    {
        return $this->belongsTo(AdCategory::class);
    }
    public function getImageAttribute($value)
    {
        return $value ? env('ALIYUN_OSS_URL') . '/' . $value : '';
    }
}
