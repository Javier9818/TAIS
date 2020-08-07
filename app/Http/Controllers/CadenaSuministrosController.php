<?php

namespace App\Http\Controllers;

use App\CadenaCliente;
use App\CadenaProveedor;
use App\Cliente;
use App\Entidad;
use App\Historial;
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
        ->selectRaw('clientes.id as id , entidad.nombre as nombre')
        ->get();

        return response()->json(["clientes" => $clientes], 200);
    }

    public function listarClientesPadreComodin($unidad_negocio, $nivel){
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];
        $clientes = DB::table('clientes')
        ->join('entidad', 'clientes.entidad_id', '=', 'entidad.id')
        ->whereExists(function($query) use($cadena, $nivel){
            $query->select(DB::raw(1))
            ->from('cadena_clientes')
            ->whereRaw('cadena_clientes.cliente_id = clientes.id and cadena_clientes.cadena_suministro_id = ? and cadena_clientes.nivel < ?', [$cadena->id, $nivel]);  //and cadena_clientes.cliente_id != comodin
        })
        ->selectRaw('clientes.id as id , entidad.nombre as nombre')
        ->get();

        return response()->json(["clientes" => $clientes], 200);
    }

    public function agregaCliente($unidad_negocio, Request $request){
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];

        $padre = '';
        foreach ($request->cliente_padres as $key => $value) {
            $cliente_padre = $value['id'] === 'd' ? null :  $value['id'];
            $padre_aux = $value['id'] === 'd' ? null : 
                DB::table('clientes')->where('clientes.id', $value['id'])
                ->join('entidad', 'entidad.id', '=', 'clientes.entidad_id')
                ->select('entidad.*')
                ->first();
            $padre = $padre_aux ? $padre.' - '.$padre_aux->nombre : null;
            $cliente = CadenaCliente::updateOrCreate([
                "cadena_suministro_id" => $cadena->id,
                "cliente_id" => $request->cliente,
                "cliente_padre" => $cliente_padre,
                "nivel" => $request->nivel
            ]);
        }
        return response()->json(["cliente" => $cliente, "padre" => $padre], 200);
    }

    public function listarClientes($unidad_negocio){
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];
        $clientes = DB::table('cadena_clientes', 'cc')
                    ->join('clientes', 'cc.cliente_id', '=', 'clientes.id')
                    ->join('entidad', 'entidad.id', '=', 'clientes.entidad_id')
                    ->selectRaw('entidad.nombre as cliente, clientes.id as clienteId, cc.nivel as nivel,cc.nivel as clientes_padre,cc.nivel as nombrePadre,
                    GROUP_CONCAT((SELECT IF(cc.cliente_padre is null, 0, (SELECT CONCAT(e.nombre, "-" ,p.id) FROM clientes p JOIN entidad e ON p.entidad_id = e.id WHERE p.id = cc.cliente_padre)))) as padres')
                    ->whereRaw('cc.cadena_suministro_id = ?', [$cadena->id])
                    ->groupByRaw('clientes.id, entidad.nombre, cc.nivel')
                    ->get();
        
        
        return response()->json(["clientes" => $clientes], 200);

    }

    public function updateCliente(Request $request){

        $cliente_padre = $request->cliente_padre === 'd' ? null : $request->cliente_padre;
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $request->unidad)->get()[0];

        if(CadenaCliente::where('cadena_suministro_id', $cadena->id)->where('cliente_padre', $request->cliente)->exists())
            return response()->json(["error"=> true, "message" => "El cliente seleccionado tiene otros clientes que dependen de él."]);
        else{

            CadenaCliente::where('cadena_suministro_id', $cadena->id)->where('cliente_id', $request->cliente)->delete();
            foreach ($request->cliente_padres as $key => $value) {
                $cliente_padre = $value['id'] === 'd' ? null :  $value['id'];
                CadenaCliente::updateOrCreate([
                    "cadena_suministro_id" => $cadena->id,
                    "cliente_id" => $request->cliente,
                    "cliente_padre" => $cliente_padre,
                    "nivel" => $request->nivel
                ]);
            }

            return response()->json(["mesagge" => true], 200);
        }
    }

    public function deleteCliente($unidad_negocio, $cliente){
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];

        if(CadenaCliente::where('cadena_suministro_id', $cadena->id)->where('cliente_padre', $cliente)->exists())
            return response()->json(["error"=> true, "message" => "El cliente seleccionado tiene otros clientes que dependen de él."]);
        else{
            CadenaCliente::where('cadena_suministro_id', $cadena->id)->where('cliente_id', $cliente)->delete();
            return response()->json(["error"=> false, "message" => "Cliente eliminado correctamente."]);
        }
        
    }
    
    public function verifyCliente($unidad_negocio, $cliente){
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];
        $cliente = CadenaCliente::where('cliente_id', $cliente)->where('cadena_suministro_id', $cadena->id)->first();
        if($cliente){
            return response()->json(["exists" => true, "nivel" => $cliente->nivel]);
        }else{
            return response()->json(["exists" => false]);
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
    
    public function listarProvedoresPadreComodin($unidad_negocio, $nivel){
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];
        $proveedores = DB::table('proveedores')
        ->join('entidad', 'proveedores.entidad_id', '=', 'entidad.id')
        ->whereExists(function($query) use($cadena, $nivel){
            $query->select(DB::raw(1))
            ->from('cadena_proveedores')
            ->whereRaw('cadena_proveedores.proveedor_id = proveedores.id and cadena_proveedores.cadena_suministro_id = ? and cadena_proveedores.nivel < ?', [$cadena->id, $nivel]);  //and cadena_clientes.cliente_id != comodin
        })
        ->selectRaw('proveedores.*, entidad.nombre')
        ->get();

        return response()->json(["proveedores" => $proveedores], 200);
    }

    public function agregaProveedor($unidad_negocio, Request $request){//e
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];
        $padre = '';
        foreach ($request->cliente_padres as $key => $value) {
            $proveedor_padre = $value['id'] === 'd' ? null :  $value['id'];
            $padre_aux = $value['id'] === 'd' ? null : 
                DB::table('proveedores')->where('proveedores.id', $value['id'])
                ->join('entidad', 'entidad.id', '=', 'proveedores.entidad_id')
                ->select('entidad.*')
                ->first();
            $padre = $padre_aux ? $padre.' - '.$padre_aux->nombre : null;
            $proveedor = CadenaProveedor::updateOrCreate([
                "cadena_suministro_id" => $cadena->id,
                "proveedor_id" => $request->cliente,
                "proveedor_padre" => $proveedor_padre,
                "nivel" => $request->nivel
            ]);
        }

        return response()->json(["proveedor" => $proveedor, "padre" => $padre], 200);
    }

    public function deleteProveedor($unidad_negocio, $proveedor){ //e
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];

        if(CadenaProveedor::where('cadena_suministro_id', $cadena->id)->where('proveedor_padre', $proveedor)->exists())
            return response()->json(["error"=> true, "message" => "El proveedor seleccionado tiene otros proveedores que dependen de él."]);
        else{
            CadenaProveedor::where('cadena_suministro_id', $cadena->id)->where('proveedor_id',  $proveedor)->delete();
            return response()->json(["error"=> false, "message" => "Proveedor eliminado correctamente."]);
        }
        
    }

    public function listarProveedores($unidad_negocio){  //e
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];
        $proveedores = DB::table('cadena_proveedores', 'cc')
                    ->join('proveedores', 'cc.proveedor_id', '=', 'proveedores.id')
                    ->join('entidad', 'entidad.id', '=', 'proveedores.entidad_id')
                    ->selectRaw('entidad.nombre as cliente, proveedores.id as clienteId, cc.nivel as nivel,cc.nivel as proveedores_padre,cc.nivel as nombrePadre,
                    GROUP_CONCAT((SELECT IF(cc.proveedor_padre is null, 0, (SELECT CONCAT(e.nombre, "-" ,p.id)  FROM proveedores p JOIN entidad e ON p.entidad_id = e.id WHERE p.id = cc.proveedor_padre)) )) as padres')
                    ->whereRaw('cc.cadena_suministro_id = ?', [$cadena->id])
                    ->groupByRaw('proveedores.id, entidad.nombre, cc.nivel')
                    ->get();
        
        
        return response()->json(["proveedores" => $proveedores], 200);

    }

    public function updateProveedor(Request $request){

        //ANTES QUE NADA EVALUAR SI EL PROVEEDOR_PADRE ELEGIDO PERTENECE A SU LINEA ACTUAL EN NIVELES MÁS ALTOS
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $request->unidad)->get()[0];


        $proveedor_padre = $request->cliente_padre === 'd' ? null : $request->cliente_padre;

        if(CadenaProveedor::where('cadena_suministro_id', $cadena->id)->where('proveedor_padre', $request->cliente)->exists())
            return response()->json(["error"=> true, "message" => "El proveedor seleccionado tiene otros proveedores que dependen de él."]);
        else{
            CadenaProveedor::where('cadena_suministro_id', $cadena->id)->where('proveedor_id', $request->cliente)->delete();
            foreach ($request->cliente_padres as $key => $value) {
                $proveedor_padre = $value['id'] === 'd' ? null :  $value['id'];
                CadenaProveedor::updateOrCreate([
                    "cadena_suministro_id" => $cadena->id,
                    "proveedor_id" => $request->cliente,
                    "proveedor_padre" => $proveedor_padre,
                    "nivel" => $request->nivel
                ]);
            }

            return response()->json(["mesagge" => true], 200);

            // $proveedor = CadenaProveedor::where('cadena_suministro_id', $cadena->id)
            //         ->where('proveedor_id', $request->cliente)
            //         ->get();
            
            // if(count($proveedor) === 1){
            //     $proveedor = $proveedor[0];
            //     $proveedor->proveedor_padre = $proveedor_padre;
            //     $proveedor->nivel = $request->nivel;;
            //     $proveedor->save();
            //     return response()->json(["mesagge" => true], 200);
            // }else{
            //     return response()->json(["error"=> true, "message" => "El proveedor seleccionado provee a más de un proveedor."]);
            // }
                    
            
        }

        // $proveedor = CadenaProveedor::where('cadena_suministro_id', $cadena->id)
        //             ->where('proveedor_id', $request->cliente)
        //             ->first();
                    
        // $nivel = $request->nivel;
        // $proveedor->proveedor_padre = $proveedor_padre;
        
        // while($proveedor){
            //     //Este nivel exite? Sino crealo
            //     if(NivelProveedor::where('numero', 1)->where('cadena_suministro_id',  $cadena->id)->doesntExist())
            //         NivelProveedor::create(["numero" => $nivel, "cadena_suministro_id" => $cadena->id]);

            //     $proveedor->nivel = $nivel;
            //     $proveedor->save();
            //     $proveedor = CadenaProveedor::where('cadena_suministro_id', $cadena->id)
            //             ->where('proveedor_padre', $proveedor->proveedor_id)
            //             ->first();
            //     $nivel ++; 
        // }

        // return response()->json(["mesagge" => true], 200);
        
    }

    public function getEntidadesCadena($unidad_negocio){
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];
        $clientes = DB::table('cadena_clientes')
                    ->join('clientes', 'cadena_clientes.cliente_id', '=', 'clientes.id')
                    ->join('entidad', 'entidad.id', '=', 'clientes.entidad_id')
                    ->where('cadena_clientes.cadena_suministro_id', $cadena->id)
                    ->selectRaw('clientes.id as id, entidad.nombre as name, cadena_clientes.cliente_padre as padre, entidad.foto as foto')
                    ->get();

        $proveedores = DB::table('cadena_proveedores')
        ->join('proveedores', 'cadena_proveedores.proveedor_id', '=', 'proveedores.id')
        ->join('entidad', 'entidad.id', '=', 'proveedores.entidad_id')
        ->where('cadena_proveedores.cadena_suministro_id', $cadena->id)
        ->selectRaw('proveedores.id ,  entidad.nombre as name,  cadena_proveedores.proveedor_padre as padre, entidad.foto as foto')
        ->get();
        
        return response()->json(["clientes" => $clientes, "proveedores" => $proveedores]);
    }

    public function verifyProveedor($unidad_negocio, $proveedor){
        $cadena = DB::table('cadena_suministros')->where('unidad_negocio_id', '=', $unidad_negocio)->get()[0];
        $proveedor = CadenaProveedor::where('proveedor_id', $proveedor)->where('cadena_suministro_id', $cadena->id)->first();
        if($proveedor){
            return response()->json(["exists" => true, "nivel" => $proveedor->nivel]);
        }else{
            return response()->json(["exists" => false]);
        }
    }

    /** -------------------------------- HISTORIAL */
    
    public function setHistorial(Request $request){
        $historia = Historial::create([
            "unidad_negocio_id" => $request->unidad_negocio,
            "contenido" => $request->content,
            "comentario" => $request->historia
        ]);

        return response()->json(["historia" => $historia], 200);
    }

    public function getHistorial($unidad){
        $historias = Historial::where('unidad_negocio_id', $unidad)->orderBy('created_at', 'desc') ->get();
        return response()->json(["historias" => $historias], 200);
    }
}
