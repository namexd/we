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

        $topics = Topic::orderBy('id','desc')->limit(5)->select('id','title','excerpt','slug','created_at','updated_at')->get();
        $data['data']['topics'] = $topics;

        return $this->response->array($data);
    }

}
