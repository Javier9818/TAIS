<?php

namespace App\Http\Controllers;

use App\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function setCaracterizacion(Request $request){
        if($request->hasFile('file')){
            if($request->file_name != null)
                    Storage::disk('public')->delete("caracterizaciones/$request->file_name");
        
            $name=Str::random(20).'.'.$request->file('file')->extension();       
            $request->file('file')->storeAs('public/caracterizaciones/', $name);
            
            $documento = Documento::updateOrCreate([
                "version_id" => null,
                "proceso_id" => $request->proceso_id,
                "type" => 0
            ],[
                "descripcion" => $request->descripcion,
                "nombre" => $name
            ]);
            
            return response()->json(["documento" => $documento]);
        
        }else return response()->json([], 500);
            
    }

    public function getCaracterizacion($proceso_id){
        $caracterizaciones = DB::table('documento')
                            ->leftJoin('procesos', 'documento.proceso_id', '=', 'procesos.id')
                            ->leftJoin('version', 'documento.version_id', '=', 'version.id')
                            ->selectRaw('documento.*, documento.nombre as file_name, procesos.nombre as proceso, version.descripcion as version')
                            ->where('procesos.id', '=', $proceso_id)
                            ->where('documento.type', '=', 0)
                            ->get();
        
        return response()->json(["caracterizaciones" => $caracterizaciones]);
    }

    public function deleteCaracterizacion($id){
        $documento = Documento::find($id);
        
        if($documento->nombre != null)
            Storage::disk('public')->delete("caracterizaciones/$documento->nombre");
        
        $documento->delete();
        
        return response()->json(["message" => "Eliminación exitosa"]);
    }

    public function getDiagramaFlujo($proceso_id){
        $diagramas = DB::table('documento')
                            ->leftJoin('procesos', 'documento.proceso_id', '=', 'procesos.id')
                            ->leftJoin('version', 'documento.version_id', '=', 'version.id')
                            ->selectRaw('documento.*, documento.nombre as file_name, procesos.nombre as proceso, version.descripcion as version')
                            ->where('procesos.id', '=', $proceso_id)
                            ->where('documento.type', '=', 1)
                            ->get();
        
        return response()->json(["diagramas" => $diagramas]);
    }

    public function setDiagramaFlujo(Request $request){
        if($request->hasFile('file')){
            if($request->file_name != null)
                    Storage::disk('public')->delete("diagrama-flujos/$request->file_name");
        
            $name=Str::random(20).'.'.$request->file('file')->extension();       
            $request->file('file')->storeAs('public/diagrama-flujos/', $name);
            
            $documento = Documento::updateOrCreate([
                "version_id" => null,
                "proceso_id" => $request->proceso_id,
                "type" => 1,
                "flag_red" => $request->flag_red == 'accepted' ? 1:0
            ],[
                "descripcion" => $request->descripcion,
                "nombre" => $name
            ]);
            
            return response()->json(["documento" => $documento]);
        
        }else return response()->json([], 500);
            
    }
}
