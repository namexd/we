<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use HasRoles;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
        'phone', 'phone_verified', 'realname'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function weuser()
    {
        return $this->HasOne(Weuser::class);
    }

    public function weappHasWeuser()
    {
        return $this->hasManyThrough(WeappHasWeuser::class, Weuser::class);
    }

    // Rest omitted for brevity

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function roles() : BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_users', 'user_id', 'role_id');
    }

    public function permissions() : BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_has_permissions', 'user_id', 'permission_id');
    }
}
