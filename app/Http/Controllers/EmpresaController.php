<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Empresa;
use App\Proceso;
use App\Proveedor;
use App\UnidadNegocio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Undefined;

class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $empresas = Empresa::orderBy('created_at','desc')->get();
        return view('admin.home', ["empresas" => $empresas]);
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
        if(Empresa::where('ruc', $request->ruc)->exists())
            return response()->json(["error" => true, "message" => "El RUC ingresado ya está siendo utilizado"], 200);
        else
            $empresa = Empresa::create([
                "ruc" => $request->ruc,
                "nombre" => $request->nombre,
                "direccion" => $request->direccion,
                "descripcion" => $request->descripcion
            ]);
        return response()->json(["error" => false, "message" => "ok", "empresa" => $empresa], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('gestionar-panel-empresa', $id);
        $empresa = Empresa::find($id);
        return view('empresa.inicio', ["empresa" => $empresa]);
    }

    public function showClientes($id)
    {
        $this->authorize('gestionar-panel-empresa', $id);
        $this->authorize('gestionar-clientes');
        $empresa = Empresa::find($id);
        $clientes = DB::table('entidad')
            ->join('clientes', 'clientes.entidad_id', '=', 'entidad.id')
            ->where('entidad.empresa_id', '=', $id)
            ->select('entidad.*')
            ->get();
        return view('empresa.clientes', ["empresa" => $empresa, "clientes" => $clientes]);
    }

    public function showProveedores($id)
    {
        $this->authorize('gestionar-panel-empresa', $id);
        $this->authorize('gestionar-proveedores');

        $empresa = Empresa::find($id);
        $proveedores = DB::table('entidad')
            ->join('proveedores', 'proveedores.entidad_id', '=', 'entidad.id')
            ->where('entidad.empresa_id', '=', $id)
            ->select('entidad.*')
            ->get();
        return view('empresa.proveedores', ["empresa" => $empresa, "proveedores" => $proveedores]);
    }

    public function showUnidadesNegocio($id){
        $this->authorize('gestionar-panel-empresa', $id);
        $this->authorize('gestionar-unidades-negocio');
        $empresa = Empresa::find($id);
        $unidades_negocio = DB::table('unidad_negocio')
            ->where('unidad_negocio.empresa_id', '=', $id)
            ->get();
        return view('empresa.unidades_negocio', ["empresa" => $empresa, "unidades_negocio" => $unidades_negocio]);
    }

    public function showAdministrarCadena($id){
        $this->authorize('gestionar-panel-empresa', $id);
        $this->authorize('gestionar-cadena');
        $empresa = Empresa::find($id);
        $unidades_negocio = UnidadNegocio::where('estado', true)->get();
        // $proveedores = DB::table('proveedores')->join('entidad', 'proveedores.entidad_id', '=', 'entidad.id')->get();
        // $clientes =  DB::table('clientes')->join('entidad', 'clientes.entidad_id', '=', 'entidad.id')->get();
        return view('empresa.administrar_cadena', ["empresa" => $empresa, "unidades_negocio" => $unidades_negocio]);
    }

    public function showGenerarCadena($id){
        $this->authorize('gestionar-panel-empresa', $id);
        $this->authorize('gestionar-cadena');
        $empresa = Empresa::find($id);
        $unidades_negocio = UnidadNegocio::where('estado', true)->get();
        return view('empresa.generar', ["empresa" => $empresa, "unidades_negocio" => $unidades_negocio]);
    }

    public function showProcesos($id){
        $this->authorize('gestionar-panel-empresa', $id);
        //$this->authorize('gestionar-cadena');

        $empresa = Empresa::find($id);
        $unidades_negocio = UnidadNegocio::where('estado', true)->where('empresa_id', $id)->get();

        if(count($unidades_negocio)>0){
            if(session('unidad') == '')
                session(['unidad' => $unidades_negocio[0]->id]);
        }
        else
            return redirect('/empresa/'.$id.'/unidades-negocio')->withErrors(['unidad-error' => 'No se encontraron unidades de negocio habilitadas']);
        
        
        $procesos = DB::table('procesos')
                    ->selectRaw('procesos.*, GROUP_CONCAT(megaprocesos.id) as megaproceso, GROUP_CONCAT(megaprocesos.nombre) as megaprocesos_names')
                    ->join('megaproceso_procesos', 'procesos.id', '=', 'megaproceso_procesos.proceso_id')
                    ->join('megaprocesos', 'megaproceso_procesos.megaproceso_id', '=', 'megaprocesos.id')
                    ->where('unidad_negocio_id', session('unidad'))
                    ->where('proceso_padre', null)
                    ->groupBy('procesos.id')
                    ->get();
        return view('empresa.procesos.procesos', ["empresa" => $empresa, "unidades" => $unidades_negocio, "procesos" => $procesos]);
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
        $empresa = Empresa::find($id)->update([
            "ruc" => $request->ruc,
            "nombre" => $request->nombre,
            "direccion" => $request->direccion,
            "descripcion" => $request->descripcion
        ]);

        return response()->json(["message"=>"Registrado correctamente"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $empresa = Empresa::find($id);
        $empresa->estado = !$empresa->estado;
        $empresa->save();

        return response()->json(["Messagge" => $empresa],200);
    }
}
