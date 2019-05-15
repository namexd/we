<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Transformers\UserTransformer;
use Carbon\Carbon;

class InfoController extends Controller
{
    public function index()
    {
        $info = [];
        $info['count_users'] = User::count();
        $info['count_users_verified'] = User::where('phone_verified','>',0)->count();
        $info['count_users_today'] = User::where('created_at','>',Carbon::today())->count();
        $info['count_users_verified_today'] = User::where('phone_verified','>',0)->where('created_at','>',Carbon::today())->count();
        $info['count_users_yesterday'] = User::where('created_at','>',Carbon::yesterday())->count();
        $info['count_users_verified_yesterday'] = User::where('phone_verified','>',0)->where('created_at','>',Carbon::yesterday())->count();
        $info['count_users_month'] = User::where('created_at','>',Carbon::now()->firstOfMonth())->count();
        $info['count_users_verified_month'] = User::where('phone_verified','>',0)->where('created_at','>',Carbon::now()->firstOfMonth())->count();

        $columns = [];
        $columns['count_users'] = '当前注册用户数';
        $columns['count_users_verified'] = '已认证用户数';
        $columns['count_users_today'] = '今日注册用户数';
        $columns['count_users_verified_today'] = '今日已认证用户数';
        $columns['count_users_yesterday'] = '昨日注册用户数';
        $columns['count_users_verified_yesterday'] = '昨日已认证用户数';
        $columns['count_users_month'] = '本月注册用户数';
        $columns['count_users_verified_month'] = '本月已认证用户数';

        $info['meta']['columns'] = $columns;
        return $this->response->array($info);

    }

    public function users()
    {
        $users = User::orderBy('id','desc')->with('weuser')->get();
        return $this->response->paginator($users,new UserTransformer());
    }
}
