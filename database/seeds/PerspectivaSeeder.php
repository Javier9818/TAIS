<?php

use App\Perspectiva;
use App\Version_cobit;
use Illuminate\Database\Seeder;

class PerspectivaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Perspectiva::create(["nombre" => "Financiera"]);
        Perspectiva::create(["nombre" => "Clientes"]);
        Perspectiva::create(["nombre" => "Procesos internos"]);
        Perspectiva::create(["nombre" => "Aprendizaje y crecimiento"]);

        Version_cobit::create(["descripcion" => "Versión actual"]);
    }
}
