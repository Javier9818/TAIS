<?php

use Illuminate\Http\Request;
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
// Auth::routes();
Route::post('/login', 'Auth\LoginController@authenticate')->name('login');
Route::post('/logout', function(){ Auth::logout(); return redirect('/');})->name('logout');

Route::get('/', function () {return view('auth.login');});
Route::get('/home', 'EmpresaController@index')->name('home')->middleware('can:gestionar-panel-general');
Route::get('/users', 'UserController@index')->name('users')->middleware('can:gestionar-panel-general');
Route::get('/empresa/{id}', 'EmpresaController@show');
Route::get('/empresa/{id}/clientes', 'EmpresaController@showClientes');
Route::get('/empresa/{id}/proveedores', 'EmpresaController@showProveedores');
Route::get('/empresa/{id}/unidades-negocio', 'EmpresaController@showUnidadesNegocio');
Route::get('/empresa/{id}/administrar-cadena', 'EmpresaController@showAdministrarCadena');
Route::get('/empresa/{id}/generar-cadena', 'EmpresaController@showGenerarCadena');
// Route::get('/aux',  function () {return view('welcome');});


Route::get('/empresa/{id}/procesos', 'EmpresaController@showProcesos');
Route::get('/empresa/{id}/mapa-procesos', 'MapaProcesoController@show');
Route::get('/empresa/{id}/priorizacion', 'PriorizacionController@show');
Route::get('/empresa/{id}/caracterizacion', 'CaracterizacionController@show');
Route::get('/empresa/{id}/diagrama-flujo', 'FlujoController@show');
Route::get('/empresa/{id}/diagrama-seguimiento', 'SeguimientoController@show');
Route::get('/empresa/{id}/gestion-indicadores', 'IndicadorController@show');


Route::post('/unidad-negocio-changue', function(Request $request){
    session(['unidad' => $request->unidad]);
    return redirect()->back();  
});

//GERENCIA DE SISTEMAS
Route::get('/empresa/{id}/objetivos-estrategicos', 'ObjetivoEstrategicoController@index');

Route::get('/empresa/{id}/objetivos-metas-empresariales', 'MetasEmpresarialesController@index');
Route::post('/version', 'MetasEmpresarialesController@setVersion');

Route::get('/empresa/{id}/objetivos-control', 'ObjetivoControlController@index');

Route::get('/transacciones', 'CascadaMetasController@getTransacciones');

Route::get('/empresa/{id}/alineamiento-resultante', 'CascadaMetasController@indexAlienamientoResultante');
Route::get('/empresa/{id}/objetivos-control-resultante', 'CascadaMetasController@indexControlResultante');