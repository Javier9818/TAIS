<?php

namespace App\Http\Controllers;

use App\CadenaProveedor;
use App\Proveedor;
use App\Entidad;
use Illuminate\Http\Request;

class ProveedorController extends Controller
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
        $image = substr($request->image, strpos($request->image, ',') + 1);
        $image = base64_decode($image);
        if($request->image != '') $res = Entidad::setImagen($image);

        $empresa = Entidad::create([
            "nombre" => $request->nombre,
            "descripcion" => $request->descripcion,
            "ruc" => $request->ruc,
            "foto" => $res,
            "celular" => $request->celular,
            "email" => $request->email,
            "empresa_id" => $request->empresa_id
        ]);
        
        Proveedor::create([
            "id" => $empresa->id,
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
        $image = substr($request->image, strpos($request->image, ',') + 1);
        $image = base64_decode($image);
        $res = null;
        if($request->foto != null && $request->image != null) $res = Entidad::setImagen($image, $request->foto);
        else if($request->image != null) $res = Entidad::setImagen($image);

        $proveedor = Entidad::find($id)->update([
            "nombre" => $request->nombre,
            "descripcion" => $request->descripcion,
            "ruc" => $request->ruc,
            "foto" => $res,
            "celular" => $request->celular,
            "email" => $request->email
        ]);

        return response()->json(["entidad" => $proveedor], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(CadenaProveedor::where('proveedor_id', $id)->exists())
            return response()->json(["error" => true, "message" => "El proveedor pertenece a una cadena de suministro."]);
        else{
            Proveedor::find($id)->delete();
            return response()->json(["error" => false, "message" => "Proveedor borrado correctamente."]);
        }
    }
}
