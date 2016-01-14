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

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/', function () {
    return view('welcome');
});




Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController'
]);

Route::get('/demo', ['as' => 'demo', 'uses' => 'SoapController@demo']);
Route::get('/api', ['as' => 'api', 'uses' => 'APIController@demo']);
Route::get('/json', ['as' => 'json', 'uses' => 'JSONController@demo']);

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
    //
    Route::auth();

    Route::resource('shopify', 'ShopifyController');
    Route::resource('preferences', 'PreferencesController');
    Route::get('/home', 'HomeController@index');

});