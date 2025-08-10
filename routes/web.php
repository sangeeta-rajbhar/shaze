<?php

use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/', function () {
    return redirect('/login');
})->middleware('guest');

Route::get('/home', 'HomeController@index');

Route::group(array('prefix' => 'users'), function()
{
    Route::get('/', 'UsersController@index');
    Route::get('/create-user-sang', 'UsersController@create');
    Route::post('/store', 'UsersController@store');
    Route::get('/edit-user-sang/{id}', 'UsersController@edit');
    Route::post('/update', 'UsersController@update');
    Route::get('/destroy-user-sang/{id}', 'UsersController@destroy');
});

Route::group(array('prefix' => 'customer'), function()
{
    Route::get('/', 'CustomerController@index');
    Route::get('/show/{id}', 'CustomerController@show');
    Route::get('/import', 'CustomerController@import');
    Route::get('/sample-data', 'CustomerController@sampleData');
    Route::post('/import-data', 'CustomerController@importData');
    Route::get('/export', 'CustomerController@exportCustomer');  
});
