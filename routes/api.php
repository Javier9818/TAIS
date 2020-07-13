<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/empresa','EmpresaController@store');
Route::post('/empresa/{id}','EmpresaController@destroy');

Route::post('/cliente','ClienteController@store');
Route::put('/cliente/{id}','ClienteController@update');
Route::post('/cliente/{id}','ClienteController@destroy');

Route::post('/proveedor','ProveedorController@store');
Route::put('/proveedor/{id}','ProveedorController@update');

Route::post('/user','UserController@store');
