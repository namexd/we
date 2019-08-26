<?php

namespace App\Http\Controllers\Api\Ccrps;

use App\Models\App;
use App\Models\Microservice\MicroserviceAPI;
use function App\Utils\microservice_access_decode;
use function App\Utils\microservice_access_encode;

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
            $response['_debug']['access_decode'] = microservice_access_decode($this->access);
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
        } elseif ($res and json_decode($res, true) == null and $res != strip_tags($res)) {
            //如果非json并且包含html直接返回
            return $res;
        } elseif ($rs = json_decode($res, true) == null) {
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

    /**
     * 匿名登陆
     * @param $app_slug
     * @param $action
     * @param null $params
     * @param null $params2
     * @param null $params3
     * @param null $params4
     * @return \Dingo\Api\Http\Response|void
     */
    public function anonymous($app_slug,$action, $params = null, $params2 = null, $params3 = null, $params4 = null)
    {
        $app = App::where('slug',$app_slug)->first();
        $this->api_server = $app->api_url;

        $res['userinfo'] = [];
        $this->access = microservice_access_encode($app->appkey, $app->appsecret, $res);

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
            $response['_debug']['access_decode'] = microservice_access_decode($this->access);
            $response['_debug']['action'] = $action;
            $response['_debug']['method'] = request()->method();
            $response['_debug']['request'] = request()->all();

            return $this->response->array($response);
        }
        $api = new MicroserviceAPI($this->access, $this->api_server);
        $res = $api->action(request()->method(), $action, request()->all());

        return $this->getResponse($res, $action);
    }


}
