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
    public function getValueAttribute($value)
    {
        if (is_array(json_decode($value,true)))
        {
            return json_decode($value,true);
        }else
        {
            return $value;
        }
    }
}
