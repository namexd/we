<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleHasPermission extends Model
{
    protected $fillable = [
        'role_id',
        'perssion_id'
    ];
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }


}
