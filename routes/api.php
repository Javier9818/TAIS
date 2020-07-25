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


/** CLIENTES EN CADENA */
Route::get('/clientes-cadena-libres/{unidad}/{comodin}','CadenaSuministrosController@clientesLibres');
Route::get('/niveles-clientes-cadena/{unidad}','CadenaSuministrosController@nivelesClientes');
Route::post('/niveles-clientes-cadena/{unidad}','CadenaSuministrosController@addNivelesClientes');
Route::get('/clientes-padre-cadena/{unidad}/{nivel}','CadenaSuministrosController@listarClientesPadre');

Route::post('/cadena_clientes/{unidad}', 'CadenaSuministrosController@agregaCliente');
Route::get('/cadena_clientes/{unidad}', 'CadenaSuministrosController@listarClientes');
Route::put('/cadena_clientes', 'CadenaSuministrosController@updateCliente');
Route::delete('/cadena_clientes/{unidad}/{id}', 'CadenaSuministrosController@deleteCliente');


/** PROVEEDORES EN CADENA */
Route::get('/proveedores-cadena-libres/{unidad}/{comodin}','CadenaSuministrosController@proveedoresLibres');
Route::get('/niveles-proveedores-cadena/{unidad}','CadenaSuministrosController@nivelesProveedores');
Route::post('/niveles-proveedores-cadena/{unidad}','CadenaSuministrosController@addNivelesProveedores');
Route::get('/proveedores-padre-cadena/{unidad}/{nivel}','CadenaSuministrosController@listarProvedoresPadre');

Route::post('/cadena_proveedores/{unidad}', 'CadenaSuministrosController@agregaProveedor');
Route::delete('/cadena_proveedores/{unidad}/{id}', 'CadenaSuministrosController@deleteProveedor');
Route::get('/cadena_proveedores/{unidad}', 'CadenaSuministrosController@listarProveedores');
Route::put('/cadena_proveedores', 'CadenaSuministrosController@updateProveedor');

//**PARA EL GRÁFICO DE CADENA */
Route::get('/entidades-cadena/{unidad}', 'CadenaSuministrosController@getEntidadesCadena');

Route::post('/user','UserController@store');
