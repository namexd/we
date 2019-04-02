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
        return $this->getResponse($res);
    }

    private function getResponse($res)
    {
        $code = 301;
        $message = '出错了';
        $response = [];
        $res = json_decode($res, true);
        if ($res) {
            $response = $res['result'] ?? [];
            $code = $res['code'] ?? 301;
            $message = $res['message'] ?? '出错啦';
        }
        if ($code < 300) {
            $response['_debug'] = json_encode($res);
            return $this->response->array($response);
        } else {
            return $this->response->error($message, $code);
        }
    }

}
