<?php

namespace App\Models\Bpms;

use GuzzleHttp\Client;

class BpmsAPI
{
    const 电子监管码查询 = 'vaccineinfo';
    public $access = '';
    private $api_server = '';

    public function __construct($access,$api_server=null)
    {
        $this->access = $access;
        $this->api_server = $api_server??config('api.defaults.bpms_api_server');
    }

    public function get($function, $params)
    {
        $client = new Client();
        $header = [
            'access' => $this->access,
        ];
        $options = [
            'query' => $params,
            'headers' => $header,
        ];
        $res = $client->get($this->api_server . $function, $options);
        return $res->getBody()->getContents();
    }

    public function post($function, $params)
    {
        $client = new Client();
        $header = [
            'access' => $this->access,
        ];
        $options = [
            'form_params' => $params,
            'headers' => $header,
        ];
        $res = $client->post($this->api_server . $function, $options);
        return $res->getBody()->getContents();
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

}
