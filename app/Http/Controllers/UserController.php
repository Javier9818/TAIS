<?php

namespace App\Http\Controllers;

use App\Auditoria;
use App\Empresa;
use App\Person;
use App\Scope;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = DB::table('users')
            ->leftJoin("empresas", "users.empresa_id", "=", "empresas.id")
            ->join("persons", "persons.id", "=", "users.person_id")
            ->leftjoin("scope_user", "scope_user.user_id", "=", "users.id")
            ->selectRaw('users.email, users.id, users.isCustomer as tipoUser, empresas.id as empresa, empresas.nombre as name_empresa,
                persons.names, persons.last_name_pat as appaterno, persons.last_name_mat as apmaterno, persons.address,
                GROUP_CONCAT(scope_user.scope_id) as scopes')
            ->groupBy('users.id')
            ->get();
        $empresas = Empresa::all();
        $scopes = Scope::all();
        return view('admin.users', ["users" => $users, "empresas" => $empresas, "scopes" => $scopes]);
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
        $persona = Person::create([
            "names" => $request->names,
            "last_name_pat" => $request->appaterno,
            "last_name_mat" => $request->apmaterno,
            // "phone" => $request->phone,
            "address" => $request->address,
            // "dni" => $request->dni
        ]);

        $tipoUser = $request->tipoUser == '0' ? true: false;

        $user = User::create([
            "email" => $request->email,
            "isAdmin" => $tipoUser,
            "isCustomer" => !$tipoUser,
            // "photo" => $request->descripcion,
            "password" => Hash::make($request->password),
            "person_id" => $persona->id,
            "empresa_id" => $request->empresa
        ]);

        foreach ($request->scopes as $key => $value) {
            DB::insert('insert into scope_user(scope_id, user_id) values (?, ?)', [$value, $user->id]);
        }
        Auditoria::create([
            "tabla" => "users",
            "accion" => "Registro",
            "terminal" => "127.0.0.1",
            "navegador" => "Chrome 87",
            "user_fk" => $request->userID ?? 1
        ]);
        $request->id = $user->id;
        return response()->json(["user" => $request->all()], 200);
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
    public function update(Request $request)
    {
        $user = User::find($request->id);
        $persona = Person::find($user->person_id);

        $tipoUser = ($request->tipoUser == '0' || $request->tipoUser == 0) ? true: false;

        $persona->update([
            "names" => $request->names,
            "last_name_pat" => $request->appaterno,
            "last_name_mat" => $request->apmaterno,
            // "phone" => $request->phone,
            "address" => $request->address,
        ]);

        $user->update([
            "email" => $request->email,
            "isAdmin" => $tipoUser,
            "isCustomer" => !$tipoUser,
            "empresa_id" => $request->empresa
        ]);

        DB::table('scope_user')->where('user_id', "=", $request->id) ->delete();

        foreach ($request->scopes ?? [] as $key => $value) {
            DB::insert('insert into scope_user(scope_id, user_id) values (?, ?)', [$value, $request->id]);
        }

        Auditoria::create([
            "tabla" => "users",
            "accion" => "Edición",
            "terminal" => "127.0.0.1",
            "navegador" => "Chrome 87",
            "user_fk" => $request->userID ?? 1
        ]);
        return response()->json(["user" => $request->all()], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $persona = Person::find($user->person_id);

        $persona->delete();
        $user->delete();
        DB::table('scope_user')->where('user_id', "=", $id) ->delete();
        Auditoria::create([
            "tabla" => "users",
            "accion" => "Eliminación",
            "terminal" => "127.0.0.1",
            "navegador" => "Chrome 87",
            "user_fk" => Auth::id() ?? 1
        ]);
        return response()->json(["message" => true], 200);
    }
}
