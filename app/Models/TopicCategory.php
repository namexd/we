<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopicCategory extends Model
{
    protected $fillable=['name','slug','description','post_count'];

    public function topics()
    {
        return $this->hasMany(Topic::class,'category_id');
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
}
