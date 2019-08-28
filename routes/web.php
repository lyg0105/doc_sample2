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
    return view('hello');
});

Route::get('/hello','My\HelloController@index');

Route::get('/login','Login\LoginController@index');

Route::get('/api/login','Login\LoginController@login');

Route::get('/api/logout','Login\LoginController@logout');

Route::get('/main','Main\MainController@index')->middleware('checkLogin');
