<?php

namespace App\Http\Controllers;

use App\Auditoria;
use App\Empresa;
use App\Objetivo_estrategico;
use App\Perspectiva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObjetivoEstrategicoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $this->authorize('gestionar-panel-empresa', $id);
        $this->authorize('gestionar-clientes');
        $empresa = Empresa::find($id);
        $objetivos = Objetivo_estrategico::where('empresa_fk', $empresa->id)->where('version_fk',1)->get();

        $perspectivas = Perspectiva::all();
        return view('empresa.objetivos_estrategicos', ["empresa" => $empresa, "objetivos" => $objetivos, "perspectivas" => $perspectivas]);
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
       $objetivo = Objetivo_estrategico::create([
           "nombre" => $request->nombre,
           "descripcion" => $request->descripcion,
           "perspectiva_fk" => $request->perspectiva_fk,
           "version_fk" => 1,
           "empresa_fk" => $request->empresaID
       ]);

       Auditoria::create([
        "tabla" => "objetivos_estrategicos",
        "accion" => "Registro",
        "terminal" => "127.0.0.1",
        "navegador" => "Chrome 87",
        "user_fk" => $request->userID ?? 1
        ]);

       return response()->json(["objetivo" => $objetivo]);
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
        $objetivo = Objetivo_estrategico::find($id);
        $objetivo->update([
            "nombre" => $request->nombre,
           "descripcion" => $request->descripcion,
           "perspectiva_fk" => $request->perspectiva_fk
        ]);

        Auditoria::create([
            "tabla" => "objetivos_estrategicos",
            "accion" => "Edición",
            "terminal" => "127.0.0.1",
            "navegador" => "Chrome 87",
            "user_fk" => $request->userID ?? 1
        ]);

        return response()->json(["objetivo" => $objetivo]);
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

    public function cambiarPrioridad(Request $request){
        $objetivo = Objetivo_estrategico::find($request->objetivoID);
        $objetivo->update([
            "IS_PRIO" => !$objetivo->IS_PRIO
        ]);

        return response()->json([
            "message" => "Se cambio el estado del objetivo."
        ]);
    }
}
