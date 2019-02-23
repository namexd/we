<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'note',
        'status',
    ];

    public function HasUser()
    {
        return $this->hasMany(UserHasApp::class);
    }

    public function users()
    {
        return $this->hasManyThrough(
            User::class,
            UserHasApp::class,
            'app_id', // 用户表外键...
            'id', // 文章表外键...
            'id', // 国家表本地键...
            'user_id' // 用户表本地键...
        );
    }

}
