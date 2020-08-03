<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function authenticate(Request $request)
    {
        // return dd($request->all());
        // return Auth::attempt(["email" => $request->email, "password" => $request->password]);
        if (Auth::attempt(["email" => $request->email, "password" => $request->password])) {
            $user = Auth::user();
            if($user->isAdmin) return redirect(route('home'));
            else if($user->isCustomer){
                // $empresa = DB::select('select * from users_empresas where user_id = ?', [$user->id])[0]->empresa_id;
                // session(['empresa' => $empresa]);
                return redirect()->intended('/empresa/'.$user->empresa_id);
            }
            else return redirect()->intended('/');
        }else
            return redirect('/')->withErrors(['login-error' => 'Usuario o contraseña incorrectos']);
    }

    
}
