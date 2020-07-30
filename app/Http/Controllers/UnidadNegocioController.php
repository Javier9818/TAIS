<?php

namespace App\Http\Controllers;

use App\CadenaSuministro;
use App\NivelCliente;
use App\NivelProveedor;
use App\UnidadNegocio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnidadNegocioController extends Controller
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
        $unidad = UnidadNegocio::create([
            "producto" => $request->producto,
            "descripcion" => $request->descripcion,
            "estado" => true,
            "empresa_id" => $request->empresa_id
        ]);

        $cadena = CadenaSuministro::create([
            "descripcion" => $request->producto,
            "unidad_negocio_id" => $unidad->id
        ]);

        NivelCliente::create([
            "cadena_suministro_id" => $cadena->id,
            "numero" => 1
        ]);

        NivelProveedor::create([
            "cadena_suministro_id" => $cadena->id,
            "numero" => 1
        ]);

        return response()->json(["unidad" => $unidad], 200);
        
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
        $unidad = UnidadNegocio::find($id)->update([
            "producto" => $request->producto,
            "descripcion" => $request->descripcion
        ]);
        return response()->json(["unidad" => $unidad], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $unidad = UnidadNegocio::find($id);
        $unidad->estado = !$unidad->estado;
        $unidad ->save();

        return response()->json(["message" => "Unidad desactivada correctamente."]);
    }
}
