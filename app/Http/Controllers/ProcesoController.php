<?php

namespace App\Http\Controllers;

use App\MapaProcesoDetalle;
use App\Proceso;
use App\UnidadNegocio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcesoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $proceso = Proceso::create([
            "nombre" => $request->nombre,
            "descripcion" => $request->descripcion,
            "unidad_negocio_id" => $request->unidad,
            "perspectivas" => $request->perspectivas
        ]);

        foreach ($request->megaproceso as $key => $value) {
            DB::insert('insert into megaproceso_procesos(megaproceso_id, proceso_id) values (?, ?)', [$value, $proceso->id]);
        }

        return response()->json(["proceso" => $proceso]);
    }

    public function storeSub(Request $request)
    {
        $proceso = Proceso::create([
            "nombre" => $request->nombre,
            "descripcion" => $request->descripcion,
            "unidad_negocio_id" => $request->unidad,
            "proceso_padre" => $request->proceso,
            "perspectivas" => $request->perspectivas
        ]);

        return response()->json(["proceso" => $proceso]);
    }

    public function getAllSubProcesos($unidad, $proceso_id){
        $subprocesos = Proceso::where('unidad_negocio_id', $unidad)->where('proceso_padre', $proceso_id)
                        ->orderBy('estado', 'desc')->get();
        return response()->json(["subprocesos" => $subprocesos]);
    }

    public function getSubProcesos($unidad, $proceso_id){
        $subprocesos = Proceso::where('unidad_negocio_id', $unidad)->where('proceso_padre', $proceso_id)->where('estado', 1)->get();
        return response()->json(["subprocesos" => $subprocesos]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($unidad)
    {
        $procesos = DB::table('procesos')
        ->selectRaw('procesos.*, megaprocesos.nombre as megaproceso, megaprocesos.id as megaproceso_id')
        ->join('megaproceso_procesos', 'procesos.id', '=', 'megaproceso_procesos.proceso_id')
        ->join('megaprocesos', 'megaproceso_procesos.megaproceso_id', '=', 'megaprocesos.id')
        ->where('unidad_negocio_id', $unidad)
        ->where('proceso_padre', null)
        ->where('estado', true)
        ->orderBy('megaprocesos.id', 'asc')
        ->get();

        return response()->json(["procesos" => $procesos]);
    }

    public function showProcessGraphComplete($unidad)
    {
        $groups = DB::table('procesos')
            ->selectRaw('procesos.*, megaproceso_procesos.megaproceso_id as megaproceso_id')
            ->leftJoin('megaproceso_procesos', 'procesos.id', '=', 'megaproceso_procesos.proceso_id')
            ->whereRaw('unidad_negocio_id = ? and procesos.estado = 1', [$unidad])
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('procesos as p')
                    ->whereRaw('p.proceso_padre = procesos.id');
        })
        ->get();

        $nodos = DB::table('procesos')
            ->selectRaw('procesos.*, megaproceso_procesos.megaproceso_id as megaproceso_id')
            ->leftJoin('megaproceso_procesos', 'procesos.id', '=', 'megaproceso_procesos.proceso_id')
            ->whereRaw('unidad_negocio_id = ? and procesos.estado = 1', [$unidad])
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('procesos as p')
                    ->whereRaw('p.proceso_padre = procesos.id');
        })
        ->get();

        $links = MapaProcesoDetalle::whereRaw('proceso_to > 0')->selectRaw('proceso_from as "from", proceso_to as "to"')->get();
        return response()->json(["groups" => $groups, "nodos" => $nodos, "links" => $links]);
    }

    public function showUnique($unidad, $mega)
    {
        $isMega = $mega < 0 ? true : false;
        $mega = $mega < 0 ? $mega*-1 : $mega;
        
        $query = DB::table('procesos')   //PROCESO * MEGAPROCESO
        ->selectRaw('procesos.id, procesos.nombre, GROUP_CONCAT(megaprocesos.id) as megaprocesos')
        ->join('megaproceso_procesos', 'procesos.id', '=', 'megaproceso_procesos.proceso_id')
        ->join('megaprocesos', 'megaproceso_procesos.megaproceso_id', '=', 'megaprocesos.id')
        ->where('unidad_negocio_id', $unidad)->where('proceso_padre', null)->where('megaprocesos.id', $mega)->where('estado', 1)
        ->groupByRaw('procesos.id, procesos.nombre, megaprocesos.id');

        $procesos = $query->get();
        $procesos_graph = $query->whereNotExists(function ($query) use($mega){
            $query->select(DB::raw(1))
                  ->from('mapa_proceso_detalle')
                  ->join('mapa_proceso', 'mapa_proceso.id', '=', 'mapa_proceso_detalle.mapa_proceso_id')
                  ->whereRaw('mapa_proceso_detalle.proceso_from = procesos.id and mapa_proceso.proceso_maestro = ?', [$mega]);
        })->get();

        $graph = DB::table('mapa_proceso')->selectRaw('mapa_proceso.*')
        ->where('proceso_maestro', $mega)
        ->where('mega', $isMega)
        ->first();

        return response()->json(["procesos" => $procesos, "procesos_graph" => $procesos_graph, "graph" => $graph]);
    }

    public function getProcesosPrio($unidad){
        $procesos = Proceso::where('unidad_negocio_id', $unidad)->where('flag_prio', true)->get();
        return response()->json(["procesos" => $procesos]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $proceso = Proceso::find($id)->update([
            "nombre" => $request->nombre,
            "descripcion" => $request->descripcion
        ]);

        DB::table('megaproceso_procesos')->where('proceso_id', "=", $id) ->delete();

        foreach ($request->megaproceso as $key => $value) {
            DB::insert('insert into megaproceso_procesos(megaproceso_id, proceso_id) values (?, ?)', [$value, $id]);
        }

        return response()->json(["proceso" => $proceso]);
    }

    public function updateSub(Request $request, $id)
    {
        $proceso = Proceso::find($id)->update([
            "nombre" => $request->nombre,
            "descripcion" => $request->descripcion
        ]);
        return response()->json(["proceso" => $proceso]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $unidad)
    {
        $unidad = UnidadNegocio::find($unidad);
        if($unidad->priorizacion !== null)
            return response()->json(["error" => true, "message" => "Los procesos listados, se encuentran registrados en la matriz de priorizacion.", "proceso" =>[]]);


        $proceso = Proceso::find($id);
        $proceso_mapa = MapaProcesoDetalle::where('proceso_from', $id)->exists();
        if($proceso_mapa)
            return response()->json(["error" => true, "message" => "El proceso  '$proceso->nombre' actualmente se encuentra registrado en el constructor de mapa de procesos", "proceso" =>[]]);
       
        else{
            DB::update('UPDATE procesos SET estado = ? WHERE proceso_padre = ?', [!$proceso->estado, $proceso->id]);
            $proceso->estado = !$proceso->estado;
            $proceso->save();
        }

        // if($proceso->flag_prio === 1)
        // return response()->json(["error" => true, "message" => "El proceso  '$proceso->nombre' actualmente se encuentra priorizado", "proceso" =>[]]);
        return response()->json(["error" => false, "message" => "ok", "proceso" =>$proceso]);
    }

    public function destroySub($id, $unidad)
    {
        $proceso = Proceso::find($id);
        $proceso_mapa = MapaProcesoDetalle::where('proceso_from', $id)->exists();
        if($proceso_mapa)
            return response()->json(["error" => true, "message" => "El proceso  '$proceso->nombre' actualmente se encuentra registrado en el constructor de mapa de procesos", "proceso" =>[]]);
       
        else{
            DB::update('UPDATE procesos SET estado = ? WHERE proceso_padre = ?', [!$proceso->estado, $proceso->id]);
            $proceso->estado = !$proceso->estado;
            $proceso->save();
        }

        // if($proceso->flag_prio === 1)
        // return response()->json(["error" => true, "message" => "El proceso  '$proceso->nombre' actualmente se encuentra priorizado", "proceso" =>[]]);
        return response()->json(["error" => false, "message" => "ok", "proceso" =>$proceso]);
    }
}
