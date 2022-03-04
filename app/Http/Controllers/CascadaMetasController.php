<?php

namespace App\Http\Controllers;

use App\Alineamiento_control;
use App\Auditoria;
use App\Empresa;
use App\Empresarial_alineamiento;
use App\Meta_alineamiento;
use App\Meta_alineamiento_empresa;
use App\Meta_empresarial;
use App\Meta_empresarial_empresa;
use App\Objetivo_control;
use App\Objetivo_control_empresa;
use App\Objetivo_estrategico;
use App\Version_cobit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPSTORM_META\map;

class CascadaMetasController extends Controller
{
    public function correrCascada(Request $request){
        $objetivos_estrategicos = Objetivo_estrategico::where('IS_PRIO', 1)->where('version_fk', 1)->get();
        $metas_empresariales_empresa = Meta_empresarial_empresa::where('meta_empresarial_empresa.version_fk', 1)
                                        ->selectRaw('meta_empresarial_empresa.*')
                                        ->join('objetivos_estrategicos', 'objetivos_estrategicos.id', '=', 'meta_empresarial_empresa.objetivo_fk')
                                        ->where('objetivos_estrategicos.IS_PRIO', 1)->get();
        Auditoria::create([
            "tabla" => "objetivo_control_empresa",
            "accion" => "Registro",
            "terminal" => "127.0.0.1",
            "navegador" => "Chrome 87",
            "user_fk" => $request->userID ?? 1
        ]);
        Auditoria::create([
            "tabla" => "version",
            "accion" => "Registro",
            "terminal" => "127.0.0.1",
            "navegador" => "Chrome 87",
            "user_fk" => $request->userID ?? 1
        ]);
        
        if($this->verificaRelacionMetasObjetivos($objetivos_estrategicos, $this->toArray($metas_empresariales_empresa->pluck('objetivo_fk')))){
            if($this->cascadaOn($metas_empresariales_empresa, $request->descripcion, $request->empresa) )   
                return response()->json([ "error" => false, "message" => "OK."  ]);
            else
                return response()->json([ "error" => true, "message" => "Ha ocurrido un error, inténtelo nuevamente."  ]);
        }else{
            return response()->json([ "error" => true, "message" => "Completar alineamiento de objetivos estratégicos con metas empresariales."  ]);
        }
    }

    private function verificaRelacionMetasObjetivos($objetivos_estrategicos, $metas_empresariales_empresa){
        $res = true;
        foreach ($objetivos_estrategicos as $objetivo ) {
           if( !in_array($objetivo->id, $metas_empresariales_empresa)){
                $res = false;
                break;
           }
        }
        return $res;
    }

    private function toArray($array){
        $res = [];
        for ($i= 0; $i < count($array) ; $i++) { 
            array_push($res, $array[$i]);
        }
        return $res;
    }

    private function cascada_alineamiento($metas_empresariales_empresa){
        $id_metas_empresariales = $this->toArray($metas_empresariales_empresa->pluck('meta_empresarial_fk'));
        
        $alineamiento_puntaje = Empresarial_alineamiento::whereIn('meta_fk', $id_metas_empresariales)
                                ->selectRaw('alineamiento_fk, SUM(peso) as peso_total')
                                ->where('peso', 2) // SOLO CONSIDERAMOS PRINCIPALES
                                ->groupBy('alineamiento_fk')
                                ->get();
        $alineamiento_resultante = [];
        $promedio = $alineamiento_puntaje->sum('peso_total') / count($alineamiento_puntaje) ;
        foreach ($alineamiento_puntaje as $alineamiento) {
            if($alineamiento->peso_total >= $promedio)
                array_push($alineamiento_resultante, $alineamiento);
        }
        
        return $alineamiento_resultante;
    }

    private function cascada_objetivos_control($metas_alineamiento){
        $id_metas_alineamiento =  array_map( function( $alineamiento ){
            return $alineamiento->alineamiento_fk;
        },$metas_alineamiento);
        
        $objetivos_control_puntaje = Alineamiento_control::whereIn('alineamiento_fk', $id_metas_alineamiento)
                                ->selectRaw('control_fk, SUM(peso) as peso_total')
                                ->where('peso', 2) // SOLO CONSIDERAMOS PRINCIPALES
                                ->groupBy('control_fk')
                                ->get();
        $objetivos_control_resultante = [];
        $promedio = $objetivos_control_puntaje->sum('peso_total') / count($objetivos_control_puntaje) ;
        foreach ($objetivos_control_puntaje as $objetivo_control) {
            if($objetivo_control->peso_total >= $promedio)
                array_push($objetivos_control_resultante, $objetivo_control);
        }
        
        return $objetivos_control_resultante;
    }

    private function cascadaOn($metas_empresariales_empresa, $descripcion, $empresa){
        try {
            DB::beginTransaction(); 
            $version = Version_cobit::create([ "descripcion" => $descripcion]);
            $alineamiento_resultante = $this->cascada_alineamiento($metas_empresariales_empresa);
            $objetivos_control_resultante = $this->cascada_objetivos_control($alineamiento_resultante);

            foreach ($objetivos_control_resultante as $objetivo_control ) {
                Objetivo_control_empresa::create([
                    "control_fk" => $objetivo_control->control_fk,
                    "empresa_fk" => $empresa,
                    "version_fk" => $version->id
                ]);  
            }

            foreach ($alineamiento_resultante as $alineamiento ) {
                Meta_alineamiento_empresa::create([
                    "alineamiento_fk" => $alineamiento->alineamiento_fk,
                    "empresa_fk" => $empresa,
                    "version_fk" => $version->id
                ]);
            }

            $objetivos_estrategicos = Objetivo_estrategico::where('IS_PRIO', 1)->where('version_fk', 1)->get();
            
            foreach ($objetivos_estrategicos as $oe ) {
                $objetivo = $oe->replicate();
                $objetivo->version_fk = $version->id;
                $objetivo->save();
            }

            foreach ($metas_empresariales_empresa as $me ) {
                $meta_empresarial_empresa = $me->replicate();
                $meta_empresarial_empresa->version_fk = $version->id;
                $objetivo = Objetivo_estrategico::find($meta_empresarial_empresa->objetivo_fk);
                $objetivo_version = Objetivo_estrategico::where('nombre', $objetivo->nombre)->where('version_fk',  $version->id)->first();
                $meta_empresarial_empresa->objetivo_fk = $objetivo_version->id;
                $meta_empresarial_empresa->save();
            }
                
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }

        return false;
    }

    public function getTransacciones(){
        $transacciones = Auditoria::with('user')->get();
        $acciones = Auditoria::with('user')->select('accion')->groupBy('accion')->get()->pluck('accion');
        return view('admin.transacciones', [ "transacciones" => $transacciones, "acciones" => $acciones]);
    }

    public function getTransaccionesByAccion($accion){
        if($accion == 1)
            $transacciones = Auditoria::with('user')->get();
        else
            $transacciones = Auditoria::with('user')->where('accion', $accion)->get();
        
        return response()->json(["transacciones" => $transacciones]);
    }

    public function indexAlienamientoResultante($idEmpresa){
        $empresa = Empresa::find($idEmpresa);
        $versiones = Version_cobit::all();
        $version = session('version', 1);
        if($version > 1){
            $metas_empresariales_empresa = Meta_empresarial_empresa::where('version_fk', $version)
                                            ->where('empresa_fk', $empresa->id)
                                            ->with('meta')
                                            ->get()
                                            ->pluck('meta_empresarial_fk');

            $matriz = Empresarial_alineamiento::whereIn('meta_fk', $metas_empresariales_empresa)
                                        ->with('meta', 'alineamiento')
                                        ->get();

            $resultante = Meta_alineamiento_empresa::where('version_fk', $version)
                                            ->where('empresa_fk', $empresa->id)
                                            ->with('alineamiento')
                                            ->get(); 
            $alineamiento = Meta_alineamiento::all();

            $metas_resultantes = Meta_empresarial::whereIn('id', $metas_empresariales_empresa)->get();
        }

        return view('Empresa.cascada_metas.alineamiento_matriz', [
            "matriz" => $matriz ?? [],
            "resultante" => $resultante ?? [],
            "versiones" => $versiones ?? [],
            "empresa" => $empresa,
            "alineamiento" => $alineamiento ?? [],
            "metas_resultantes" =>  $metas_resultantes ?? []
        ]);
        
    }

    public function indexControlResultante($idEmpresa){
        $empresa = Empresa::find($idEmpresa);
        $versiones = Version_cobit::all();
        $version = session('version', 1);
        if($version > 1){
            $metas_alineamiento_empresa = Meta_alineamiento_empresa::where('version_fk', $version)
                                            ->where('empresa_fk', $empresa->id)
                                            ->with('alineamiento')
                                            ->get()
                                            ->pluck('alineamiento_fk');

            $matriz = Alineamiento_control::whereIn('alineamiento_fk', $metas_alineamiento_empresa)
                                        ->with('control', 'alineamiento')
                                        ->get();

            $resultante = Objetivo_control_empresa::where('version_fk', $version)
                                            ->with('control')
                                            ->where('empresa_fk', $empresa->id)
                                            ->get(); 
            $control = Objetivo_control::all();

            $metas_resultantes = Meta_alineamiento::whereIn('id', $metas_alineamiento_empresa)->get();
        }

        return view('Empresa.cascada_metas.control_matriz', [
            "matriz" => $matriz ?? [],
            "resultante" => $resultante ?? [],
            "versiones" => $versiones ?? [],
            "empresa" => $empresa,
            "control" => $control ?? [],
            "metas_resultantes" =>  $metas_resultantes ?? []
        ]);
        
    }
}
