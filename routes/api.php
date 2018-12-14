<?php

use Illuminate\Http\Request;

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api'
], function($api) {
    $api->get('version',function(){
       return 'v1';
    });
    $api->post('wxconfig', 'WeController@wxconfig')
        ->name('api.we.wxconfig');
});