<?php

use Illuminate\Http\Request;

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => 'serializer:array'
], function ($api) {
    $api->group([
        'middleware' => ['api.throttle'],
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expires'),
    ], function ($api) {
        $api->get('version', function () {return response(['version'=>'v1.02']);});

        //微信jssdk的配置信息
        $api->post('we/wxconfig', 'WeController@wxconfig')->name('api.we.wxconfig');

        $api->get('time', 'CheckController@index')->name('api.check.time');
        // 登录
        $api->post('authorizations', 'AuthorizationsController@store')
            ->name('api.authorizations.store');
        // 小程序登录
        $api->post('weapp/authorizations', 'AuthorizationsController@weappStore')
            ->name('api.weapp.authorizations.store');
        // 第三方登录
        $api->post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
            ->name('api.socials.authorizations.store');
        // 使用openid直接登录
        $api->post('we/authorizations', 'AuthorizationsController@weStore')
            ->name('api.we.authorizations.store');
        // 刷新token
        $api->put('authorizations/current', 'AuthorizationsController@update')
            ->name('api.authorizations.update');
        // 删除token
        $api->delete('authorizations/current', 'AuthorizationsController@destroy')
            ->name('api.authorizations.destroy');
        // 需要 token 验证的接口
        $api->group(['middleware' => 'api.auth'], function($api) {
            // 所有单位
            $api->get('companies', 'CompaniesController@index')->name('api.companies.index');
            // 当前单位
            $api->get('companies/current', 'CompaniesController@current')->name('api.companies.current');
            // 当前登录用户信息
            $api->get('user', 'UsersController@me')->name('api.users.show');
            // 所有冰箱
            $api->get('coolers', 'CoolersController@index')->name('api.coolers.index');
            $api->get('coolers/{cooler}', 'CoolersController@show')->name('api.coolers.show');
            $api->get('coolers/{cooler}/history', 'CoolersController@history')->name('api.coolers.history');
            // 所有探头
            $api->get('collectors', 'CollectorsController@index')->name('api.collectors.index');
            // 所有联系人
            $api->get('contacts', 'ConcatsController@index')->name('api.contacts.index');
            // 实时温湿度
            $api->get('collectors/realtime', 'CollectorsController@realtime')->name('api.collectors.realtime');
            //同步数据，获取data_id之后的新数据
//            $api->post('collectors/sync',function (){
//                return response(['_SERVER'=>json_encode($_SERVER)]);
//            });
            $api->post('collectors/sync', 'CollectorsController@sync')->name('api.collectors.sync');
            //同步基础数据 -- collector
            $api->post('tables_syncs', 'TablesSyncsController@index')->name('api.table_syncs.index');
        });
    });
});
