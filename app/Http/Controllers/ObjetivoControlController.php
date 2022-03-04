<?php

namespace App\Http\Controllers;

use App\Empresa;
use App\Grado_implementacion;
use App\Iniciativas;
use App\Objetivo;
use App\Objetivo_control_empresa;
use App\Version_cobit;
use Illuminate\Http\Request;

class ObjetivoControlController extends Controller
{
    public function index($empresaID){
        $this->authorize('gestionar-panel-empresa', $empresaID);
        $this->authorize('gestionar-clientes');
        $empresa = Empresa::find($empresaID);
        $objetivos_control = Objetivo_control_empresa::where('empresa_fk', $empresaID)
                            ->where('version_fk', session('version', 1))
                            ->with('control')
                            ->get();
        $versiones = Version_cobit::all();
            
        return view('Empresa.cascada_metas.objetivos_control', [
            "empresa" => $empresa, 
            "objetivos_control" => $objetivos_control,
            "versiones" => $versiones
        ]);
    }

    public function getIniciativas($controlEmpresaID){
        $iniciativas = Iniciativas::where('control_empresa_fk', $controlEmpresaID)->orderBy('created_at', 'desc')->get();
        return response()->json(["iniciativas" => $iniciativas]);
    }

    public function getGradosImplementacion($controlEmpresaID){
        $grados = Grado_implementacion::where('control_empresa_fk', $controlEmpresaID)->orderBy('created_at', 'desc')->get();
        return response()->json(["grados" => $grados]);
    }

    public function setIniciativas(Request $request, $controlEmpresaID){
        $iniciativa = Iniciativas::create([
            "nombre" => $request->nombre,
            "descripcion" => $request->descripcion,
            "control_empresa_fk" => $controlEmpresaID
        ]);

        return response()->json(["iniciativa" => $iniciativa]);
    }

    public function setGradosImplementacion(Request $request, $controlEmpresaID){
        $grado = Grado_implementacion::create([
            "nombre" => $request->nombre,
            "descripcion" => $request->descripcion,
            "control_empresa_fk" => $controlEmpresaID
        ]);

        return response()->json(["grado" => $grado]);
    }

    public function updateIniciativas(Request $request){
        $iniciativa = Iniciativas::find($request->id)->update([
            "nombre" => $request->nombre,
            "descripcion" => $request->descripcion
        ]);

        return response()->json(["iniciativa" => $iniciativa]);
    }

    public function updateGradosImplementacion(Request $request){
        $grado = Grado_implementacion::find($request->id)->update([
            "nombre" => $request->nombre,
            "descripcion" => $request->descripcion
        ]);

        return response()->json(["grado" => $grado]);
    }

    public function deleteIniciativas($iniciativaID){
        Iniciativas::find($iniciativaID)->delete();
        return response()->json(["error" => false, "message" => "Se eliminó correctamente."]);
    }

    public function deleteGradosImplementacion($gradoID){
        Grado_implementacion::find($gradoID)->delete();
        return response()->json(["error" => false, "message" => "Se eliminó correctamente."]);
    }
}
