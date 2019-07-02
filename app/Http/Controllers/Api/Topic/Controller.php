<?php

namespace App\Http\Controllers\Api\Topic;

use App\Http\Controllers\Api\Controller as BaseController;
use App\Models\App;
use App\Models\UserHasApp;

class Controller extends BaseController
{
    public $app_program = 'microservice_topic';
    public $user;
    public $access;
    public $api_server = null;

    public function __construct()
    {

    }

    public function check()
    {

        $ucenter_user = $this->user();
        $app = App::where('program',$this->app_program)->first();
        $this->api_server = $app->api_url;
        $user_info = (new App())->userBindedLoginInfo($app->slug, $ucenter_user,'microservice');
        $this->access = $user_info['access'];
    }

}
