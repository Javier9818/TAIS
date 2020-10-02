<?php

namespace App\Http\Controllers;

use App\Objetivo;
use App\Proceso;
use Illuminate\Http\Request;

class MapaEstrategicoController extends Controller
{
    public function getPerspectivas($idProceso){
        $perspectivas = (Proceso::where('id', $idProceso)->first())->perspectivas;
        return response()->json(["perspectivas" => $perspectivas]);
    }

    public function setObjetivo(Request $request){
        $objetivo = Objetivo::create([
            "proceso_id" => $request->idProceso,
            "descripcion" => $request->data
        ]);

        return response()->json(["objetivo" => $objetivo]);
    }

    public function getObjetivos($idProceso){
        $objetivos = Objetivo::where('proceso_id', $idProceso)->get();
        return response()->json(["objetivos" => $objetivos]);
    }

    public function updateObjetivo(Request $request, $id){
        $objetivo = Objetivo::find($id)->update([
            "descripcion" => $request->data
        ]);

        return response()->json(["objetivo" => $objetivo]);
    }
}
