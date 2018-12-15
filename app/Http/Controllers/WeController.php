<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WeappHasWeuser;
use App\Models\Weuser;
use Request;
use EasyWeChat\Factory;

/**
 * 智慧冷链公众号：网页版微信授权
 * Class WeController
 * @package App\Http\Controllers
 */
class WeController extends Controller
{
    private $weapp_id = 1;

    public function oauth()
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $oauth = $app->oauth;
        return $oauth->redirect();
    }

    public function callback()
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $oauth = $app->oauth;

        // 获取 OAuth 授权结果用户信息
        $wxuser = $oauth->user();
        $userInfo = $wxuser->toArray();
        $array = [
            "id" => "o2uqWjhdiLr2_p01L2tFIriUbPNg",
            "name" => "刘念",
            "nickname" => "刘念",
            "avatar" => "http://thirdwx.qlogo.cn/mmopen/vi_32/PiajxSqBRaEJZHS11gEJuic5alkovVheP91WiauEx6b77icyM3RF7GTS4Cuu5ABL2Hs89Irwmnu8U9ja04Jk90O62g/132",
            "email" => null,
            "original" => [
                "openid" => "o2uqWjhdiLr2_p01L2tFIriUbPNg",
                "nickname" => "刘念",
                "sex" => 1,
                "language" => "zh_CN",
                "city" => "嘉定",
                "province" => "上海",
                "country" => "中国",
                "headimgurl" => "http://thirdwx.qlogo.cn/mmopen/vi_32/PiajxSqBRaEJZHS11gEJuic5alkovVheP91WiauEx6b77icyM3RF7GTS4Cuu5ABL2Hs89Irwmnu8U9ja04Jk90O62g/132",
                "privilege" => [],
                "unionid" => "o-zI_uJQvX8vh43xjdJ6iSNfy2ao",
                "provider" => "WeChat"
            ]
        ];

        // 检测we.openid 是否存
        $hasWeuser = WeappHasWeuser::where('weapp_id', $this->weapp_id)->where('openid', $userInfo['original']['openid'])->first();
        if (!$hasWeuser) {
            // 检测we.unionid 是否存
            $hasWeuser = WeappHasWeuser::where('unionid', $userInfo['original']['unionid'])->first();
            // 有unionid 但是没有openid，则为小程序等第二应用的用户，插入关系
            if ($hasWeuser) {
                $new_weappHasWeuser = [
                    'weapp_id' => $this->weapp_id,
                    'weuser_id' => $hasWeuser->weuser_id,
                    'openid' => $userInfo['original']['openid'],
                    'unionid' => $userInfo['original']['unionid'],
                ];
                $new_weappHasWeuser = new WeappHasWeuser($new_weappHasWeuser);
                $new_weappHasWeuser->save();
            }
        }
        if ($hasWeuser) {
            // 如果openid存在，检测unionid字段是否为空，为空则更新
            if (!$hasWeuser->unionid and $userInfo['original']['unionid']) {
                $hasWeuser->unionid = $userInfo['original']['unionid'];
                $hasWeuser->save();
            }
        } else {
            // 如果openid不存在，创建user,
            $new_user = [
                'name'=>$userInfo['original']['nickname']
            ];
            $user = new User($new_user);
            $user->save();

            $new_weuser = [
                'nickname' => $userInfo['original']['nickname'],
                'sex' => $userInfo['original']['sex'],
                'language' => $userInfo['original']['language'],
                'city' => $userInfo['original']['city'],
                'province' => $userInfo['original']['province'],
                'country' => $userInfo['original']['country'],
                'headimgurl' => $userInfo['original']['headimgurl'],
                'privilege' => json_encode($userInfo['original']['privilege'])
            ];
            $weuser = new Weuser($new_weuser);
            $user->weuser()->save($weuser);
            $new_weappHasWeuser = [
                'weapp_id' => $this->weapp_id,
                'openid' => $userInfo['original']['openid'],
                'unionid' => $userInfo['original']['unionid'],
            ];
            $weappHasWeuser = new WeappHasWeuser($new_weappHasWeuser);
            $user->weuser()->weappHasWeuser()->save($weappHasWeuser);
        }
        dd($user);

        // 跳转ucenter，并传入openid，ucenter主要维护绑定的手机号,绑定的系统账户等信息
        return redirect('/ucenter?openid=' . $userInfo);

    }

}
