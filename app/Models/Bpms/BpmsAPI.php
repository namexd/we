<?php

namespace App\Models\Bpms;

use GuzzleHttp\Client;

class BpmsAPI
{
    const API_SERVER = 'http://dev2.lengwang.net/lib/bpmsinterface/';
//    const API_SERVER = 'http://apis.juhe.cn/simpleWeather/query';
    const 订单扫码入库 = 'receivevaccine';
    const 电子监管码查询 = 'vaccineinfo';
    public $access = '';

    public function __construct($access)
    {
        $this->access = $access;
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
        $res = $client->get(self::API_SERVER . $function, $options);
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
        $res = $client->post(self::API_SERVER . $function, $options);
        return $res->getBody()->getContents();
    }

    //订单扫码入库
    public function receivevaccine($qrcode)
    {
        return $this->get(self::订单扫码入库, ['qrcode' => $qrcode]);
    }

    //电子监管码查询
    public function vaccineinfo($piats)
    {
        return $this->get(self::电子监管码查询, ['piats' => $piats]);
    }
}
