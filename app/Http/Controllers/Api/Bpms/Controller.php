<?php

namespace App\Http\Controllers\Api\Bpms;

use App\Http\Controllers\Api\Controller as BaseController;
use App\Models\App;
use App\Models\UserHasApp;

class Controller extends BaseController
{
    public $app_program = 'bpms';
    public $user;
    public $access;
    public $api_server = null;

    public function __construct()
    {

    }

    public function check()
    {
        $ucenter_user = $this->user();
        $app_ids = App::where('program',$this->app_program)->pluck('id');
        $user_app = UserHasApp::where('user_id', $ucenter_user->id)->whereIn('app_id', $app_ids)->first();
        if ($user_app == null) {
            return $this->response->error('系统账号绑定错误', 457);
        }
        $app = App::find($user_app->app_id);
        $this->api_server = $app->api_url;
        $user_info = (new App())->userBindedLoginInfo($app->slug, $ucenter_user);
        $this->access = $user_info['access'];
    }

}
