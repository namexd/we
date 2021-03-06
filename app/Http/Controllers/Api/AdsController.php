<?php

namespace App\Http\Controllers\Api;

use App\Models\Topic;
use App\Models\AdCategory;
use Illuminate\Http\Request;
use Jenssegers\Agent\Facades\Agent;
class AdsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $is_mobile = Agent::isMobile();
        $ads = AdCategory::where('types',$is_mobile ? 'mobile' : 'web')->with('ads')->get();
        $data['data'] = $ads->toArray();
        return $this->response->array($data);
    }

}
