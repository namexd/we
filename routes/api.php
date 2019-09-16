<?php

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => ['serializer:array', 'bindings']
], function ($api) {
    $api->post('lengwang/topics', 'TopicsController@testStore')->name('topics.testStore');
    $api->get('lengwang/topics/categories', 'TopicsController@category')->name('topics.categories');
    $api->post('lengwang/uploads', 'UploadsController@store')->name('api.uploads.store');
    $api->get('domains/{domain}','DomainsController@show');
//    $api->get('{path}', function (Request $request) use ($api) {
////拿到路由，查数据库/缓存，想怎么渲染就怎渲染
//        return $request->getPathInfo();
//    })->where('path', '.*');
    // 短信验证码，1分钟，1次

    $api->group([
        'middleware' => ['api.throttle'],
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expires'),
    ], function ($api) {
        //获取验证码
        $api->post('verification_codes', 'VerificationCodesController@store')
            ->name('api.verificationCodes.store');
        //默认菜单
        $api->get('home/mobile_default', 'HomeController@mobileDefault')->name('api.home.mobile');

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
            // 使用手机号和验证码登录,未测试
            $api->post('verifications/phone/{slug}', 'AuthorizationsController@AppPhoneStore')
                ->name('api.users.phoneStore.app');
        });
        $api->get('get_bind_miniProgram/{id}', 'UsersController@getBindMiniProgram');
        $api->get('get_certification/{id}', 'UsersController@getCertification');
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
            $api->put('users/change_real_name/{id}', 'UsersController@changeRealName')->name('api.users.change_real_name');
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
            //用户发票信息
            $api->get('invoice_type', 'UserInvoiceInfoController@getInvoiceType');
            $api->resource('user_invoice_info', UserInvoiceInfoController::class);
            $api->get('invoice_apply_status', 'UserInvoiceApplyController@getStatus');
            $api->resource('user_invoice_applies', UserInvoiceApplyController::class);


            //工具查询-批签发通用数据
            $api->get('piqianfas/vaccines','PiqianfasController@vaccines');
            $api->get('piqianfas/vaccine_companies','PiqianfasController@vaccine_companies');
            $api->get('piqianfas/products','PiqianfasController@product');
            $api->get('piqianfas/list','PiqianfasController@list');
            $api->get('piqianfas/monthlist','PiqianfasController@monthList');
            $api->get('piqianfas/detail','PiqianfasController@detail');
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
            //冷链系统数据
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
            $api->group( [
                'namespace' => 'Ucenter',
                'prefix' => 'ucenter',
            ], function ($api) {
                $api->any('{action}', 'ActionsController@index')->name('api.ccrps.actions.index');
                $api->any('{action}/{params?}/{params2?}/{params3?}/{params4?}', 'ActionsController@index')->name('api.exports.actions.index');
            });
            //Ocenter 旧的用户中心
            $api->group([
                'namespace' => 'Admin',
                'prefix' => 'admin',
            ], function ($api) {
                $api->get('users/statics', 'UsersController@statics')->name('api.admin.users.statics');
                $api->get('users', 'UsersController@index')->name('api.admin.users.index');
                $api->get('tools', 'ToolsController@infomation')->name('api.admin.tools.infomation');
                $api->get('tools/information/{slug}','ToolsController@infomationDetail')->name('api.admin.tools.info.detail');
            });

            //topics
            $api->group([
                'namespace' => 'Topic',
                'prefix' => 'topic',
            ], function ($api) {
                $api->any('{action}', 'ActionsController@index')->name('api.ccrps.actions.index');
                $api->any('{action}/{params?}/{params2?}/{params3?}/{params4?}', 'ActionsController@index')->name('api.ccrps.actions.index');

            });
        });
    });
});
