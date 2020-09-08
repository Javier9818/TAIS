<?php

namespace App\Http\Controllers;

use App\Empresa;
use App\MapaProceso;
use App\MapaProcesoDetalle;
use App\Proceso;
use App\UnidadNegocio;
use App\Version;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapaProcesoController extends Controller
{
    public function show($empresa_id){
        $empresa = Empresa::find($empresa_id);
        $unidades_negocio = UnidadNegocio::where('estado', true)->where('empresa_id', $empresa_id)->get();

        if(count($unidades_negocio)>0){
            if(session('unidad') == '')
                session(['unidad' => $unidades_negocio[0]->id]);
        }
        else
            return redirect('/empresa/'.$empresa_id.'/unidades-negocio')->withErrors(['unidad-error' => 'No se encontraron unidades de negocio habilitadas']);
        
        
        
        
        return view('empresa.procesos.mapaProcesos', ["empresa" => $empresa,"unidades" => $unidades_negocio ]);
    }
    
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

    public function getSubProcesosGraph($unidad, $proceso_id){
        $subprocesos = DB::table('procesos')
        ->selectRaw('procesos.*')
        ->where('unidad_negocio_id', $unidad)->where('proceso_padre', $proceso_id)->where('estado', 1)
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('mapa_proceso_detalle')
                  ->whereRaw('mapa_proceso_detalle.proceso_from = procesos.id');
        })->get();

        
        $graph = DB::table('mapa_proceso')->selectRaw('mapa_proceso.*')
        ->where('proceso_maestro', $proceso_id)
        ->where('mega', false)
        ->first();

        return response()->json(["subprocesos" => $subprocesos, "graph" => $graph]);
    }

    public function setVersion(Request $request){
        $proceso = new Proceso();

        if(!$proceso->verifyPriorizacion($request->unidad))//Return true if is correct
            return response()->json(["error" => true, "message"=>"No se han priorizado los procesos actuales"]);
        
        $verifyCaract = $proceso->verifyDocuments($request->unidad, 0);  //Return true if is correct, or object if exists error
        if($verifyCaract !== true)
            return response()->json(["error" => true, "message"=>"No se ha registrado la caracterización del proceso '$verifyCaract->nombre'"]);

        
        $verifyFlujo =  $proceso->verifyDocuments($request->unidad, 1);
        if($verifyFlujo !== true)
            return response()->json(["error" => true, "message"=>"No se ha registrado el diagrama de flujo del proceso '$verifyFlujo->nombre'"]);

        $verifySeguimientos =  $proceso->verifySeguimientos($request->unidad);
        if($verifySeguimientos !== true)
        return response()->json(["error" => true, "message"=>"No se ha registrado el seguimiento del proceso '$verifySeguimientos->nombre'"]);



        $proceso->newVersion($request);
        return response()->json(["error" => false, "message" => "Registro exitoso"]);
        
    }

    public function getVersion($unidad){
        $versiones = Version::where('unidad_negocio_id', $unidad)->get();
        return response()->json(["versiones" => $versiones]);
    }
}
