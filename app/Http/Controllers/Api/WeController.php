<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use App\Transformers\UserTransformer;
use App\Http\Requests\Api\UserRequest;

class WeController extends Controller
{
    public function wxconfig()
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        return $app->jssdk->buildConfig(array('onMenuShareQQ', 'onMenuShareWeibo'), true) ;
    }
}
