<?php

namespace App\Http\Controllers;

use App\CadenaCliente;
use App\CadenaProveedor;
use App\NivelCliente;
use App\NivelProveedor;
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

    public function updateCliente(Request $request){

        //ANTES QUE NADA EVALUAR SI EL CLIENTE_PADRE ELEGIDO PERTENECE A SU LINEA ACTUAL EN NIVELES MÁS ALTOS

        $cliente_padre = $request->cliente_padre === 'd' ? null : $request->cliente_padre;
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $request->unidad)->get()[0];

        $cliente = CadenaCliente::where('cadena_suministro_id', $cadena->id)
                    ->where('cliente_id', $request->cliente)
                    ->first();
                    
        $nivel = $request->nivel;
        $cliente->cliente_padre = $cliente_padre;
        
        while($cliente){
            //Este nivel exite? Sino crealo
            if(NivelCliente::where('numero', 1)->where('cadena_suministro_id',  $cadena->id)->doesntExist())
                NivelCliente::create(["numero" => $nivel, "cadena_suministro_id" => $cadena->id]);

            $cliente->nivel = $nivel;
            $cliente->save();
            $cliente = CadenaCliente::where('cadena_suministro_id', $cadena->id)
                    ->where('cliente_padre', $cliente->cliente_id)
                    ->first();
            $nivel ++; 
        }

        return response()->json(["mesagge" => true], 200);
        
    }

    public function deleteCliente($unidad_negocio, $cliente){
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];

        if(CadenaCliente::where('cadena_suministro_id', $cadena->id)->where('cliente_padre', $cliente)->exists())
            return response()->json(["error"=> true, "message" => "El cliente seleccionado tiene otros clientes que dependen de él."]);
        else{
            $cliente = CadenaCliente::where('cadena_suministro_id', $cadena->id)->where('cliente_id', $cliente)->first();
            $cliente->delete();
            return response()->json(["error"=> false, "message" => "Cliente eliminado correctamente."]);
        }
        
    }

    /** PROVEEDORES EN CADENA */

    public function proveedoresLibres($unidad_negocio, $comodin){
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];
        $proveedores = DB::table('proveedores')
                    ->join('entidad', 'proveedores.entidad_id', '=', 'entidad.id')
                    ->whereNotExists(function($query) use($cadena){
                        $query->select(DB::raw(1))
                        ->from('cadena_proveedores')
                        ->whereRaw('cadena_proveedores.proveedor_id = proveedores.id and cadena_proveedores.cadena_suministro_id = ?', [$cadena->id]);  //and cadena_clientes.cliente_id != comodin
                    })
                    ->selectRaw('proveedores.*, entidad.nombre')
                    ->get();
        
        return response()->json(["proveedores" => $proveedores], 200);
    }

    public function nivelesProveedores($unidad_negocio){
        $niveles = DB::table('cadena_suministros')
                    ->join('nivel_proveedores', 'nivel_proveedores.cadena_suministro_id', '=', 'cadena_suministros.id')
                    ->select('nivel_proveedores.*')
                    ->whereRaw('cadena_suministros.unidad_negocio_id = ?', [$unidad_negocio])
                    ->get();
        
        return response()->json(["niveles" => $niveles], 200);
    }

    public function addNivelesProveedores($unidad_negocio){
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];
        $niveles = count(NivelProveedor::all()->where('cadena_suministro_id', '=', $cadena->id));
        $nivel = NivelProveedor::create([
            "cadena_suministro_id" => $cadena->id,
            "numero" => $niveles + 1
        ]);

        return response()->json(["nivel" => $nivel], 200);
    }

    public function listarProvedoresPadre($unidad_negocio, $nivel){
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];
        $proveedores = DB::table('proveedores')
        ->join('entidad', 'proveedores.entidad_id', '=', 'entidad.id')
        ->whereExists(function($query) use($cadena, $nivel){
            $query->select(DB::raw(1))
            ->from('cadena_proveedores')
            ->whereRaw('cadena_proveedores.proveedor_id = proveedores.id and cadena_proveedores.cadena_suministro_id = ? and cadena_proveedores.nivel = ?', [$cadena->id, $nivel]);  //and cadena_clientes.cliente_id != comodin
        })
        ->selectRaw('proveedores.*, entidad.nombre')
        ->get();

        return response()->json(["proveedores" => $proveedores], 200);
    }

    public function agregaProveedor($unidad_negocio, Request $request){
        $proveedor_padre = $request->cliente_padre === 'd' ? null : $request->cliente_padre;
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];
        $proveedor = CadenaProveedor::create([
            "cadena_suministro_id" => $cadena->id,
            "proveedor_id" => $request->cliente,
            "proveedor_padre" => $proveedor_padre,
            "nivel" => $request->nivel
        ]);

        return response()->json(["proveedor" => $proveedor], 200);
    }

    public function deleteProveedor($unidad_negocio, $proveedor){
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];

        if(CadenaProveedor::where('cadena_suministro_id', $cadena->id)->where('proveedor_padre', $proveedor)->exists())
            return response()->json(["error"=> true, "message" => "El proveedor seleccionado tiene otros proveedores que dependen de él."]);
        else{
            $proveedor = CadenaProveedor::where('cadena_suministro_id', $cadena->id)->where('proveedor_id', $proveedor)->first();
            $proveedor->delete();
            return response()->json(["error"=> false, "message" => "Proveedor eliminado correctamente."]);
        }
        
    }

    public function listarProveedores($unidad_negocio){
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];
        $proveedores = DB::table('cadena_proveedores', 'cc')
                    ->join('proveedores', 'cc.proveedor_id', '=', 'proveedores.id')
                    ->join('entidad', 'entidad.id', '=', 'proveedores.entidad_id')
                    ->selectRaw('entidad.nombre as cliente, proveedores.id as clienteId, cc.nivel as nivel, cc.proveedor_padre')
                    ->whereRaw('cc.cadena_suministro_id = ?', [$cadena->id])
                    ->get();
        
        
        return response()->json(["proveedores" => $proveedores], 200);

    }

    public function updateProveedor(Request $request){

        //ANTES QUE NADA EVALUAR SI EL PROVEEDOR_PADRE ELEGIDO PERTENECE A SU LINEA ACTUAL EN NIVELES MÁS ALTOS

        $proveedor_padre = $request->cliente_padre === 'd' ? null : $request->cliente_padre;
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $request->unidad)->get()[0];

        $proveedor = CadenaProveedor::where('cadena_suministro_id', $cadena->id)
                    ->where('proveedor_id', $request->cliente)
                    ->first();
                    
        $nivel = $request->nivel;
        $proveedor->proveedor_padre = $proveedor_padre;
        
        while($proveedor){
            //Este nivel exite? Sino crealo
            if(NivelProveedor::where('numero', 1)->where('cadena_suministro_id',  $cadena->id)->doesntExist())
                NivelProveedor::create(["numero" => $nivel, "cadena_suministro_id" => $cadena->id]);

            $proveedor->nivel = $nivel;
            $proveedor->save();
            $proveedor = CadenaProveedor::where('cadena_suministro_id', $cadena->id)
                    ->where('proveedor_padre', $proveedor->proveedor_id)
                    ->first();
            $nivel ++; 
        }

        return response()->json(["mesagge" => true], 200);
        
    }
}
