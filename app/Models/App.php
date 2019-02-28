<?php

namespace App\Models;

use function App\Utils\app_access_encode;
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

    /**
     * app之间登录通讯信息
     * @param $app_slug
     * @param $user
     * @return array
     */
    public function userBindedLoginInfo($app_slug, $user)
    {
        $app = self::where('slug', $app_slug)->first();
        $bindApp = $user->hasApps->where('app_id', $app->id)->first();
        $array['login_url'] = '';
        $array['app_url'] = '';
        $array['app_name'] = $app['name'];
        $res = false;
        if ($bindApp) {
            $res['app'] = $app->slug;
            $res['username'] = $bindApp->app_username;
            $res['userid'] = $bindApp->app_userid;
            $res['unitid'] = $bindApp->app_unitid;
            $res['ucenter_user_id'] = $bindApp->user_id;
            $array['app_url'] = $app->app_url;
            $array['login_url'] = $app->login_url;
            $res = app_access_encode($app->appkey, $app->appsecret, $res);
        } else {
            $res = false;
        }
        $array['access'] = $res;
        return $array;
    }

}
