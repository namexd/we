<?php

namespace App\Http\Controllers\Api\Exports;

use App\Models\Microservice\MicroserviceAPI;

class ActionsController extends Controller
{
    public function index($action, $params = null, $params2 = null, $params3 = null, $params4 = null)
    {
        $this->check();
        if ($params) {
            $action .= '/' . $params;
            if ($params2) {
                $action .= '/' . $params2;
                if ($params3) {
                    $action .= '/' . $params3;
                    if ($params4) {
                        $action .= '/' . $params4;
                    }
                }
            }
        }
        if (request()->get('debug')) {
            $response['_debug']['_url'] = $this->api_server;
            $response['_debug']['access'] = $this->access;
            $response['_debug']['action'] = $action;
            $response['_debug']['method'] = request()->method();
            $response['_debug']['request'] = request()->all();
            return $this->response->array($response);
        }
        $api = new MicroserviceAPI($this->access, $this->api_server);
        $res = $api->action(request()->method(), $action, request()->all());

        return $this->getResponse($res, $action);
    }

    private function getResponse($res)
    {
        if ($rs = json_decode($res, true)) {
            if (isset($rs['status_code']) and $rs['status_code'] != 200) {
                return $this->response->array($rs)->setStatusCode($rs['status_code']);
            }
            return $rs;
        } elseif ($rs == null) {
            {
                if (isset($rs['status_code']) and $rs['status_code'] != 200) {
                    return $this->response->created();
                }
                return $this->response->noContent();
            }
        } else {
            return $this->response->error('未知异常', 401);
        }
    }

}
