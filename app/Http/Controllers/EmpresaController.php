<?php

namespace App\Http\Controllers;

use App\Empresa;
use Illuminate\Http\Request;

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
        $clientes = $empresa->clientes;
        return view('empresa.clientes', ["empresa" => $empresa, "clientes" => $clientes]);
    }

    public function showProveedores($id)
    {
        $empresa = Empresa::find($id);
        $proveedores = $empresa->proveedores;
        return view('empresa.proveedores', ["empresa" => $empresa, "proveedores" => $proveedores]);
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
        //
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
