<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualVersion extends Model
{
    protected  $fillable=['manual_id','number','description','publish_time'];

    public function manual()
    {
        return $this->belongsTo(Manual::class,'manual_id','id');
    }
}
