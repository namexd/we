<?php

namespace App\Http\Controllers\Api;

use function App\Utils\send_vcode;
use Request;

class TestController extends Controller
{
    public function index()
    {
        $rs = send_vcode(13817181960,'1234');
        dd($rs);
    }
}
