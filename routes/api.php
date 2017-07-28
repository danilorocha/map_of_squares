<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::resource('territories', 'TerritoryController');

Route::resource('squares', 'SquareController');
Route::get('squares/{x}/{y}', 'SquareController@show');
Route::patch('squares/{x}/{y}/{paint}', 'SquareController@update');
