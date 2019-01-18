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

}
