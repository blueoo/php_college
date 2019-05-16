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

Route::prefix('index')->group(function () {
    Route::any('/kafkaProducer',['as' => 'view', 'uses'=>'Index\IndexController@kafkaProducer']);
    Route::any('/testLog', 'Index\IndexController@testLog');
    Route::any('/testConsistentHash', 'Index\IndexController@testConsistentHash');
    Route::any('/export', 'Index\IndexController@export');
    Route::any('/testAMQP', 'Index\IndexController@testAMQP');
});