<?php

namespace App\Http\Controllers\Api\Bpms;

use App\Http\Controllers\Api\Controller as BaseController;
use App\Models\App;
use App\Models\UserHasApp;

class Controller extends BaseController
{
    public $app_id = 2;
    public $user;
    public $access;

    public function __construct()
    {

    }

    public function check()
    {
        $ucenter_user = $this->user();
        $user_app = UserHasApp::where('user_id', $ucenter_user->id)->where('app_id', $this->app_id)->first();
        if ($user_app == null) {
            return $this->response->error('系统账号绑定错误', 457);
        }
        $user_info = (new App())->userBindedLoginInfo(App::疫苗追溯系统, $ucenter_user);
        $this->access = $user_info['access'];
    }

}
