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
