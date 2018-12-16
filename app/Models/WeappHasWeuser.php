<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeappHasWeuser extends Model
{
    protected $fillable = [
        'weapp_id',
        'weuser_id',
        'openid',
        'unionid'
    ];
    public function weuser()
    {
        return $this->belongsTo(Weuser::class);
    }

    public function weapp()
    {
        return $this->belongsTo(Weapp::class);
    }

}
