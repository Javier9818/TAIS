<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
        //DB::insert('insert into scopes (id, description) values (?, ?)', [5, 'Super usuario']);

    }
}
