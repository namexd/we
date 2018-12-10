<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use Dingo\Api\Routing\Helpers;
class CheckController extends Controller
{
    public function index(Request $request)
    {
        return ['time'=>time()];
    }
}
