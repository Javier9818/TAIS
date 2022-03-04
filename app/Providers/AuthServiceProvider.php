<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user) {
            if ($user->isAdmin) return true;
        });

        Gate::define('gestionar-panel-general', function($user){
            return $user->isAdmin;
        });

        Gate::define('gestionar-panel-empresa', function($user, $idEmpresa){
            return ($user->isCustomer && $user->empresa_id == $idEmpresa);
        });

        Gate::define('gestionar-objetivos-estrategicos', function($user){
            $permisos = $user->permisoUser;
            foreach ($permisos as $key => $permiso) {
                if($permiso->id === 2)
                return true;
            }
            return false;
        });

        Gate::define('gestionar-cascada-metas', function($user){
            $permisos = $user->permisoUser;
            foreach ($permisos as $key => $permiso) {
                if($permiso->id === 3)
                return true;
            }
            return false;
        });

        Gate::define('gestionar-clientes', function($user){
            $permisos = $user->permisoUser;
            foreach ($permisos as $key => $permiso) {
                if($permiso->id === 3)
                return true;
            }
            return false;
        });

        Gate::define('gestionar-proveedores', function($user){
            $permisos = $user->permisoUser;
            foreach ($permisos as $key => $permiso) {
                if($permiso->id === 2)
                return true;
            }
            return false;
        });

        Gate::define('gestionar-unidades-negocio', function($user){
            $permisos = $user->permisoUser;
            foreach ($permisos as $key => $permiso) {
                if($permiso->id === 5)
                return true;
            }
            return false;
        });

        Gate::define('gestionar-cadena', function($user){
            $permisos = $user->permisoUser;
            foreach ($permisos as $key => $permiso) {
                if($permiso->id === 4)
                return true;
            }
            return false;
        });
        

        Gate::define('gestionar-procesos', function($user){
            $permisos = $user->permisoUser;
            foreach ($permisos as $key => $permiso) {
                if($permiso->id === 5)
                return true;
            }
            return false;
        });

        Gate::define('gestionar-mapa-procesos', function($user){
            $permisos = $user->permisoUser;
            foreach ($permisos as $key => $permiso) {
                if($permiso->id === 6)
                return true;
            }
            return false;
        });

        Gate::define('gestionar-priorizacion', function($user){
            $permisos = $user->permisoUser;
            foreach ($permisos as $key => $permiso) {
                if($permiso->id === 7)
                return true;
            }
            return false;
        });

        Gate::define('gestionar-caracterizacion', function($user){
            $permisos = $user->permisoUser;
            foreach ($permisos as $key => $permiso) {
                if($permiso->id === 8)
                return true;
            }
            return false;
        });

        Gate::define('gestionar-diagrama-flujo', function($user){
            $permisos = $user->permisoUser;
            foreach ($permisos as $key => $permiso) {
                if($permiso->id === 9)
                return true;
            }
            return false;
        });

        Gate::define('gestionar-diagrama-seguimiento', function($user){
            $permisos = $user->permisoUser;
            foreach ($permisos as $key => $permiso) {
                if($permiso->id === 10)
                return true;
            }
            return false;
        });
    }
}
