<?php

namespace App\Http\Controllers\Api\Bpms;


use App\Models\Bpms\BpmsAPI;

class ScanController extends Controller
{
    public function index()
    {
        $this->check();
        $api = new BpmsAPI($this->access);
        $http = $api->receivevaccine('C20181217145811,520122');
        dd($http);

    }

}
