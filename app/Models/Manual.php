<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manual extends Model
{

    protected  $fillable=['type','image','title','description','slug','status'];
    const ManualType=[
        1=>'软件产品',
        2=>'硬件产品',
    ];

    public function categories()
    {
        return $this->hasMany(ManualCategory::class,'manual_slug','slug');
    }
    public function version()
    {
        return $this->hasMany(ManualVersion::class,'manual_id');
    }

    /**
     * note：
     * author: xiaodi
     * date: 2019/3/11 13:10
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_has_manuals', 'manual_id', 'role_id');
    }
    public function hasRoles()
    {
        return $this->hasMany(RoleHasManual::class);
    }

    public function withRoles($role_ids)
    {
        $rs = $this->whereHas('hasRoles', function ($query) use ($role_ids) {
            $query->whereIn('role_id', $role_ids);
        });
        return $rs;
    }

    public function lists($user,$pageSize=10)
    {
       $manuals=$this->withRoles($user->roles->pluck('id'))->paginate($pageSize);
       return $manuals;
    }
}
