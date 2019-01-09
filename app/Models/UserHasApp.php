<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserHasApp extends Model
{
    protected $fillable = [
        'weapp_id',
        'weuser_id',
        'openid',
        'unionid'
    ];
    public function apps()
    {
        return $this->belongsTo(App::class);
    }
    public function users()
    {
        return $this->belongsTo(User::class);
    }


}
