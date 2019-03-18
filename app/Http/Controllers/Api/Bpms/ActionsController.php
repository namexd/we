<?php

namespace App\Http\Controllers\Api\Bpms;


use App\Models\Bpms\BpmsAPI;

class ActionsController extends Controller
{
    public function index($action)
    {
        $this->check();
        $api = new BpmsAPI($this->access);
        $res = $api->action(request()->method(), $action, request()->all());
        print_r($res);

    }

}
