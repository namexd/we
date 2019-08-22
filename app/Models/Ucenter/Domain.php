<?php

namespace App\Models\Ucenter;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $fillable = [
        'domain',
        'name',
        'slug',
        'description',
    ];

    public function config()
    {
        return $this->belongsToMany(LoginConfig::class,'domain_configs','domain_id','config_id')->withPivot('value');;
    }
}
