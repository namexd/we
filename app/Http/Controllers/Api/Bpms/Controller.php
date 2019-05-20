<?php

namespace App\Http\Controllers\Api\Bpms;

use App\Http\Controllers\Api\Controller as BaseController;
use App\Models\App;
use App\Models\UserHasApp;

class Controller extends BaseController
{
    public $app_id = 2;
    public $appdemo_id = 4;
    public $user;
    public $access;
    public $api_server = null;

    public function __construct()
    {

    }

    public function check()
    {
        $ucenter_user = $this->user();
        $user_app = UserHasApp::where('user_id', $ucenter_user->id)->where('app_id', $this->app_id)->first();
        $app_slug = App::疫苗追溯系统;
        $this->api_server = config('api.defaults.bpms_api_server');
        if ($user_app == null) {
            //demo版
            $user_app = UserHasApp::where('user_id', $ucenter_user->id)->where('app_id', $this->appdemo_id)->first();
            if ($user_app == null) {
                return $this->response->error('系统账号绑定错误', 457);
            }else{
                $app_slug = App::疫苗追溯系统演示系统;
                $this->api_server = config('api.defaults.bpmsdemo_api_server');
            }
        }
        $user_info = (new App())->userBindedLoginInfo($app_slug, $ucenter_user);
        $this->access = $user_info['access'];
    }

}
