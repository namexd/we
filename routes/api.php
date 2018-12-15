<?php

use Illuminate\Http\Request;

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api'
], function($api) {
    $api->get('version',function(){
       return 'v1';
    });
    //微信sdk
    $api->post('wxconfig', 'WeController@wxconfig')->name('api.we.wxconfig');
    //微信网页授权
    $api->get('wxauth', 'WeController@wxauth')->name('api.we.wxauth');
});