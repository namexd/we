<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentChangeNew extends Model
{
    protected $fillable=[
        'cooler_name',
        'number',
        'brand',
        'version',
        'type',
        'size',
        'start_time',
        'is_medical'
    ];
    protected  $casts=[
        'is_medical'=>'boolean'
    ];
}
