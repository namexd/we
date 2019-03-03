<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopicCategory extends Model
{
    protected $fillable=['name','slug','image','description','post_count'];

    public function topics()
    {
        return $this->hasMany(Topic::class,'category_id');
    }

    public function lastTopics()
    {
        return $this->hasMany(Topic::class,'category_id')->limit(10)->orderBy('id','desc');
    }

    public function getUpdatedAtColumn()
    {
        return null;
    }

    public function getCreatedAtColumn()
    {
        return null;
    }

    public function setUpdatedAt($value)
    {
        return null;
    }

    public function setCreatedAt($value)
    {
        return null;
    }

    public function getImageAttribute($value)
    {
        return $value ? env('ALIYUN_OSS_URL') . '/' . $value : '';
    }
}
