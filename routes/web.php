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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/fund-wallet', 'HomeController@index');
Route::get('/add-new-expense', 'HomeController@expense');
Route::get('/view-transactions', 'HomeController@index');

Route::post('/mail/template', 'HomeController@save_template');
