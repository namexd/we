<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleHasMenu extends Model
{
    protected $fillable = [
        'role_id',
        'menu_id'
    ];
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }


}
