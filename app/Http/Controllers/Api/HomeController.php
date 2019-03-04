<?php

namespace App\Http\Controllers\Api;

use App\Models\AdCategory;
use App\Models\Menu;
use App\Models\Topic;

class HomeController extends Controller
{
    public function mobile()
    {
        $is_mobile = 1;
        $menus = (new Menu())->listTree($this->user(),$is_mobile);
        $data['data']['menus'] = $menus;

        $ads = AdCategory::where('types',$is_mobile ? 'mobile' : 'web')->with('ads')->get()->toArray();
        $data['data']['ads'] = $ads;

        $topics = Topic::orderBy('id','desc')->limit(5)->select('id','title','image','excerpt','slug','created_at','updated_at')->get();
        $data['data']['topics'] = $topics;
        if($menus==[])
        {
            $user = $this->user();
            if(count($user->hasApps))
            {
                $data['data']['announcement'] = '<div style="background:#faf2cc;color:#FF0000; padding:10px;">您绑定的 <b style="color:blue">'.(implode(',',$user->apps->pluck('name')->toArray())).'</b> ，功能仍正在陆续开发中，敬请期待。</br></div>';
            }else{
                $data['data']['announcement'] = '<div style="background:#faf2cc;color:#FF0000; padding:10px;">您可能没有<b style="color:blue">绑定系统</b>，请到【我的】页面绑定业务系统。<br>已开通的系统：冷链监测系统；</br></div>';
            }
        }else{
            $data['data']['announcement'] = '<div style="background:#faf2cc;color:#FF0000; padding:10px;">感谢使用，功能陆续开放中。如遇到问题，请联系客服。</div>';
        }

        return $this->response->array($data);
    }

}
