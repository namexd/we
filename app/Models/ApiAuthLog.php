<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ApiAuthLog extends Authenticatable
{
    use Notifiable;

    const 普通登录 = 'api.authorizations.store';
    const 小程序登录 = 'api.weapp.authorizations.store';
    const 第三方登录 = 'api.socials.authorizations.store';
    const 微信登录 = 'api.we.authorizations.store';
    const 手机号登录 = 'api.users.phoneStore';
    const AUTH_ROUTES = [
        self::普通登录,
        self::小程序登录,
        self::第三方登录,
        self::微信登录,
        self::手机号登录,

    ];

    protected $connection = 'pgccsc';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'method', 'uri', 'query', 'params', 'route_name', 'user_agent_id', 'user_agent', 'ip'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
