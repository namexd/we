<?php

namespace App\Http\Controllers\Api;

use Auth;
use Cache;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    use Helpers;
    public $pagesize = 20;

}