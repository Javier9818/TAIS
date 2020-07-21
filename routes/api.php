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
Route::put('/empresa/{id}','EmpresaController@update');
Route::post('/empresa/{id}','EmpresaController@destroy');

Route::post('/cliente','ClienteController@store');
Route::put('/cliente/{id}','ClienteController@update');
Route::post('/cliente/{id}','ClienteController@destroy');

Route::post('/proveedor','ProveedorController@store');
Route::put('/proveedor/{id}','ProveedorController@update');

Route::post('/unidad-negocio','UnidadNegocioController@store');
Route::put('/unidad-negocio/{id}','UnidadNegocioController@update');

Route::get('/clientes-cadena-libres/{unidad}/{comodin}','CadenaSuministrosController@clientesLibres');
Route::get('/niveles-clientes-cadena/{unidad}','CadenaSuministrosController@nivelesClientes');
Route::post('/niveles-clientes-cadena/{unidad}','CadenaSuministrosController@addNivelesClientes');
Route::get('/clientes-padre-cadena/{unidad}/{nivel}','CadenaSuministrosController@listarClientesPadre');

Route::post('/cadena_clientes/{unidad}', 'CadenaSuministrosController@agregaCliente');
Route::get('/cadena_clientes/{unidad}', 'CadenaSuministrosController@listarClientes');

Route::post('/user','UserController@store');
