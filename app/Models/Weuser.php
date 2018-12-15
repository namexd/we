<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Weuser extends Model
{
    protected $fillable = [
        'user_id',
        'nickname',
        'sex',
        'language',
        'city',
        'province',
        'country',
        'headimgurl',
        'privilege'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function weappHasWeuser()
    {
        return $this->hasOne(WeappHasWeuser::class);
    }
}
