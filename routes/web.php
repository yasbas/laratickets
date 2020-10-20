<?php

use Illuminate\Support\Facades\Auth;
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

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('tickets');
    } else {
        return view('welcome');
    }
});

Auth::routes();

Route::get('/tickets', 'TicketController@index')->name('tickets.index');
Route::get('/tickets/create', 'TicketController@create')->name('tickets.create');
Route::get('/tickets/{ticket}', 'TicketController@show');
Route::post('/tickets/store', 'TicketController@store')->name('tickets.store');

Route::get('login/envato', 'Auth\LoginController@redirectToProvider');
Route::get('login/envato/callback', 'Auth\LoginController@handleProviderCallback');

