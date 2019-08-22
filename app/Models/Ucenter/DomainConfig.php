<?php

namespace App\Models\Ucenter;

use Illuminate\Database\Eloquent\Model;

class DomainConfig extends Model
{
    protected $fillable = [
        'domain_id',
        'config_id',
        'value',
    ];
}
