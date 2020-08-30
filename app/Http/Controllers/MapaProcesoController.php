<?php

namespace App\Http\Controllers;

use App\MapaProceso;
use App\MapaProcesoDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapaProcesoController extends Controller
{
    public function store(Request $request){
       $mapa = MapaProceso::updateOrCreate(
        [
            "unidad_negocio_id" => $request->unidad,
            "proceso_maestro" =>  $request->maestro < 0 ?  $request->maestro*-1:$request->maestro,
            "mega" => $request->maestro < 0 ? true: false
        ],
        ["objeto" => $request->objeto]);

        //$detalle = count($request->links) > 0 ? $request->links : $request->nodes;

        DB::table('mapa_proceso_detalle')->where('mapa_proceso_id', "=", $mapa->id) ->delete();

        foreach ($request->nodes as $key => $value) {
            MapaProcesoDetalle::create([
                "proceso_from" => $value['key'],
                "mapa_proceso_id" => $mapa->id
            ]);
        }

        foreach ($request->links as $key => $value) {
            MapaProcesoDetalle::updateOrCreate([
                "proceso_from" => $value['from'],
                "mapa_proceso_id" => $mapa->id
            ],["proceso_to" => $value['to']]);
        }

        return response()->json(["estado"=>"ok"]);
    }
}
