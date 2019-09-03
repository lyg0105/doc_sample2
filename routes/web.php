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
    return redirect('main');
});

Route::get('/hello','My\HelloController@index');

Route::get('/login','Login\LoginController@index');

Route::get('/api/login','Login\LoginController@login');

Route::get('/api/logout','Login\LoginController@logout');

Route::middleware(['checkLogin'])->group(function () {
    Route::get('/main','Main\MainController@index');

    Route::get('/doc/list','Home\Doc\DocController@list');
    Route::get('/doc/write','Home\Doc\DocController@write');
    Route::post('/api/doc/list','Api\Doc\DocController@list');

    Route::post('/api/common/write','Api\Common\CommonApiController@write');
    Route::post('/api/common/xcolumn','Api\Common\CommonApiController@xcolumn');
});
