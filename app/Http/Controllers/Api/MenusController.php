<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Ccrps\ActionsController;
use App\Models\Menu;
use Jenssegers\Agent\Facades\Agent;

class MenusController extends Controller
{
    public function index($system = null)
    {
        if ($system) {
            if (in_array($system, Menu::SYTEMS)) {
                $system = Menu::SYTEMS[$system];
            } else {
                $system = null;
            }
        }
        $is_mobile = Agent::isMobile();
        $menus = (new Menu())->listTree($this->user(), $is_mobile, $system);
        $data['data'] = $menus;
        return $this->response->array($data);
    }

    //测试
    public function tree()
    {
//        $user = User::where('id', $this->user()->id)->first();
        $is_mobile = Agent::isMobile();

        $menus = (new Menu())->toTree(['weapp']);

        return $this->response->array($menus);
    }
}
