<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Weapp extends Model
{
    protected $fillable = [
        'type',
        'name',
        'app_id',
        'secret',
        'token',
        'aes_key',
        'other'
    ];
    public function hasWeuser()
    {
        return $this->hasOne(WeappHasWeuser::class);
    }
}
