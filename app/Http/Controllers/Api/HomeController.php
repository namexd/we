<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Ccrps\ActionsController;
use App\Models\AdCategory;
use App\Models\App;
use App\Models\Ccrp\User;
use App\Models\Menu;
use App\Models\UserHasApp;
use App\Transformers\Ccrp\CompanyInfoTransformer;
use App\Transformers\UserTransformer;

class HomeController extends Controller
{
    public function mobile()
    {
        $is_mobile = 1;
        $menus = (new Menu())->listTree($this->user(), $is_mobile);
        $data['data']['menus'] = $menus;

        $ads = AdCategory::where('types', $is_mobile ? 'mobile' : 'web')->with('ads')->get()->toArray();
        $data['data']['ads'] = $ads;

        $user = $this->user();
        if ($user->isTester()) {
            $data['data']['announcement'] = config('api.defaults.announcement_tester');
        } else {
            if (count($user->hasApps)) {
                $data['data']['announcement'] = config('api.defaults.announcement');
                if (in_array(App::where('slug', App::冷链监测系统)->first()->id, $user->hasApps->pluck('app_id')->toArray())) {
                    $result = (new ActionsController())->index('collectors/count_warningSetting_unset');
                    $data['data']['announcement']='您当前有';
                    if ($result['warning_count'] > 0) {
                        $data['data']['announcement'] .= "<span style='color:red; padding:2px 2px;'>{$result['warning_count']}个探头报警未开启;</span>";
                    }
                    if ($result['status_2'] > 0) {
                        $data['data']['announcement'] .= "<span style='color:orange; padding:2px 2px;'>{$result['status_3']}个设备维修;</span>";
                    }
                    if ($result['status_3'] > 0) {
                        $data['data']['announcement'] .= "<span style='color:orange; padding:2px 2px;'>{$result['status_3']}个设备备用;</span>";
                    }
                    if ($result['status_6'] > 0) {
                        $data['data']['announcement'] .= "<span style='color:orange; padding:2px 2px;'>{$result['status_6']}个设备除霜;</span>";
                    }
                    if ($result['status_5'] > 0) {
                        $data['data']['announcement'] .= "<span style='color:orange; padding:2px 2px;'>{$result['status_5']}个设备盘苗;</span>";
                    }
                }
            } else {
                $data['data']['announcement'] = config('api.defaults.announcement_noapp');
            }
        }

        return $this->response->array($data);
    }

    public function mobileDefault()
    {
        $is_mobile = 1;
        $menus = (new Menu())->listTreeDefault($is_mobile);
        $data['data']['menus'] = $menus;

        $ads = AdCategory::where('types', $is_mobile ? 'mobile' : 'web')->with('ads')->get()->toArray();
        $data['data']['ads'] = $ads;

        $data['data']['announcement'] = config('api.defaults.announcement_noapp');


        return $this->response->array($data);
    }

    public function ccrp()
    {
        $is_mobile = 0;

        $user = $this->user();
        $menus = (new Menu())->listTree($user, $is_mobile, Menu::网页端冷链监测);
        $data['data']['menus'] = $menus;

        if ($menus == []) {
            if (count($user->hasApps)) {
                $data['data']['announcement'] = '<div style="background:#faf2cc;color:#FF0000; padding:10px;">您绑定的 <b style="color:blue">'.(implode(',', $user->apps->pluck('name')->toArray())).'</b> ，功能仍正在陆续开发中，敬请期待。</br></div>';
            } else {
                $data['data']['announcement'] = '<div style="background:#faf2cc;color:#FF0000; padding:10px;">您可能没有<b style="color:blue">绑定系统</b>，请到【我的】页面绑定业务系统。<br>已开通的系统：冷链监测系统；</br></div>';
            }
        } else {
            $data['data']['announcement'] = config('api.defaults.announcement');
        }


        $apps = $user->apps;
        if ($apps) {
            foreach ($apps as $app) {
                switch ($app->slug) {
                    case  'ccrp':
                        $user_app = UserHasApp::where('user_id', $user->id)->where('app_id', $app->id)->first();
                        if ($user_app) {
                            $ccrp_user = User::where('id', $user_app->app_userid)->where('status', 1)->first();
                            $ccrp_company = $ccrp_user->userCompany;
                            if ($ccrp_company) {
                                $company = (new CompanyInfoTransformer)->transform($ccrp_company);
                                $data['data']['company'] = $company;
                            }
                        }
                        break;
                }
            }
        }
        $user->weuser;
        $data['meta']['user'] = (new UserTransformer())->transform($user);

        return $this->response->array($data);
    }

}
