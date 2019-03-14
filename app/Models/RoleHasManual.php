<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleHasManual extends Model
{
    protected $fillable = [
        'role_id',
        'manual_id',
    ];
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function manual()
    {
        return $this->belongsTo(Manual::class,'manual_id','id');
    }


}
