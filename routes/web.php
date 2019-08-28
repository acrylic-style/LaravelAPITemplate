<?php

require('functions.php');

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

Route::any('/error/{error?}', function (Request $request, $error = 500) {
    return response()->json(["error" => "There was an error while processing your request.", "status" => (int) $error], (int) $error);
});

//Auth::routes(); // defines register, login route

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/redirect', 'Auth\LoginController@redirectToProvider');
Route::get('/callback', 'Auth\LoginController@handleProviderCallback');

Route::get('/registermc', function (Request $request) {
    $request->user()->refresh_token = session('refresh_token');
    return view('registermc');
});
