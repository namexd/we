<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_has_users', 'role_id', 'user_id');
    }

    public function isRole(string $role) : bool
    {
        return $this->pluck('slug')->contains($role);
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }

    public function can(string $permission) : bool
    {
        return $this->permissions()->where('slug', $permission)->exists();
    }

    /**
     * Check user has no permission.
     *
     * @param $permission
     *
     * @return bool
     */
    public function cannot(string $permission) : bool
    {
        return !$this->can($permission);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->users()->detach();

            $model->permissions()->detach();
        });
    }

}
