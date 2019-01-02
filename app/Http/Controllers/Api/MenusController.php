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
    private $topid=[
        'mobile'=>1,
        'web'=>8,
        'web.ccms'=>9,
        'web.bpms'=>0,
    ];
    public function index()
    {
        $is_mobile = Agent::isMobile();
        $model = new Menu();
        $menus = $model->where('types', $is_mobile ? 'mobile' : 'web')->orderBy('order', 'asc')->get();
        $pid = $is_mobile?$this->topid['mobile']:$this->topid['web'];
        if($is_mobile)
        {
            $pid = $this->topid['mobile'];
        }else{
            if(request()->system and in_array(request()->system,$this->topid)){
                $pid=$this->topid[request()->system];
            }else{
                $pid = $this->topid['web.ccms'];
            }
        }
        $menus = $model->toTree($menus->toArray(),$pid);
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
