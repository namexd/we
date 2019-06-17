<?php

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => ['serializer:array', 'bindings']
], function ($api) {
    $api->post('lengwang/topics', 'TopicsController@testStore')->name('topics.testStore');
    $api->get('lengwang/topics/categories', 'TopicsController@category')->name('topics.categories');
    $api->post('lengwang/uploads', 'UploadsController@store')->name('api.uploads.store');

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

        $api->get('test/list', 'TestController@list')->name('api.test.list');
        $api->get('test/detail/{topic}', 'TestController@detail')->name('api.test.detail');
        $api->get('sample/detail', function () {
            return response(['version' => 'v1.02']);
        });
        $api->get('version', function () {
            return response(['version' => 'v1.02']);
        });
        $api->get('clear_cache', function () {
            return Cache::flush()?'clear cache success;':'clear cache failt;';
        });
        $api->group([
            'middleware' => ['apilog']
        ], function ($api) {

            // 登录
            $api->post('authorizations', 'AuthorizationsController@store')
                ->name('api.authorizations.store');
            // 小程序登录
            $api->post('weapp/authorizations/{weapp?}', 'AuthorizationsController@weappStore')
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
        //解析二维码
        $api->get('users/qrcode/{code}', 'UsersController@qrcodeShow')->name('api.users.qrcode_show');
        //CCrp数据报表
        $api->group([
            'namespace' => 'Ccrp\Reports',
            'prefix' => 'reports',
        ], function ($api) {
            //导出报表回调地址
            $api->get('temperatures/coolers_history_30/{cooler_id}/{month}', 'TemperatureController@CoolerHistoryShow')->name('api.ccrp.reports.coolers_history_30.show');
            $api->get('coolers/count_cooler_number', 'CoolersController@countCoolerNumber')->name('api.ccrp.reports.coolers.count_cooler_number');
            $api->get('coolers/count_cooler_volume', 'CoolersController@countCoolerVolume')->name('api.ccrp.reports.coolers.count_cooler_volume');
            $api->get('coolers/count_cooler_status', 'CoolersController@countCoolerStatus')->name('api.ccrp.reports.coolers.count_cooler_status');


        });
        // 需要 token 验证的接口
        $api->group([
            'middleware' => ['api.auth', 'apilog']
        ], function ($api) {
            //广告
            $api->get('ads', 'AdsController@index')->name('ads.index');
            //文章
            $api->get('topics', 'TopicsController@index')->name('topics.index');
            $api->post('topics', 'TopicsController@store')->name('topics.store');
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
            //检测用户已经绑定信息的权限
            $api->get('users/apps', 'UsersController@apps')->name('api.users.apps');
            $api->put('users/apps', 'UsersController@bindApps')->name('api.users.bind_apps');
            $api->get('users/apps/{app_slug?}/check', 'UsersController@checkApps')->name('api.users.apps.check');
            $api->delete('users/apps', 'UsersController@unbindApps')->name('api.users.unbind_apps');
            //自动绑定系统接口
            $api->put('users/apps/auto_bind/{app_id}', 'UsersController@autoBindApps')->name('api.users.auto_bind_apps');
            //生成用户二维码
            $api->get('users/qrcode', 'UsersController@qrcode')->name('api.users.qrcode');
            // 可查看的用户列表（通讯录）
            $api->get('users/list', 'UsersController@index')->name('api.users.index');
            //更新微信信息
            $api->get('weusers', 'WeusersController@show')->name('api.weusers.show');
            $api->put('weusers', 'WeusersController@store')->name('api.weusers.store');
            $api->post('weusers/generateFormId', 'WeusersController@generateFormId')->name('api.weusers.generateFormId');
            //根据program查看app
            $api->get('apps/programs', 'AppsController@programs')->name('api.apps.programs');
            $api->get('apps/programs/user_not_bind', 'AppsController@programsUserNotBind')->name('api.apps.programs.user_not_bind');
            $api->get('apps/programs/{program}', 'AppsController@programsList')->name('api.apps.programs.list');
            //查看app
            $api->get('apps/{slug}', 'AppsController@show')->name('api.apps.show');
            //所有app
            $api->get('apps', 'AppsController@index')->name('api.apps.index');
            //消息统计
            $api->get('messages/count/{type?}', 'MessagesController@Count')->name('api.messages.count');
            $api->get('messages', 'MessagesController@index')->name('api.messages.index');
            $api->get('messages/{id}', 'MessagesController@show')->name('api.messages.show');
            //上传文件
            $api->post('uploads', 'UploadsController@store')->name('api.uploads.store');
            //角色可见的产品手册列表
            $api->get('manuals', 'ManualsController@index')->name('api.manuals.index');
            //手册功能列表
            $api->get('manual_categories', 'ManualsController@showCategories')->name('api.manuals.show_categories');
            //手册章节阅读
            $api->get('manual_posts', 'ManualsController@showPosts')->name('api.manuals.show_posts');
            //活动列表
            $api->get('meetings', 'MeetingsController@index')->name('api.meetings.index');
            //已报名列表
            $api->get('meeting_registrations/{meeting?}', 'MeetingsController@meetingRegistrations')->name('api.meetings.registrations');
            //提交报名信息
            $api->post('meeting_registrations', 'MeetingsController@postRegistration')->name('api.meetings.registrations');
            //财务发票管理
            $api->resource('oa_accountant_invoices', OaAccountantInvoiceController::class);
            //销售周报管理
            $api->resource('oa_sales_report', OaSalesReportController::class);


            //工具查询-批签发通用数据
            $api->get('piqianfas/vaccines','PiqianfasController@vaccines');
            $api->get('piqianfas/vaccine_companies','PiqianfasController@vaccine_companies');
            $api->get('piqianfas/products','PiqianfasController@product');
            $api->get('piqianfas/list','PiqianfasController@list');
            $api->get('piqianfas/monthlist','PiqianfasController@monthList');
            $api->get('piqianfas/detail','PiqianfasController@detail');
            //冷链系统数据
//            $api->group([
//                'namespace' => 'Ccrp',
//                'prefix' => 'ccrp',
//            ], function ($api) {
//                //单位树
//                $api->get('companies/tree/{id?}', 'CompaniesController@tree')->name('api.ccrp.companies.tree');
//                //单位下级单位
//                $api->get('companies/branch/{id?}', 'CompaniesController@branch')->name('api.ccrp.companies.branch');
//                // 当前单位
//                $api->get('companies/current/{id?}', 'CompaniesController@current')->name('api.ccrp.companies.current');
//                // 所有单位清单
//                $api->get('companies/{id?}', 'CompaniesController@index')->name('api.ccrp.companies.index');
//                // 管辖下级单位的管理水平报表
//                $api->get('companies/stat/manage/{id?}/{month?}', 'CompaniesController@statManage')->name('api.ccrp.companies.stat_manage');
//                $api->get('companies/stat/warnings/{id?}/{month?}', 'CompaniesController@statWarnings')->name('api.ccrp.companies.stat_warnings');
//                // 所有冰箱
//                $api->get('coolers', 'CoolersController@index')->name('api.ccrp.coolers.index');
//                $api->get('coolers/all', 'CoolersController@all')->name('api.ccrp.coolers.all');
//                $api->get('coolers/cooler_type100', 'CoolersController@coolerType100')->name('api.ccrp.coolers.coolerType100');
//                $api->get('coolers/{cooler}', 'CoolersController@show')->name('api.ccrp.coolers.show');
//                $api->get('coolers/{cooler}/history', 'CoolersController@history')->name('api.ccrp.coolers.history');
//                // 所有探头
//                $api->get('collectors', 'CollectorsController@index')->name('api.ccrp.collectors.index');
//                $api->get('collectors/realtime', 'CollectorsController@realtime')->name('api.ccrp.collectors.realtime');
//                $api->get('collectors/{collector}/history', 'CollectorsController@history')->name('api.ccrp.collectors.history');
//                $api->get('collectors/{collector}', 'CollectorsController@show')->name('api.ccrp.collectors.show');
//                // 所有联系人
//                $api->get('contacts', 'ConcatsController@index')->name('api.ccrp.contacts.index');
//                // 是否包含手机号的联系人
//                $api->get('contacts/{company_id}/has_phone/{phone}', 'ConcatsController@hasPhone')->name('api.ccrp.contacts.has_phone');
//                // 报警统计
//                $api->get('warning_events/categories/{handled?}', 'WarningAllEventsController@categories')->name('api.ccrp.warning_all_events.categories');
//                // 超温报警
//                $api->get('warning_events/overtemp/list/{handled}', 'WarningEventsController@index')->name('api.ccrp.warning_events.index');
//                $api->get('warning_events/overtemp/{event}', 'WarningEventsController@show')->name('api.ccrp.warning_events.show');
//                $api->put('warning_events/overtemp/{event}', 'WarningEvenunhandledtsController@update')->name('api.ccrp.warning_events.update');
//                // 断电报警
//                $api->get('warning_events/poweroff/list/{handled}', 'WarningSenderEventsController@index')->name('api.ccrp.warning_sender_events.index');
//                $api->get('warning_events/poweroff/{event}', 'WarningSenderEventsController@show')->name('api.ccrp.warning_sender_events.show');
//                $api->put('warning_events/poweroff/{event}', 'WarningSenderEventsController@update')->name('api.ccrp.warning_sender_events.update');
//                //报警发送记录
//                $api->get('warning_sendlogs/list/{type?}', 'WarningSendlogsController@index')->name('api.ccrp.warning_sendlogs.list');
//                $api->get('warning_sendlogs/{sendlog}', 'WarningSendlogsController@show')->name('api.ccrp.warning_sendlogs.show');
//                //人工测温记录,查看或者签名
//                $api->get('stat_manual_records', 'StatManualRecordsController@create')->name('api.ccrp.stat_manual_records.create');
//                $api->post('stat_manual_records', 'StatManualRecordsController@store')->name('api.ccrp.stat_manual_records.store');
//                $api->get('stat_manual_records/list/{month?}', 'StatManualRecordsController@index')->name('api.ccrp.stat_manual_records.index');
//                $api->get('stat_manual_records/show/{day?}/{session?}', 'StatManualRecordsController@show')->name('api.ccrp.stat_manual_records.show');
//                //冷链变更
//                $api->resource('equipment_change_applies', EquipmentChangeApplyController::class);
//                $api->get('equipment_change_types', 'EquipmentChangeApplyController@getChangeType');
//                //第三方校准证书
//                $api->get('jzzs', 'CertificationsController@index');
//                $api->get('jzzs/{id}', 'CertificationsController@show');
//                //巡检单
//                $api->get('check_tasks','CheckTasksController@index');
//                $api->get('check_tasks/{id}','CheckTasksController@show');
//                //冷藏车
//                $api->get('vehicles','VehiclesController@index');
//                $api->get('vehicles/refresh/{vehicle_id}','VehiclesController@refresh');
//                $api->get('vehicles/current/{vehicle_id}','VehiclesController@current');
//                $api->get('vehicles/vehicle_temp','VehiclesController@vehicle_temp');
//                $api->get('vehicles/vehicle_map','VehiclesController@vehicle_map');
//                $api->get('printers','PrintersController@index');
//                $api->get('printers/history_temp','PrintersController@printTemp');
//                $api->get('printers/clear/{id}',function ($id){
//                    $resp= file_get_contents('http://pr01.coldyun.com/WPServer/clearorder?sn='.$id);
//                    return json_decode($resp,true);
//                });
//                //CCrp数据报表
//                $api->group([
//                    'namespace' => 'Reports',
//                    'prefix' => 'reports',
//                ], function ($api) {
//                    $api->get('devices/statistic', 'DevicesController@statistic')->name('api.ccrp.reports.devices.statistic');
//                    $api->get('devices/stat_manages', 'DevicesController@statManage')->name('api.ccrp.reports.devices.stat_manage');
//                    $api->get('devices/stat_coolers', 'DevicesController@statCooler')->name('api.ccrp.reports.devices.stat_cooler');
//                    $api->post('devices/stat_cooler_history_temp', 'TemperatureController@statCoolerHistoryTemp')->name('api.ccrp.reports.devices.stat_cooler_history_temp');
//                    $api->get('temperatures/coolers_history_30/list/{month} ', 'TemperatureController@CoolerHistoryList')->name('api.ccrp.reports.coolers_history_30.list');
//                    $api->get('temperatures/coolers_history_30/{cooler_id}/{month}', 'TemperatureController@CoolerHistoryShow')->name('api.ccrp.reports.coolers_history_30.show');
//                    $api->get('warningers/statistics', 'WarningersController@statistics')->name('api.ccrp.reports.warningers.statistics');
//                    $api->get('login_logs/statistics', 'LoginLogsController@statistics')->name('api.ccrp.reports.login_logs.statistics');
//                    $api->get('login_logs/list', 'LoginLogsController@list')->name('api.ccrp.reports.login_logs.list');
//                    $api->get('coolers/logs', 'CoolersController@logs')->name('api.ccrp.reports.coolers.logs');
//                    $api->get('coolers/count_cooler_number', 'CoolersController@countCoolerNumber')->name('api.ccrp.reports.coolers.count_cooler_number');
//                    $api->get('coolers/count_cooler_volume', 'CoolersController@countCoolerVolume')->name('api.ccrp.reports.coolers.count_cooler_volume');
//                    $api->get('coolers/count_cooler_status', 'CoolersController@countCoolerStatus')->name('api.ccrp.reports.coolers.count_cooler_status');
//
//                    $api->get('companies/infomation/{slug}','CompaniesController@infomationDetail')->name('api.ccrp.reports.companies.info.detail');
//                    $api->get('companies/infomation','CompaniesController@infomation')->name('api.ccrp.reports.companies.infomation');
//                });
                //以下没有使用
                //同步数据，获取data_id之后的新数据
//            $api->post('collectors/sync',function (){
//                return response(['_SERVER'=>json_encode($_SERVER)]);
//            });
//            $api->post('collectors/sync', 'CollectorsController@sync')->name('api.collectors.sync');
                //同步基础数据 -- collector
//            $api->post('tables_syncs', 'TablesSyncsController@index')->name('api.table_syncs.index');

//            });

            //生物制品系统数据
            $api->group([
                'namespace' => 'Bpms',
                'prefix' => 'bpms',
            ], function ($api) {
                //单位树
//                $api->get('scan', 'ScanController@index')->name('api.bpms.scan.index');
                $api->get('{action}', 'ActionsController@index')->name('api.bpms.actions.index');
                $api->post('{action}', 'ActionsController@index')->name('api.bpms.actions.index');
                // 当前单位
            });
            //ccrp测试组
            $api->group([
                'namespace' => 'Ccrps',
                'prefix' => 'ccrp',
            ], function ($api) {
                $api->any('{action}', 'ActionsController@index')->name('api.ccrps.actions.index');
                $api->any('{action}/{params?}/{params2?}/{params3?}/{params4?}', 'ActionsController@index')->name('api.ccrps.actions.index');

            });
            //export
            $api->group([
                'namespace' => 'Exports',
                'prefix' => 'export',
            ], function ($api) {
                $api->any('{action}', 'ActionsController@index')->name('api.ccrps.actions.index');
                $api->any('{action}/{params?}/{params2?}/{params3?}/{params4?}', 'ActionsController@index')->name('api.exports.actions.index');

            });
            //Ocenter 旧的用户中心
            $api->group([
                'namespace' => 'Ocenter',
                'prefix' => 'ocenter',
            ], function ($api) {
                $api->get('wxmember/check_phone/{openid}', 'WxmembersController@checkPhone')->name('api.ocenter.wxmember.check_phone');
                $api->put('wxmember/bind_phone/{openid}/{phone}', 'WxmembersController@bindPhone')->name('api.ocenter.wxmember.bind_phone');
                // ccrp是否包含手机号的联系人
                $api->get('contacts/{company_id}/has_phone/{phone}', 'ConcatsController@hasPhone')->name('api.ccrp.contacts.has_phone');
            });
            //Ocenter 旧的用户中心
            $api->group([
                'namespace' => 'Admin',
                'prefix' => 'admin',
            ], function ($api) {
                $api->get('users/statics', 'UsersController@statics')->name('api.admin.users.statics');
                $api->get('users', 'UsersController@index')->name('api.admin.users.index');
            });
        });
    });
});
