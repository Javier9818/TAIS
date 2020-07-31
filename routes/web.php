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

Route::get('/', function () {return view('auth.login');});
Route::get('/home', 'EmpresaController@index')->name('home');
Route::get('/users', 'UserController@index')->name('users');
Route::get('/empresa/{id}', 'EmpresaController@show');
Route::get('/empresa/{id}/clientes', 'EmpresaController@showClientes');
Route::get('/empresa/{id}/proveedores', 'EmpresaController@showProveedores');
Route::get('/empresa/{id}/unidades-negocio', 'EmpresaController@showUnidadesNegocio');
Route::get('/empresa/{id}/administrar-cadena', 'EmpresaController@showAdministrarCadena');
Route::get('/empresa/{id}/generar-cadena', 'EmpresaController@showGenerarCadena');
Route::get('/aux',  function () {return view('welcome');});

Route::post('/login', 'Auth\LoginController@authenticate')->name('login');
Route::post('/logout', function(){ Auth::logout(); return redirect('/');})->name('logout');
//Auth::routes();


