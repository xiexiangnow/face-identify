<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


use Illuminate\Support\Facades\Route;

Route::get('/', 'UploadController@index');

//上传执行地址
Route::any('uploadPic','UploadController@upload');

//人脸列表
Route::get('lists','UploadController@picList');

//详情
Route::get('detail','UploadController@detail');


Route::resource('upload','UploadController',['only' => ['index','show']]);



//支付测试
Route::get('payTest','AlipayController@payTest');