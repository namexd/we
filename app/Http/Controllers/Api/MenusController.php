<?php

namespace App\Http\Controllers\Api;

use App\Models\Menu;
use App\Models\User;
use App\Transformers\MenuTransformer;
use Dingo\Api\Auth\Auth;
use Jenssegers\Agent\Facades\Agent;
use Request;

class MenusController extends Controller
{
    public function index()
    {
        $is_mobile = Agent::isMobile();
//        $menus = (new Menu())->where('types', $is_mobile ? 'mobile' : 'web')->orderBy('order', 'asc')->get();

        $menus = (new Menu())->toTree([],1);
        return $this->response->array($menus);
    }

    public function tree()
    {
//        $user = User::where('id', $this->user()->id)->first();
        $is_mobile = Agent::isMobile();

        $menus = (new Menu())->toTree(['weapp']);

        return $this->response->array($menus);
    }
}
