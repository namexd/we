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

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_users', 'user_id', 'role_id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_has_permissions', 'user_id', 'permission_id');
    }

    public function apps(): BelongsToMany
    {
        return $this->belongsToMany(App::class, 'user_has_apps', 'user_id', 'app_id');
    }

    public function hasApps()
    {
        return $this->hasMany(UserHasApp::class);
    }

    public function hasRoles()
    {
        return $this->hasMany(RoleHasUser::class);
    }

    /**
     * 是否是冷王角色
     * @return mixed
     */
    public function isLengwang()
    {
        return $this->whereHas('hasRoles', function ($query) {
            $role = Role::where('slug', Role::冷王)->first();
            $query->where('role_id', $role->id)->where('user_id',$this->id);
        })->count();
    }

    /**
     * 是否是体验者权限
     * @return mixed
     */
    public function isTester()
    {
        return $this->whereHas('hasRoles', function ($query) {
            $role = Role::where('slug', Role::测试用户)->first();
            $query->where('role_id', $role->id)->where('user_id',$this->id);
        })->count();
    }

    /**
     * 添加体验者权限
     */
    public function registerTester()
    {
        if (!$this->isTester()) {
            $role = Role::where('slug', Role::测试用户)->first();
            $this->roles()->attach($role->id);
        }
    }

    /**
     * 剔除体验者权限
     */
    public function removeTester()
    {
        if ($this->isTester()) {
            $role = Role::where('slug', Role::测试用户)->first();
            $this->roles()->detach($role->id);
        }
    }

    public function withRole($role_id = Role::LENGWANG_ROLE_ID)
    {
        return $this->whereHas('hasRoles', function ($query) use ($role_id) {
            $query->where('role_id', $role_id);
        });
    }

}
