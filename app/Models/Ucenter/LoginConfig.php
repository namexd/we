<?php

namespace App\Models\Ucenter;

use Illuminate\Database\Eloquent\Model;

class LoginConfig extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'value',
        'description'
    ];
}
