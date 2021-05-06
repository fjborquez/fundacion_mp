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

Route::get('/login', 'App\Http\Controllers\AdminLoginController@index')->name('login');
Route::post('/login', 'App\Http\Controllers\AdminLoginController@login');

Route::middleware(['auth'])->group(function () {
    Route::get('/', 'App\Http\Controllers\AdminHomeController@index');
    Route::get('/home', 'App\Http\Controllers\AdminHomeController@index')->name('home');
    Route::get('/configuraciones', 'App\Http\Controllers\AdminConfiguracionesController@index')->name('configuraciones');
    Route::post('/configuraciones', 'App\Http\Controllers\AdminConfiguracionesController@store');
    Route::get('/usuarios', 'App\Http\Controllers\AdminUsuarioController@index');
    Route::get('/crearUsuarios', 'App\Http\Controllers\AdminUsuarioController@add')->name('crearUsuarios');
    Route::post('/crearUsuarios', 'App\Http\Controllers\AdminUsuarioController@store');
    Route::get('/modificarUsuarios/{id}', 'App\Http\Controllers\AdminUsuarioController@modify')->name('modificarUsuarios');
    Route::post('/modificarUsuarios/{id}', 'App\Http\Controllers\AdminUsuarioController@update');
    Route::get('/eliminarUsuarios/{id}', 'App\Http\Controllers\AdminUsuarioController@delete');
    Route::get('/repositorio', 'App\Http\Controllers\AdminHomeController@index');
    Route::get('/logout', 'App\Http\Controllers\AdminLoginController@logout');
});