<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MercadoPublicoController;

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

Route::post('/tokens/create', 'App\Http\Controllers\TokenController@create');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/usuarios', 'App\Http\Controllers\UserController@index');
    Route::get('/usuarios/{id}', 'App\Http\Controllers\UserController@show');
    Route::post('/usuarios', 'App\Http\Controllers\UserController@store');
    Route::put('/usuarios/{id}', 'App\Http\Controllers\UserController@update');
    Route::delete('/usuarios/{id}', 'App\Http\Controllers\UserController@delete');

    Route::get('/mercado-publico', 'App\Http\Controllers\MercadoPublicoController@index');
});