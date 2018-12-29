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
        $menus = (new Menu())->where('types', $is_mobile ? 'mobile' : 'web')->orderBy('order', 'asc')->get();
        return $this->response->collection($menus, new MenuTransformer());
    }

    public function tree()
    {
        dd($this->user()->isRole('ccms.user'));
        $user = User::where('id', $this->user()->id)->first();
        $is_mobile = Agent::isMobile();
        $menus = (new Menu())->where('types', $is_mobile ? 'mobile' : 'web')->orderBy('order', 'asc');

        dd($user->inRoles(['ccms.user', 'developer']));
        $menus = (new Menu())->toTree();
        foreach ($menus as $item) {
            if ($user->visible($item['roles']) && (empty($item['permission']) ?: $user->can($item['permission']))) {
                dd($item);
            }

        }
        return $this->response->collection($menus, new MenuTransformer());
    }
}
