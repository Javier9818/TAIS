<?php

namespace App\Http\Controllers;

use App\Auditoria;
use App\Empresa;
use App\Meta_empresarial;
use App\Meta_empresarial_empresa;
use App\Objetivo;
use App\Objetivo_estrategico;
use App\Perspectiva;
use App\Version_cobit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MetasEmpresarialesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($idEmpresa)
    {   
        $empresa = Empresa::find($idEmpresa);
        $version = session('version', 1);
        $objetivos = Objetivo_estrategico::where('empresa_fk', $idEmpresa)
                        ->where('version_fk', $version)
                        ->where('IS_PRIO', 1)
                        ->get();
        $perspectivas = Perspectiva::all();

        $meta_empresarial_fks = Meta_empresarial_empresa::where('meta_empresarial_empresa.empresa_fk', $idEmpresa)
                            ->join('objetivos_estrategicos', 'meta_empresarial_empresa.objetivo_fk', '=', 'objetivos_estrategicos.id')
                            ->where('objetivos_estrategicos.IS_PRIO', true)
                            ->where('meta_empresarial_empresa.version_fk', $version)
                            ->get()
                            ->pluck('meta_empresarial_fk');
                    
        $versiones = Version_cobit::all();
        
        $metas_resultantes = Meta_empresarial::whereIn('id', $meta_empresarial_fks)->get();
        return view('Empresa.cascada_metas.metas_empresariales', [
            "empresa" => $empresa, 
            "objetivos" => $objetivos, 
            "perspectivas" => $perspectivas,
            "metas_resultantes" => $metas_resultantes,
            "versiones" => $versiones
        ]);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function metasByPerspectiva($perspectiva_fk, $empresa_fk, $objetivo_fk){
        $metas = Meta_empresarial::where('perspectiva_fk', $perspectiva_fk)->with('perspectiva')->get();
        $array = $metas->pluck('id');
        $meta_empresa = Meta_empresarial_empresa::where('empresa_fk', $empresa_fk)
                        ->where('objetivo_fk', $objetivo_fk)
                        ->whereIn('meta_empresarial_fk', $array)
                        ->first();
        return response()->json(["metas" => $metas, "meta_empresa" => $meta_empresa]);
    }

    public function setMetaEmpresarialEmpresa(Request $request){
        $meta_empresarial_empresa = Meta_empresarial_empresa::updateOrCreate([
            "empresa_fk" => $request->empresa_fk,
            "objetivo_fk" => $request->objetivo_fk,
            "version_fk" => 1
        ],[
            "meta_empresarial_fk" => $request->meta_fk
        ]);

        $meta_empresarial_fks = Meta_empresarial_empresa::where('meta_empresarial_empresa.empresa_fk',$request->empresa_fk)
                            ->join('objetivos_estrategicos', 'meta_empresarial_empresa.objetivo_fk', '=', 'objetivos_estrategicos.id')
                            ->where('objetivos_estrategicos.IS_PRIO', true)
                            ->get()
                            ->pluck('meta_empresarial_fk');
        
        $metas_resultantes = Meta_empresarial::whereIn('id', $meta_empresarial_fks)->get();

        Auditoria::create([
            "tabla" => "meta_empresarial_empresa",
            "accion" => "Registro",
            "terminal" => "127.0.0.1",
            "navegador" => "Chrome 87",
            "user_fk" => $request->userID ?? 1
        ]);
        return response()->json([
            "meta_empresarial_empresa" => $meta_empresarial_empresa,
            "metas_resultantes" => $metas_resultantes
        ]);
    }

    public function setVersion(Request $request){
        // return dd(session('version'));
        session(['version' => $request->version.'']);
        // return dd(session('version'));
        return response()->json(["message" => "ok"]);
    }
}
