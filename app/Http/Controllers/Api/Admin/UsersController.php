<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Transformers\UserAdminTransformer;
use Carbon\Carbon;

class UsersController extends Controller
{

    public function crudModel()
    {
        $this->setCrudModel(User::class);
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
            'label'=>'当前注册用户数',
            'value'=>'count_users',
        ];
        $columns[] = [
            'label'=>'已认证用户数',
            'value'=>'count_users_verified'
        ];
        $columns[] = [
            'label'=>'今日注册用户数',
            'value'=>'count_users_today'
        ];
        $columns[] = [
            'label'=>'今日已认证用户数',
            'value'=>'count_users_verified_today'
        ];
        $columns[] = [
            'label'=>'昨日注册用户数',
            'value'=>'count_users_yesterday'
        ];
        $columns[] = [
            'label'=>'昨日已认证用户数',
            'value'=>'count_users_verified_yesterday'
        ];
        $columns[] = [
            'label'=>'本月注册用户数',
            'value'=>'count_users_month'
        ];
        $columns[] = [
            'label'=>'本月已认证用户数',
            'value'=>'count_users_verified_month'
        ];


        $info['meta']['columns'] = $columns;
        return $this->response->array($info);

    }

    public function index()
    {
        $this->crudModel();
        $users = User::orderBy('id','desc')->paginate();
        $res = $this->response->paginator($users,new UserAdminTransformer());
        return $this->display($res);
    }
}
