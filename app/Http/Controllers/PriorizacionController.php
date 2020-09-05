<?php

namespace App\Http\Controllers;

use App\Criterio;
use App\Empresa;
use App\Escala;
use App\Proceso;
use App\UnidadNegocio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriorizacionController extends Controller
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
        
        
        
        
        return view('empresa.procesos.priorizacion', ["empresa" => $empresa,"unidades" => $unidades_negocio ]);
    }

    public function storeCriterio(Request $request, $unidad){
        $criterio = Criterio::create([
            "nombre" => $request->nombre,
            "descripcion" => $request->descripcion,
            "peso" => $request->peso,
            "unidad_negocio_id" => $unidad
        ]);

        Escala::create(["descripcion" => "Muy bueno","puntaje" => "5","criterio_id" => $criterio->id]);
        Escala::create(["descripcion" => "Bueno","puntaje" => "4","criterio_id" => $criterio->id]);
        Escala::create(["descripcion" => "Regular","puntaje" => "3","criterio_id" => $criterio->id]);
        Escala::create(["descripcion" => "Malo","puntaje" => "2","criterio_id" => $criterio->id]);
        Escala::create(["descripcion" => "Muy malo","puntaje" => "1","criterio_id" => $criterio->id]);
        
        return response()->json(["criterio" => $criterio]);
    }

    public function updateCriterio(Request $request){
        $criterio = Criterio::find($request->id)->update([
            "nombre" => $request->nombre,
            "descripcion" => $request->descripcion,
            "peso" => $request->peso
        ]);

        return response()->json(["criterio" => $criterio]);
    }

    public function showCriterios($unidad){
        $criterios = Criterio::where('unidad_negocio_id', $unidad)->get();
        return response()->json(["criterios" => $criterios]);
    }

    public function showEscalas($criterio){
        $escalas = Escala::where('criterio_id', $criterio)->get();
        return response()->json(["escalas" => $escalas]);
    }

    public function storeEscala(Request $request){
       $escala = Escala::where('pepuntajeso',$request->puntaje)->where('criterio_id', $request->criterio)->get();
       if(count($escala)>0) return response()->json([],500);
        $escala = Escala::create([
            "descripcion" => $request->descripcion,
            "puntaje" => $request->puntaje,
            "criterio_id" => $request->criterio
        ]);
        
        return response()->json(["escala" => $escala]);
    }

    public function updateEscala(Request $request){
        $escala = Escala::find($request->id);
        if($escala->puntaje != $request->puntaje){
            $aux = Escala::where('puntaje',$request->puntaje)->where('criterio_id', $request->criterio_id)->get();
            if(count($aux)>0) return response()->json([],500);
        }
        $escala->update([
            "descripcion" => $request->descripcion,
            "puntaje" => $request->puntaje
        ]);

        return response()->json(["escala" => $escala]);
    }
    
    public function getCriteriosForMatriz($unidad){
        $procesos = Proceso::where('unidad_negocio_id', $unidad)->where('proceso_padre', null)->select(['id as  idprocess','nombre as name'])->get();
        $criterios = DB::table('criterios')
                    ->join('escalas', 'escalas.criterio_id', '=', 'criterios.id')
                    ->selectRaw("criterios.id as idcriterio, criterios.peso as weight, criterios.nombre as name, GROUP_CONCAT(CONCAT(escalas.id, '-',escalas.descripcion,'-',escalas.puntaje)) as escala")
                    ->groupByRaw('criterios.id, criterios.peso, criterios.nombre')
                    ->where('criterios.unidad_negocio_id','=', $unidad)
                    ->get();
        $matriz = UnidadNegocio::find($unidad)->priorizacion;
        
        return response()->json(["criterios" => $criterios, "procesos" => $procesos, "matriz" => $matriz ]);
    }

    public function setMatriz(Request $request){
        $unidad = UnidadNegocio::find($request->unidad);
        $unidad->priorizacion = $request->matriz;
        $unidad->save();

        DB::update('UPDATE procesos SET flag_prio = ? WHERE unidad_negocio_id = ?', [false, $request->unidad]);

        foreach ($request->procesos as $key => $value) {
            Proceso::find($value['idprocess'])->update([
                "flag_prio" => true
            ]);
        }

        return response()->json(["response" => "Registro exitoso"]);
    }
}
