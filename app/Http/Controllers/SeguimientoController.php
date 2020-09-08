<?php

namespace App\Http\Controllers;

use App\Empresa;
use App\Rol;
use App\Seguimiento;
use App\UnidadNegocio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeguimientoController extends Controller
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
        
        
        
        
        return view('empresa.procesos.diagrama_seguimiento', ["empresa" => $empresa,"unidades" => $unidades_negocio ]);
    }

    public function getSeguimiento($proceso, $version){
      
        $query = DB::table('seguimiento')->join('rol', 'rol.id', '=', 'seguimiento.rol_id')
                    ->selectRaw('seguimiento.*, rol.descripcion as rol_new, rol.id as rol')
                    ->where('seguimiento.proceso_id', $proceso);
        
        if($version > 0)
            $seguimiento = $query->whereRaw('seguimiento.version_id = ?', [$version])->get();
        else
            $seguimiento = $query->whereRaw('seguimiento.version_id IS NULL')->get();
                    

        return response()->json(["seguimiento" => $seguimiento]);
    }

    public function getRoles($unidad){
        $roles = DB::table('rol')->where('unidad_negocio_id', $unidad)->get();
        return response()->json(["roles" => $roles]);
    }

    public function setSeguimiento(Request $request){
        $rol = Rol::firstOrCreate([
            "id" => $request->rol
        ],[
            "unidad_negocio_id" => $request->unidad,
            "descripcion" => $request->rol_new
        ]);
        
        $seguimiento = Seguimiento::updateOrCreate([
            "id" => $request->id
        ],[
            "actividad" => $request->actividad,
            "flujo" => $request->flujo,
            "tiempo" => $request->tiempo,
            "proceso_id" => $request->proceso,
            "rol_id" => $rol->id
        ]);

        $seguimiento = DB::table('seguimiento')->join('rol', 'rol.id', '=', 'seguimiento.rol_id')
                        ->where('seguimiento.id', '=',$seguimiento->id)
                        ->selectRaw('seguimiento.*, rol.descripcion as rol_new, rol.id as rol')
                        ->first();
                        
        return response()->json(["seguimiento" => $seguimiento]);
    }

    public function deleteSeguimiento($id){
        Seguimiento::find($id)->delete();
        return response()->json(["message" => "Correcto"]);
    }

}
