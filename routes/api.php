<?php

use Illuminate\Http\Request;

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' =>  ['serializer:array', 'bindings']
], function ($api) {
    // 短信验证码，1分钟，1次
    $api->group([
        'middleware' => ['api.throttle','api.auth'],
        'limit' => config('api.rate_limits.sms.limit'),
        'expires' => config('api.rate_limits.sms.expires'),
    ],function($api){
        $api->post('verificationCodes', 'VerificationCodesController@store')
            ->name('api.verificationCodes.store');
    });

    //广告
    $api->get('ads',  'AdsController@index')->name('ads.index');
    $api->get('topics',  'TopicsController@index')->name('topics.index');
    $api->get('topics/{topic}',  'TopicsController@show')->name('topics.show');

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
        //测试
        $api->get('test','TestController@index')->name('api.test');
        // 使用手机号和验证码登录,未测试
        $api->post('verifications/phone', 'AuthorizationsController@phoneStore')
            ->name('api.users.phoneStore');
        // 需要 token 验证的接口
        $api->group(['middleware' => 'api.auth'], function($api) {

            //获取菜单
            $api->get('menus/{system?}','MenusController@index')->name('api.menus.index');
            $api->get('menus/tree','MenusController@tree')->name('api.menus.tree');
            // 当前登录用户信息
            $api->get('user', 'UsersController@me')->name('api.users.show');
            // 可查看的用户列表（通讯录）
            $api->get('users', 'UsersController@index')->name('api.users.index');
            // 验证手机号（更新手机号)
            $api->put('users/phone', 'UsersController@phoneUpdate')->name('api.users.phoneUpdate');
            $api->group([
                'namespace' => 'Ccms',
                'prefix'=>'ccms',
            ],function($api){
                // 所有单位
                $api->get('companies', 'CompaniesController@index')->name('api.companies.index');
                $api->get('companies/tree/{id?}', 'CompaniesController@tree')->name('api.companies.tree');
                // 当前单位
                $api->get('companies/current', 'CompaniesController@current')->name('api.companies.current');
                // 管辖下级单位的管理水平报表
                $api->get('companies/stat/manage/{id?}/{month?}', 'CompaniesController@stat_manage')->name('api.companies.stat_manage');
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

                // 报警统计
                $api->get('warning_events/categories/{handled?}', 'WarningAllEventsController@categories')->name('api.warning_all_events.categories');
                // 超温报警
                $api->get('warning_events/overtemp/list/{handled}', 'WarningEventsController@index')->name('api.warning_events.index');
                $api->get('warning_events/overtemp/{event}', 'WarningEventsController@show')->name('api.warning_events.show');
                $api->put('warning_events/overtemp/{event}', 'WarningEventsController@update')->name('api.warning_events.update');
                // 断电报警
                $api->get('warning_events/poweroff/list/{handled}', 'WarningSenderEventsController@index')->name('api.warning_sender_events.index');
                $api->get('warning_events/poweroff/{event}', 'WarningSenderEventsController@show')->name('api.warning_sender_events.show');
                $api->put('warning_events/poweroff/{event}', 'WarningSenderEventsController@update')->name('api.warning_sender_events.update');


                //同步数据，获取data_id之后的新数据
//            $api->post('collectors/sync',function (){
//                return response(['_SERVER'=>json_encode($_SERVER)]);
//            });
//            $api->post('collectors/sync', 'CollectorsController@sync')->name('api.collectors.sync');
                //同步基础数据 -- collector
//            $api->post('tables_syncs', 'TablesSyncsController@index')->name('api.table_syncs.index');

            });

        });
    });
});
