<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppRedirect extends Model
{
    protected $fillable = [
        'app_id',
        'app_unitid',
        'redirect_url',
        'status',
    ];
}
