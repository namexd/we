<?php

namespace App\Http\Controllers\Api;

use App\Models\App;
use App\Transformers\AppTransformer;

class AppsController extends Controller
{

    public function index()
    {
        $apps = App::where('status',1)->get();
        return $this->response->collection($apps,new AppTransformer());
    }
    public function show($slug)
    {
        $apps = App::where('status',1)->where('slug',$slug)->first();
        return $this->response->item($apps,new AppTransformer());
    }
    public function programs()
    {
        $apps = App::where('status',1)->groupBy('program')->get();
        return $this->response->collection($apps,new AppTransformer());
    }
    public function programsList($program)
    {
        $apps = App::where('status',1)->where('program',$program)->get();
        return $this->response->collection($apps,new AppTransformer());
    }

    public function programsUserNotBind()
    {
        $user = $this->user();
        $programsBind = App::where('status',1)->whereIn('id',$user->hasApps->pluck('app_id'))->pluck('program');
        $programs = App::where('status',1)->whereNotIn('program',$programsBind)->groupBy('program')->get();
        return $this->response->collection($programs, new AppTransformer());
    }

}
