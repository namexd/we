<?php

namespace App\Http\Controllers\Api\Bpms;

use App\Models\Bpms\BpmsAPI;
use Log;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

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
        if ($res_decode = json_decode($res, true) and isset($res_decode['result'])) {
            $response = $res_decode['result'] ?? null;
            $code = $res_decode['code'] ?? 301;
            $message = $res_decode['message'] ?? '出错啦';
        }else{
            $response['message']='json解析出错了';
            $response['code']='-999';
            $response['_debug'] = json_decode($res, true)??$res;

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
        } elseif ($code == 500) {
            return json_decode($res, true)??$res;
        } else {
            return $this->response->error($message, $code);
        }
    }

}
