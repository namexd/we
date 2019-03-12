<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleHasManualCategory extends Model
{
    protected $fillable = [
        'role_id',
        'manual_category_id',
    ];
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function manual_category()
    {
        return $this->belongsTo(ManualCategory::class);
    }


}
