<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Entidad;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $empresa = Entidad::create([
            "nombre" => $request->nombre,
            "descripcion" => $request->descripcion,
            "ruc" => $request->ruc,
            "celular" => $request->celular,
            "email" => $request->email,
            "empresa_id" => $request->empresa_id
        ]);
        
        Cliente::create([
            "entidad_id" => $empresa->id
        ]);

        return response()->json(["entidad" => $empresa], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $cliente = Entidad::find($id)->update([
            "nombre" => $request->nombre,
            "descripcion" => $request->descripcion,
            "ruc" => $request->ruc,
            "celular" => $request->celular,
            "email" => $request->email
        ]);
        return response()->json(["entidad" => $cliente], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
