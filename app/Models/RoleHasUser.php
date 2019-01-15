<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleHasUser extends Model
{
    protected $fillable = [
        'role_id',
        'user_id',
    ];
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
