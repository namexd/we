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
        $menus = $model->withRoles($this->user()->roles->pluck('id'))->where('types', $is_mobile ? 'mobile' : 'web')->get();
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
