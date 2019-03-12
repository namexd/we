<?php

namespace App\Models;

use function App\Utils\app_access_encode;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    const 冷链监测系统 = 'ccrp';
    const 疫苗追溯系统 = 'bpms';
    protected $fillable = ['name', 'slug', 'image', 'appkey', 'appsecret', 'status'];

    public function getImageAttribute($value)
    {
        return $value ? config('api.defaults.image.host') . '/' . $value : '';
    }

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

    /**
     * @param $user User
     * @param $app_username
     * @param $app_userid
     * @param $app_unitid
     * @return mixed
     */
    public function bind($user, $app_username, $app_userid, $app_unitid)
    {

        $app_id = $this->id;
        $data = [
            'app_id' => $app_id,
            'app_username' => $app_username,
            'app_userid' => $app_userid,
            'app_unitid' => $app_unitid
        ];

        $app = self::find($app_id);
        //更新角色
        $role_slug = $app->slug;
        if ($role_slug == Role::冷链用户) {
            $app_user = \App\Models\Ccrp\User::find($app_userid);
            $userCompany = $app_user->userCompany;
            if ($userCompany->cdcLevel() >= 1) {
                $role_slug = Role::冷链疾控用户;
            }
        }
        $role = Role::where('slug', $role_slug)->first();
        if ($role) {
            if (!$user->hasRoles->where('role_id', $role->id)->count()) {
                $user->hasRoles()->create(['role_id' => $role->id]);
            }
        }
        //添加绑定关系
        $rs = $user->hasApps()->create($data);
        return $rs;
    }

    public function unbind($user)
    {
        $user_has_app = UserHasApp::where('app_id', $this->id)->where('user_id', $user->id)->first();
        $role_slug = $this->slug;
        if ($role_slug == Role::冷链用户) {
            $app_user = \App\Models\Ccrp\User::find($user_has_app->app_userid);
            $userCompany = $app_user->userCompany;
            if ($userCompany->cdcLevel() >= 1) {
                $role_slug = Role::冷链疾控用户;
            }
        }
        $role = Role::where('slug', $role_slug)->first();
        if ($role) {
            RoleHasUser::where('role_id', $role->id)->where('user_id', $user->id)->delete();
        }
        $rs = $user_has_app->delete();
        return $rs;
    }

}
