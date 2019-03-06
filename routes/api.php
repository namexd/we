<?php

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => ['serializer:array', 'bindings']
], function ($api) {
//    $api->get('{path}', function (Request $request) use ($api) {
////拿到路由，查数据库/缓存，想怎么渲染就怎渲染
//        return $request->getPathInfo();
//    })->where('path', '.*');
    // 短信验证码，1分钟，1次
    $api->group([
        'middleware' => ['api.throttle', 'api.auth'],
        'limit' => config('api.rate_limits.sms.limit'),
        'expires' => config('api.rate_limits.sms.expires'),
    ], function ($api) {
        $api->post('verification_codes', 'VerificationCodesController@store')
            ->name('api.verificationCodes.store');
    });

    $api->group([
        'middleware' => ['api.throttle'],
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expires'),
    ], function ($api) {
        $api->get('version', function () {
            return response(['version' => 'v1.02']);
        });
        $api->group([
            'middleware' => ['apilog']
        ], function ($api) {
            // 登录
            $api->post('authorizations', 'AuthorizationsController@store')
                ->name('api.authorizations.store');
            // 小程序登录
            $api->post('weapp/authorizations', 'AuthorizationsController@weappStore')
                ->name('api.weapp.authorizations.store');
            // 第三方登录
            $api->post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
                ->name('api.socials.authorizations.store');
            // 微信使用openid直接登录
            $api->post('we/authorizations', 'AuthorizationsController@weStore')
                ->name('api.we.authorizations.store');
            // 使用手机号和验证码登录,未测试
            $api->post('verifications/phone', 'AuthorizationsController@phoneStore')
                ->name('api.users.phoneStore');
        });
        $api->get('test', 'TestController@index');
        //微信jssdk的配置信息
        $api->post('we/wxconfig', 'WeController@wxconfig')->name('api.we.wxconfig');

        $api->get('time', 'CheckController@index')->name('api.check.time');

        // 刷新token
        $api->put('authorizations/current', 'AuthorizationsController@update')
            ->name('api.authorizations.update');
        // 删除token
        $api->delete('authorizations/current', 'AuthorizationsController@destroy')
            ->name('api.authorizations.destroy');
        //测试
        $api->get('test/form', 'TestController@form')->name('api.test.form');
        $api->get('test/form/preview', 'TestController@formPreview')->name('api.test.form.preview');
        $api->get('test/ajax', 'TestController@ajax')->name('api.test.ajax');
        // 查看文件信息
        $api->get('uploads/{uniqid}', 'UploadsController@show')->name('api.uploads.show');
        // 需要 token 验证的接口
        $api->group([
            'middleware' => ['api.auth', 'apilog']
        ], function ($api) {
            //广告
            $api->get('ads', 'AdsController@index')->name('ads.index');
            //文章
            $api->get('topics', 'TopicsController@index')->name('topics.index');
            $api->get('topics/{topic}', 'TopicsController@show')->name('topics.show');
            $api->get('topic_categories', 'TopicCategoriesController@index')->name('topics_categories.index');
            $api->get('topic_categories/{category}', 'TopicCategoriesController@show')->name('topics_categories.show');
            //首页
            $api->get('home/mobile', 'HomeController@mobile')->name('api.home.mobile');
            $api->get('home/ccrp', 'HomeController@ccrp')->name('api.home.ccrp');
            //获取菜单
            $api->get('menus/{system?}', 'MenusController@index')->name('api.menus.index');
            // 当前登录用户信息
            $api->get('users', 'UsersController@me')->name('api.users.show');
            // 验证手机号（更新手机号)
            $api->put('users/verification_codes', 'UsersController@verificationCodes')->name('api.users.verification_codes');
            //小程序更新手机号，未使用
            $api->put('users/phone', 'UsersController@updatePhone')->name('api.users.updatePhone');
            //用户绑定的系统信息
            $api->get('users/apps/{app_slug?}/login_url', 'UsersController@appsLoginUrl')->name('api.users.apps.login_url');
            $api->get('users/apps', 'UsersController@apps')->name('api.users.apps');
            $api->put('users/apps', 'UsersController@bindApps')->name('api.users.bind_apps');
            $api->delete('users/apps', 'UsersController@unbindApps')->name('api.users.unbind_apps');
            // 可查看的用户列表（通讯录）
            $api->get('users/list', 'UsersController@index')->name('api.users.index');
            //更新微信信息
            $api->get('weusers', 'WeusersController@show')->name('api.weusers.show');
            $api->put('weusers', 'WeusersController@store')->name('api.weusers.store');
            //系统信息
            $api->get('apps', 'AppsController@index')->name('api.apps.index');
            //消息统计
            $api->get('messages/count/{type?}', 'MessagesController@Count')->name('api.messages.count');
            //上传文件
            $api->post('uploads', 'UploadsController@store')->name('api.uploads.store');
            //冷链系统数据
            $api->group([
                'namespace' => 'Ccrp',
                'prefix' => 'ccrp',
            ], function ($api) {
                //单位树
                $api->get('companies/tree/{id?}', 'CompaniesController@tree')->name('api.ccrp.companies.tree');
                // 当前单位
                $api->get('companies/current/{id?}', 'CompaniesController@current')->name('api.ccrp.companies.current');
                // 所有单位清单
                $api->get('companies/{id?}', 'CompaniesController@index')->name('api.ccrp.companies.index');
                // 管辖下级单位的管理水平报表
                $api->get('companies/stat/manage/{id?}/{month?}', 'CompaniesController@statManage')->name('api.ccrp.companies.stat_manage');
                $api->get('companies/stat/warnings/{id?}/{month?}', 'CompaniesController@statWarnings')->name('api.ccrp.companies.stat_warnings');
                // 所有冰箱
                $api->get('coolers', 'CoolersController@index')->name('api.ccrp.coolers.index');
                $api->get('coolers/{cooler}', 'CoolersController@show')->name('api.ccrp.coolers.show');
                $api->get('coolers/{cooler}/history', 'CoolersController@history')->name('api.ccrp.coolers.history');
                // 所有探头
                $api->get('collectors', 'CollectorsController@index')->name('api.ccrp.collectors.index');
                // 所有联系人
                $api->get('contacts', 'ConcatsController@index')->name('api.ccrp.contacts.index');
                // 报警统计
                $api->get('warning_events/categories/{handled?}', 'WarningAllEventsController@categories')->name('api.ccrp.warning_all_events.categories');
                // 超温报警
                $api->get('warning_events/overtemp/list/{handled}', 'WarningEventsController@index')->name('api.ccrp.warning_events.index');
                $api->get('warning_events/overtemp/{event}', 'WarningEventsController@show')->name('api.ccrp.warning_events.show');
                $api->put('warning_events/overtemp/{event}', 'WarningEventsController@update')->name('api.ccrp.warning_events.update');
                // 断电报警
                $api->get('warning_events/poweroff/list/{handled}', 'WarningSenderEventsController@index')->name('api.ccrp.warning_sender_events.index');
                $api->get('warning_events/poweroff/{event}', 'WarningSenderEventsController@show')->name('api.ccrp.warning_sender_events.show');
                $api->put('warning_events/poweroff/{event}', 'WarningSenderEventsController@update')->name('api.ccrp.warning_sender_events.update');
                //报警发送记录
                $api->get('warning_sendlogs/list/{type?}', 'WarningSendlogsController@index')->name('api.ccrp.warning_sendlogs.list');
                $api->get('warning_sendlogs/{sendlog}', 'WarningSendlogsController@show')->name('api.ccrp.warning_sendlogs.show');
                //人工测温记录,查看或者签名
                $api->get('stat_manual_records', 'StatManualRecordsController@create')->name('api.ccrp.stat_manual_records.create');
                $api->post('stat_manual_records', 'StatManualRecordsController@store')->name('api.ccrp.stat_manual_records.store');
                $api->get('stat_manual_records/list/{month?}', 'StatManualRecordsController@index')->name('api.ccrp.stat_manual_records.index');
                $api->get('stat_manual_records/show/{day?}/{session?}', 'StatManualRecordsController@show')->name('api.ccrp.stat_manual_records.show');

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
