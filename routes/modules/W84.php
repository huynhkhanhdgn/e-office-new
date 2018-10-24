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


use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Modules\W84', 'middleware' => 'auth'], function () {
    Route::any('/W84F1000/{task?}', 'W84F1000Controller@index');
    Route::any('/W84F1001/{task?}', 'W84F1001Controller@index');
    Route::any('/W84F2000/{task?}', 'W84F2000Controller@index');

    Route::any('/W84F2001/{task?}', 'W84F2001Controller@index');
});

