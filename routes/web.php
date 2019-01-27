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
//Route::get('/info', function () {
//    return phpinfo();
//});

//微信网页授权
Route::get('we/oauth', 'WeController@oauth')->name('we.oauth');
Route::get('we/callback', 'WeController@callback')->name('we.callback');
Route::get('we/test', 'WeController@test')->name('we.test');
Route::get('we/qrcode', 'WeController@qrcode')->name('we.qrcode');
Route::get('we/qrcode/callback', 'WeController@qrback')->name('we.qrback');
Route::get('files/{uniqid}', 'UploadsController@show')->name('uploads.show');
