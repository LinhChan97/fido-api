<?php


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

Route::group(['middleware' => 'cors', 'prefix' => '/v1'], function () {
    Route::post('/login', 'UserController@authenticate');
    Route::post('/register', 'UserController@register');
    Route::get('/logout/{api_token}', 'UserController@logout');
});

Route::get('groups', 'GroupController@index');

Route::get('groups/{id}', 'GroupController@show');

Route::post('groups', 'GroupController@store');

Route::put('groups/{id}', 'GroupController@update');

Route::delete('groups/{id}', 'GroupController@destroy');

// users

Route::get('users', 'UserController@index');

Route::get('users/{id}', 'UserController@show');

Route::post('users', 'UserController@store');

Route::put('users/{id}', 'UserController@update');

Route::delete('users/{id}', 'UserController@destroy');