<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
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
        $info['new_sers'] = User::orderBy('id','desc')->with('weuser')->limit(5)->get();

        $columns = [];
        $columns['count_users'] = '当前注册用户数';
        $columns['count_users_verified'] = '已验证手机号用户数';
        $columns['count_users_today'] = '今日注册用户数';
        $columns['count_users_verified_today'] = '今日已验证手机号用户数';
        $columns['new_sers'] = '最新注册用户';

        $info['meta']['columns'] = $columns;
        return $this->response->array($info);

    }
}
