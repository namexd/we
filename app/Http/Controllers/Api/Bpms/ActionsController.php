<?php

namespace App\Http\Controllers\Api\Bpms;

use App\Models\Bpms\BpmsAPI;
use function App\Utils\microservice_access_decode;
use Log;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ActionsController extends Controller
{
    public function index($action)
    {
        $this->check();

        if (request()->get('debug')) {
            $response['_debug']['_url'] = $this->api_server;
            $response['_debug']['access'] = $this->access;
//            $response['_debug']['access_decode'] = microservice_access_decode($this->access);
            $response['_debug']['action'] = $action;
            $response['_debug']['method'] = request()->method();
            $response['_debug']['request'] = request()->all();

            return $this->response->array($response);
        }
        $api = new BpmsAPI($this->access,$this->api_server);
        $res = $api->action(request()->method(), $action, request()->all());
        return $this->getResponse($res,$action);
    }

    private function getResponse($res,$action=null)
    {
        $code = 301;
        $message = '出错了';
        $response = [];
        $res_decode = json_decode($res, true);
        if ($res_decode and isset($res_decode['result'])) {
            $response = $res_decode['result'] ?? null;
            $code = $res_decode['code'] ?? 301;
            $message = $res_decode['message'] ?? '出错啦';
        }elseif($res_decode)
        {
            $response['message']=$res_decode['message'];
            $response['code']=$res_decode['code'];
            if(env('BPMS_API_DEBUG')==true)
            {
                $response['_debug'] = json_decode($res, true)??$res;
                $response['_debug']['_url_api_server']=$this->api_server;
                $response['_debug']['_url_action']=$action;
            }
            //检测bpms接口log
            if(env('BPMS_API_DEBUG_LOG') == true)
            {
                $log = new Logger('bpms');
                $log->pushHandler(new StreamHandler(storage_path('logs/bpms.log'),Logger::INFO) );
                $log->addInfo('json解析出错了:'.$res);
            }

            return $this->response->array($response);
        }else{
            $response['message']='json解析出错了';
            $response['code']='-999';
            if(env('BPMS_API_DEBUG')==true) {
                $response['_debug'] = json_decode($res, true) ?? $res;
                $response['_debug']['_url_api_server'] = $this->api_server;
                $response['_debug']['_url_action'] = $action;
            }
            //检测bpms接口log
            if(env('BPMS_API_DEBUG_LOG') == true)
            {
                $log = new Logger('bpms');
                $log->pushHandler(new StreamHandler(storage_path('logs/bpms.log'),Logger::INFO) );
                $log->addInfo('json解析出错了:'.$res);
            }

            return $this->response->array($response);
        }

        if ($code < 300) {
            $response['_debug'] = json_decode($res, true)??$res;
            return $this->response->array($response);
        } elseif ($code == 401) {
            return $this->response->array($response);
        } elseif ($code == 500) {
            return json_decode($res, true)??$res;
        } else {
            return $this->response->error($message, $code);
        }
    }

}
