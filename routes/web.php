<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/info', function () {
    return phpinfo();
});

//微信网页授权,需要微信浏览器里打开
Route::get('we/oauth', 'WeController@oauth')->name('we.oauth');
Route::get('we/callback', 'WeController@callback')->name('we.callback');
Route::get('we/test', 'WeController@test')->name('we.test');
//PC浏览器扫码登录
//回调页面
Route::get('we/qrcode/bind', 'WeController@bind')->name('we.qrbind');
Route::get('we/qrcode/callback/bind', 'WeController@qrbackBind')->name('we.qrback.bind');
Route::get('we/qrcode/callback', 'WeController@qrback')->name('we.qrback');
//回调测试页面
Route::get('we/qrcode/home', 'WeController@qrhome')->name('we.qrhome');
//扫码页面
Route::get('we/qrcode/{redirect_url?}', 'WeController@qrcode')->name('we.qrcode');
//跳转登录
Route::get('we/jumpto/{to?}', 'WeController@jumpLoginFromUcenter')->name('we.jump_login_from_ucenter');
Route::get('we/{from?}/jumpto/{to?}', 'WeController@jumpLogin')->name('we.jump_login');
//跳转到oss文件
Route::get('files/{uniqid}', 'UploadsController@show')->name('uploads.show');

