<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController'
]);

Route::get('/carrier', ['as' => 'carrier', 'uses' => 'CarrierController@index']);
Route::post('/uninstall', ['as' => 'uninstall', 'uses' => 'Auth\AuthController@uninstall']);
Route::resource('preference', 'PreferenceController');
Route::get('/edit_carrier', ['as' => 'edit_carrier', 'uses' => 'CarrierController@edit_carrier']);
Route::resource('carrier', 'CarrierController');
Route::resource('order', 'OrderController');






/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {

    Route::auth();
    Route::get('/', 'Auth\AuthController@access');
    Route::get('/authCallBack', 'Auth\AuthController@authCallback');
    Route::get('/home', 'HomeController@index');

});
