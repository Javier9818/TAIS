<?php

use App\Person;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::insert('insert into scopes (id, description) values (?, ?)', [1, 'Gestionar Empresa']);
        DB::insert('insert into scopes (id, description) values (?, ?)', [2, 'Gestionar Proveedores']);
        DB::insert('insert into scopes (id, description) values (?, ?)', [3, 'Gestionar Clientes']);
        DB::insert('insert into scopes (id, description) values (?, ?)', [4, 'Gestionar Cadena de Suministro']);
        DB::insert('insert into scopes (id, description) values (?, ?)', [5, 'Unidades de negocio']);


        DB::insert('insert into megaprocesos (id, nombre) values(?, ?)', [1, 'Proceso Estratégico']);
        DB::insert('insert into megaprocesos (id, nombre) values(?, ?)', [2, 'Proceso Primario']);
        DB::insert('insert into megaprocesos (id, nombre) values(?, ?)', [3, 'Proceso de Apoyo']);

        $persona = Person::create([
            "names" =>"Javier",
            "last_name_pat" => "Briceño",
            "last_name_mat" => "Montaño",
            "address" => "Ninguna",
        ]);

        User::create([
            "email" => "jbriceno@unitru.edu.pe",
            "isAdmin" => true,
            "isCustomer" => false,
            // "photo" => $request->descripcion,
            "password" => Hash::make("72764269"),
            "person_id" => $persona->id
        ]);


    }
}
