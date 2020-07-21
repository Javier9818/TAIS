<?php

namespace App\Http\Controllers;

use App\CadenaCliente;
use App\NivelCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Ui\Presets\React;

class CadenaSuministrosController extends Controller
{
    public function clientesLibres($unidad_negocio, $comodin){
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];
        $clientes = DB::table('clientes')
                    ->join('entidad', 'clientes.entidad_id', '=', 'entidad.id')
                    ->whereNotExists(function($query) use($cadena){
                        $query->select(DB::raw(1))
                        ->from('cadena_clientes')
                        ->whereRaw('cadena_clientes.cliente_id = clientes.id and cadena_clientes.cadena_suministro_id = ?', [$cadena->id]);  //and cadena_clientes.cliente_id != comodin
                    })
                    ->selectRaw('clientes.*, entidad.nombre')
                    ->get();
        
        return response()->json(["clientes" => $clientes], 200);
    }

    public function nivelesClientes($unidad_negocio){
        $niveles = DB::table('cadena_suministros')
                    ->join('nivel_clientes', 'nivel_clientes.cadena_suministro_id', '=', 'cadena_suministros.id')
                    ->select('nivel_clientes.*')
                    ->whereRaw('cadena_suministros.unidad_negocio_id = ?', [$unidad_negocio])
                    ->get();
        
        return response()->json(["niveles" => $niveles], 200);
    }

    public function addNivelesClientes($unidad_negocio){
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];
        $niveles = count(NivelCliente::all()->where('cadena_suministro_id', '=', $cadena->id));
        $nivel = NivelCliente::create([
            "cadena_suministro_id" => $cadena->id,
            "numero" => $niveles + 1
        ]);

        return response()->json(["nivel" => $nivel], 200);
    }

    public function listarClientesPadre($unidad_negocio, $nivel){
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];
        $clientes = DB::table('clientes')
        ->join('entidad', 'clientes.entidad_id', '=', 'entidad.id')
        ->whereExists(function($query) use($cadena, $nivel){
            $query->select(DB::raw(1))
            ->from('cadena_clientes')
            ->whereRaw('cadena_clientes.cliente_id = clientes.id and cadena_clientes.cadena_suministro_id = ? and cadena_clientes.nivel = ?', [$cadena->id, $nivel]);  //and cadena_clientes.cliente_id != comodin
        })
        ->selectRaw('clientes.*, entidad.nombre')
        ->get();

        return response()->json(["clientes" => $clientes], 200);
    }

    public function agregaCliente($unidad_negocio, Request $request){
        $cliente_padre = $request->cliente_padre === 'd' ? null : $request->cliente_padre;
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];
        $cliente = CadenaCliente::create([
            "cadena_suministro_id" => $cadena->id,
            "cliente_id" => $request->cliente,
            "cliente_padre" => $cliente_padre,
            "nivel" => $request->nivel
        ]);

        return response()->json(["cliente" => $cliente], 200);
    }

    public function listarClientes($unidad_negocio){
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];
        $clientes = DB::table('cadena_clientes', 'cc')
                    ->join('clientes', 'cc.cliente_id', '=', 'clientes.id')
                    ->join('entidad', 'entidad.id', '=', 'clientes.entidad_id')
                    ->selectRaw('entidad.nombre as cliente, clientes.id as clienteId, cc.nivel as nivel, cc.cliente_padre')
                    ->whereRaw('cc.cadena_suministro_id = ?', [$cadena->id])
                    ->get();
        
        
        return response()->json(["clientes" => $clientes], 200);

    }
}
