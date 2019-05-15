<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Transformers\UserTransformer;
use Carbon\Carbon;

class UsersController extends Controller
{

    public function crudModel()
    {
        return User::class;
    }


    public function statics()
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
        $columns[] = [
            'label'=>'count_users',
            'value'=>'当前注册用户数'
        ];
        $columns[] = [
            'label'=>'count_users_verified',
            'value'=>'已认证用户数'
        ];
        $columns[] = [
            'label'=>'count_users_today',
            'value'=>'今日注册用户数'
        ];
        $columns[] = [
            'label'=>'count_users_verified_today',
            'value'=>'今日已认证用户数'
        ];
        $columns[] = [
            'label'=>'count_users_yesterday',
            'value'=>'昨日注册用户数'
        ];
        $columns[] = [
            'label'=>'count_users_verified_yesterday',
            'value'=>'昨日已认证用户数'
        ];
        $columns[] = [
            'label'=>'count_users_month',
            'value'=>'本月注册用户数'
        ];
        $columns[] = [
            'label'=>'count_users_verified_month',
            'value'=>'本月已认证用户数'
        ];


        $info['meta']['columns'] = $columns;
        return $this->response->array($info);

    }

    public function index()
    {
        $users = User::orderBy('id','desc')->paginate();
        $res = $this->response->paginator($users,new UserTransformer());
        return $this->display($res);
    }
}
