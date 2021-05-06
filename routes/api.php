<?php

use Illuminate\Support\Facades\Route;

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

Route::namespace('App\Http\Controllers')->group(function () {
    Route::post('/tokens/create', 'TokenController@create');
    Route::get('/mercado-publico', 'MercadoPublicoController@index');
});

Route::namespace('App\Http\Controllers')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/usuarios', 'UserController@index');
    Route::get('/usuarios/{id}', 'UserController@show');
    Route::post('/usuarios', 'UserController@store');
    Route::put('/usuarios/{id}', 'UserController@update');
    Route::delete('/usuarios/{id}', 'UserController@delete');
});
