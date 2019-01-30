<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable=['title','body','user_id','category_id','reply_count','view_count','last_reply_user_id','order','excerpt','slug','status'];

    public function category()
    {
        return $this->belongsTo(TopicCategory::class);
    }

    public function admin()
    {
        return $this->belongsTo(Administrator::class,'user_id');
    }

    public function replayUser()
    {
        return $this->belongsTo(User::class,'last_reply_user_id');
    }

    public function getImageAttribute($value)
    {
        return config('filesystems.disks.admin.url').'/'.$value;
    }
}
