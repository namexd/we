<?php

namespace App\Models\Bpms;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class BpmsAuthAPI
{
    private $api_server = '';

    public function setApiServer($app)
    {
        $this->api_server =$app->api_auth_url;
    }

    public function get($function, $params)
    {
        $client = new Client();
        $options = [
            'query' => $params,
        ];
        try {
            $res = $client->get($this->api_server . $function, $options);
            return $res->getBody()->getContents();
        } catch (ClientException $exception) {
            return $exception->getMessage();
        }
    }

    public function post($function, $params)
    {
        $client = new Client();
        $options = [
            'form_params' => $params,
        ];
        try {
            $res = $client->post($this->api_server . $function, $options);
            return $res->getBody()->getContents();
        } catch (ClientException $exception) {
            return $exception->getMessage();
        }
    }

    public function action($method, $function, $params)
    {
        switch ($method) {
            case 'GET':
                return $this->get($function, $params);
                break;
            case 'POST':
                return $this->post($function, $params);
                break;
        }
        return null;
    }

    public function getResponse($res)
    {
        $res_decode = json_decode($res, true) ;
        if ($res_decode and isset($res_decode['result'])) {
            return $res_decode['result'];
        } else if($res_decode and $res_decode['code']==200) {
            return true;
        }else{
            return false;
        }

    }
}
