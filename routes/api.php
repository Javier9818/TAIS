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
Route::delete('/cliente/{id}', 'ClienteController@destroy');


Route::post('/proveedor','ProveedorController@store');
Route::put('/proveedor/{id}','ProveedorController@update');
Route::delete('/proveedor/{id}', 'ProveedorController@destroy');

Route::post('/unidad-negocio','UnidadNegocioController@store');
Route::put('/unidad-negocio/{id}','UnidadNegocioController@update');
Route::delete('/unidad-negocio/{id}','UnidadNegocioController@destroy');


/** CLIENTES EN CADENA */
Route::get('/clientes-cadena-libres/{unidad}/{comodin}','CadenaSuministrosController@clientesLibres');
Route::get('/niveles-clientes-cadena/{unidad}','CadenaSuministrosController@nivelesClientes');
Route::post('/niveles-clientes-cadena/{unidad}','CadenaSuministrosController@addNivelesClientes');
Route::get('/clientes-padre-cadena/{unidad}/{nivel}','CadenaSuministrosController@listarClientesPadre');
Route::get('/clientes-padre-comodin/{unidad}/{nivel}','CadenaSuministrosController@listarClientesPadreComodin');

Route::post('/cadena_clientes/{unidad}', 'CadenaSuministrosController@agregaCliente');
Route::get('/cadena_clientes/{unidad}', 'CadenaSuministrosController@listarClientes');
Route::put('/cadena_clientes', 'CadenaSuministrosController@updateCliente');
Route::delete('/cadena_clientes/{unidad}/{id}', 'CadenaSuministrosController@deleteCliente');

Route::get('/verifyCliente/{unidad}/{cliente}', 'CadenaSuministrosController@verifyCliente');


/** PROVEEDORES EN CADENA */
Route::get('/proveedores-cadena-libres/{unidad}/{comodin}','CadenaSuministrosController@proveedoresLibres');
Route::get('/niveles-proveedores-cadena/{unidad}','CadenaSuministrosController@nivelesProveedores');
Route::post('/niveles-proveedores-cadena/{unidad}','CadenaSuministrosController@addNivelesProveedores');
Route::get('/proveedores-padre-cadena/{unidad}/{nivel}','CadenaSuministrosController@listarProvedoresPadre');
Route::get('/proveedores-padre-comodin/{unidad}/{nivel}','CadenaSuministrosController@listarProvedoresPadreComodin');

Route::post('/cadena_proveedores/{unidad}', 'CadenaSuministrosController@agregaProveedor');
Route::delete('/cadena_proveedores/{unidad}/{id}', 'CadenaSuministrosController@deleteProveedor');
Route::get('/cadena_proveedores/{unidad}', 'CadenaSuministrosController@listarProveedores');
Route::put('/cadena_proveedores', 'CadenaSuministrosController@updateProveedor');

Route::get('/verifyProveedor/{unidad}/{proveedor}', 'CadenaSuministrosController@verifyProveedor');

//**PARA EL GRÁFICO DE CADENA */
Route::get('/entidades-cadena/{unidad}', 'CadenaSuministrosController@getEntidadesCadena');
Route::post('/cadena-historial', 'CadenaSuministrosController@setHistorial');
Route::get('/cadena-historial/{unidad}', 'CadenaSuministrosController@getHistorial');




Route::post('/user','UserController@store');
Route::put('/user','UserController@update');
Route::delete('/user/{id}','UserController@destroy');

Route::get('/proceso/{unidad}','ProcesoController@show');
Route::get('/procesos-graph/{unidad}','ProcesoController@showProcessGraphComplete');
Route::get('/procesos-prio/{unidad}','ProcesoController@getProcesosPrio');
Route::get('/proceso-unique/{unidad}/{mega}','ProcesoController@showUnique');
Route::post('/proceso','ProcesoController@store');
Route::put('/proceso/{id}','ProcesoController@update');

Route::get('/subproceso/{unidad}/{proceso}','ProcesoController@getSubProcesos');
Route::post('/subproceso','ProcesoController@storeSub');
Route::put('/subproceso/{id}','ProcesoController@updateSub');


Route::post('/mapa-proceso','MapaProcesoController@store');
Route::get('/mapa-proceso/subproceso/{unidad}/{proceso}','MapaProcesoController@getSubProcesosGraph');

Route::post('/criterio/{unidad}','PriorizacionController@storeCriterio');
Route::put('/criterio','PriorizacionController@updateCriterio');
Route::get('/criterio/{unidad}','PriorizacionController@showCriterios');

Route::get('/escala/{criterio}','PriorizacionController@showEscalas');
Route::post('/escala','PriorizacionController@storeEscala');
Route::put('/escala','PriorizacionController@updateEscala');

Route::get('/criterios-matriz/{unidad}','PriorizacionController@getCriteriosForMatriz');
Route::post('/matriz-priorizacion','PriorizacionController@setMatriz');

Route::get('/seguimiento/{proceso}/{unidad}','SeguimientoController@getSeguimiento');
Route::get('/rol/{unidad}','SeguimientoController@getRoles');
Route::post('/seguimiento','SeguimientoController@setSeguimiento');
Route::delete('/seguimiento/{id}','SeguimientoController@deleteSeguimiento');

Route::post('/caracterizacion','DocumentController@setCaracterizacion');
Route::get('/caracterizacion/{proceso}','DocumentController@getCaracterizacion');
Route::delete('/caracterizacion/{id}','DocumentController@deleteCaracterizacion');

Route::post('/diagrama-flujo','DocumentController@setDiagramaFlujo');
Route::get('/diagrama-flujo/{proceso}','DocumentController@getDiagramaFlujo');
Route::delete('/diagrama-flujo/{id}','DocumentController@deleteDiagramaFlujo');