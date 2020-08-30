<?php

namespace App\Http\Controllers;

use App\Empresa;
use App\UnidadNegocio;
use Illuminate\Http\Request;

class MapaProcesosController extends Controller
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
}
