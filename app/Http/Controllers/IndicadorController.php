<?php

namespace App\Http\Controllers;

use App\Empresa;
use App\Indicador;
use App\UnidadNegocio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndicadorController extends Controller
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
    
        return view('Empresa.procesos.indicadores', ["empresa" => $empresa,"unidades" => $unidades_negocio ]);
    }

    public function store(Request $request){
        $indicador = Indicador::create([
            "proceso_id" => $request->idProceso,
            "descripcion" => $request->data
        ]);

        return response()->json(["indicador" => $indicador]);
    }

    public function update(Request $request, $id){
        $indicador = Indicador::find($id)->update([
            "descripcion" => $request->data
        ]);;

        return response()->json(["indicador" => $indicador]);
    }

    public function showData($idProceso){
        $indicadores = Indicador::where('proceso_id', $idProceso)->get();
        return response()->json(["indicadores" => $indicadores]);
    }
}
