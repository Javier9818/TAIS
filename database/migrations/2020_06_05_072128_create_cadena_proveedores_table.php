<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCadenaProveedoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cadena_proveedores', function (Blueprint $table) {
            $table->id();
            $table->integer('cadena_suministro_id');
            $table->integer('proveedor_id');
            $table->integer('proveedor_padre');
            $table->integer('nivel_proveedor_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cadena_proveedores');
    }
}
