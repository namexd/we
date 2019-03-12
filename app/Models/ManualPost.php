<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualPost extends Model
{
    protected $fillable = [
        'manual_id','product_id','version_id','category_id','content','status'
    ];

    public function manual()
    {
        return $this->belongsTo(Manual::class);
    }
    public function manual_version()
    {
        return $this->belongsTo(ManualVersion::class,'version_id');
    }
    public function manual_category()
    {
        return $this->belongsTo(ManualCategory::class,'category_id');
    }

    public function getContentAttribute($content)
    {
        $host=env('ALIYUN_OSS_URL');
        return urldecode(str_replace("{{host}}","$host", "$content"));
    }


    public function setContentAttribute($value)
    {
        $host=env('ALIYUN_OSS_URL');
        $this->attributes['content']= str_replace("$host","{{host}}", "$value");
    }

    public function getDetail($category_id)
    {
        $array=[];
        $results= self::where('category_id',$category_id)->get();

        foreach ($results as $key=>$v)
        {
            $array[]['manual']=$v->manual->title;
            $array[]['category']=$v->manual_category->title;
            $array[]['version']=$v->manual_version->number;
            $array[]['publish_time']=$v->manual_version->publish_time;
            $array[]['content']=$v->content;
        }
        return $array;
    }
}
