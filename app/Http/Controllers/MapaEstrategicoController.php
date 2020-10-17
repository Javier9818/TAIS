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

    public function setPerspectiva(Request $request){
        $exitsObjetivos = Objetivo::where('proceso_id', $request->idProceso)->exists();
        // $exitsObjetivos = false;
        if($exitsObjetivos)
            return response()->json(["error" => true , "message" => "Existen registros de items en el mapa"]);
        else{
            $proceso = Proceso::where('id', $request->idProceso)->first();
            $proceso->perspectivas = $request->data;
            $proceso->save();
        }
        
        return response()->json(["error" => false , "message" => "okey"]);
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

    public function deleteObjetivo($id){
        Objetivo::find($id)->delete();
        return response()->json(["message" => "okey"]);
    }
}
