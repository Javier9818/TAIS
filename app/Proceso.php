<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Proceso extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'unidad_negocio_id', 'proceso_padre', 'flag_prio', 'estado'];
    protected $table = 'procesos';



    public function getSubProcesosPriorizados($unidad){
        $sub_procesos_prio = DB::table('procesos as p')->whereRaw('p.unidad_negocio_id = ? and p.proceso_padre > 0', [$unidad])
        ->whereExists( function ($query){
            $query->select(DB::raw(1))
            ->from('procesos as p2')
            ->whereRaw('p2.id = p.proceso_padre and p2.flag_prio = 1');
        })->get();

        return $sub_procesos_prio;
    }

    public function getProcesosNoPadresPriorizados($unidad){
        $procesos_prio = DB::table('procesos as p')->whereRaw('p.unidad_negocio_id = ?  and p.flag_prio = 1', [$unidad])
            ->whereNotExists( function ($query){
                $query->select(DB::raw(1))
                ->from('procesos as p2')
                ->whereRaw('p2.proceso_padre = p.id');
            })->get();

        return $procesos_prio;
    }

    public function verifyDocuments($unidad, $type){
        $sub_procesos_prio = $this->getSubProcesosPriorizados($unidad);
        $res = $this->verifyDocument($sub_procesos_prio, $type);
        if($res !== true) return $res;
        else if($res == true){
            $procesos_prio = $this->getProcesosNoPadresPriorizados($unidad);
            $res_two = $this->verifyDocument($procesos_prio, $type);
            if($res_two !== true)  return $res_two;
        }
        return true;
    }

    public function verifyDocument($procesos, $type){
        foreach ($procesos as $key => $value) {
            $documento = DB::table('documento as d')->whereRaw('d.type = ? and d.proceso_id = ? and d.version_id IS NULL', [$type, $value->id])->exists();
            if(!$documento) 
                return $value;
         }
         return true;
    }

    public function verifySeguimientos($unidad){
        $sub_procesos_prio = $this->getSubProcesosPriorizados($unidad);
        $res = $this->verifySeguimiento($sub_procesos_prio);
        if($res !== true) return $res;
        else if($res == true){
            $procesos_prio = $this->getProcesosNoPadresPriorizados($unidad);
            $res_two = $this->verifySeguimiento($procesos_prio);
            if($res_two !== true)  return $res_two;
        }
        return true;
    }

    public function verifySeguimiento($procesos){
        foreach ($procesos as $key => $value) {
            $seguimiento = DB::table('seguimiento as s')->whereRaw('s.proceso_id = ? and s.version_id IS NULL', [$value->id])->exists();
            if(!$seguimiento) 
                return $value;
         }
         return true;
    }

    public function verifyPriorizacion($unidad){
        $res = DB::table('unidad_negocio')->whereRaw('id = ? and priorizacion IS NOT NULL', [$unidad])->exists();
        return $res;
    }

    public function newVersion($request){
        $unidad_negocio = UnidadNegocio::find($request->unidad);
        
        $version = Version::create([
            "unidad_negocio_id" => $request->unidad,
            "mapa_proceso" => $request->mapaProceso,
            "descripcion" => $request->version,
            "priorizacion" => $unidad_negocio->priorizacion
        ]);

        DB::update('UPDATE seguimiento SET version_id = ? WHERE version_id IS NULL', [$version->id]);
        DB::update('UPDATE documento SET version_id = ? WHERE version_id IS NULL', [$version->id]);
        DB::update('UPDATE procesos SET flag_prio = ? WHERE flag_prio = 1 AND unidad_negocio_id = ? ', [0, $request->unidad]);
        DB::update('UPDATE mapa_proceso SET objeto = null WHERE  unidad_negocio_id = ? ', [$request->unidad]);
        
        
        $unidad_negocio->priorizacion = null;
        $unidad_negocio->save();
    }
}
