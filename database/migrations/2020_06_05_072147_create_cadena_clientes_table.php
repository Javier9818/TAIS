<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCadenaClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cadena_clientes', function (Blueprint $table) {
            $table->id();
            $table->integer('cadena_suministro_id');
            $table->integer('cliente_id');
            $table->integer('cliente_padre');
            $table->integer('nivel_cliente_id');
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
        Schema::dropIfExists('cadena_clientes');
    }
}
