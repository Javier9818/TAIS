<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Empresa;
use App\Proveedor;
use App\UnidadNegocio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $empresas = Empresa::orderBy('created_at','desc')->get();
        return view('admin.home', ["empresas" => $empresas]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $empresa = Empresa::create([
            "ruc" => $request->ruc,
            "nombre" => $request->nombre,
            "direccion" => $request->direccion,
            "descripcion" => $request->descripcion
        ]);
        return response()->json(["empresa" => $empresa], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $empresa = Empresa::find($id);
        return view('empresa.inicio', ["empresa" => $empresa]);
    }

    public function showClientes($id)
    {
        $empresa = Empresa::find($id);
        $clientes = DB::table('entidad')
            ->join('clientes', 'clientes.entidad_id', '=', 'entidad.id')
            ->where('entidad.empresa_id', '=', $id)
            ->select('entidad.*')
            ->get();
        return view('empresa.clientes', ["empresa" => $empresa, "clientes" => $clientes]);
    }

    public function showProveedores($id)
    {
        $empresa = Empresa::find($id);
        $proveedores = DB::table('entidad')
            ->join('proveedores', 'proveedores.entidad_id', '=', 'entidad.id')
            ->where('entidad.empresa_id', '=', $id)
            ->select('entidad.*')
            ->get();
        return view('empresa.proveedores', ["empresa" => $empresa, "proveedores" => $proveedores]);
    }

    public function showUnidadesNegocio($id){
        $empresa = Empresa::find($id);
        $unidades_negocio = DB::table('unidad_negocio')
            ->where('unidad_negocio.empresa_id', '=', $id)
            ->get();
        return view('empresa.unidades_negocio', ["empresa" => $empresa, "unidades_negocio" => $unidades_negocio]);
    }

    public function showAdministrarCadena($id){
        $empresa = Empresa::find($id);
        $unidades_negocio = UnidadNegocio::all();
        $proveedores = DB::table('proveedores')->join('entidad', 'proveedores.entidad_id', '=', 'entidad.id')->get();
        $clientes =  DB::table('clientes')->join('entidad', 'clientes.entidad_id', '=', 'entidad.id')->get();
        return view('empresa.administrar_cadena', ["empresa" => $empresa, "unidades_negocio" => $unidades_negocio, "proveedores" => $proveedores, "clientes" => $clientes]);
    }

    public function showGenerarCadena($id){
        $empresa = Empresa::find($id);
        $unidades_negocio = UnidadNegocio::all();
        return view('empresa.generar', ["empresa" => $empresa, "unidades_negocio" => $unidades_negocio]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $empresa = Empresa::find($id)->update([
            "ruc" => $request->ruc,
            "nombre" => $request->nombre,
            "direccion" => $request->direccion,
            "descripcion" => $request->descripcion
        ]);

        return response()->json(["message"=>"Registrado correctamente"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $empresa = Empresa::find($id);
        $empresa->estado = !$empresa->estado;
        $empresa->save();

        return response()->json(["Messagge" => $empresa],200);
    }
}
