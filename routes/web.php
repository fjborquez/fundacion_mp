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

Route::namespace('App\Http\Controllers')->group(function () {
    Route::get('/login', 'AdminLoginController@index')
        ->name('login');
    Route::post('/login', 'AdminLoginController@login');
});

Route::namespace('App\Http\Controllers')->middleware(['auth'])->group(function () {
    Route::get('/', 'AdminHomeController@index');
    Route::get('/home', 'AdminHomeController@index')
        ->name('home');
    Route::get('/configuraciones', 'AdminConfiguracionesController@index')
        ->name('configuraciones');
    Route::post('/configuraciones', 'AdminConfiguracionesController@store');
    Route::get('/usuarios', 'AdminUsuarioController@index');
    Route::get('/crearUsuarios', 'AdminUsuarioController@add')
        ->name('crearUsuarios');
    Route::post('/crearUsuarios', 'AdminUsuarioController@store');
    Route::get('/modificarUsuarios/{id}', 'AdminUsuarioController@modify')
        ->name('modificarUsuarios');
    Route::post('/modificarUsuarios/{id}', 'AdminUsuarioController@update');
    Route::get('/eliminarUsuarios/{id}', 'AdminUsuarioController@delete');
    Route::get('/repositorio', 'AdminHomeController@index');
    Route::get('/logout', 'AdminLoginController@logout');
});
