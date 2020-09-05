<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatrizPriorizacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matriz_priorizacion', function (Blueprint $table) {
            $table->id();
            $table->integer('proceso_id');
            $table->integer('criterio_id');
            $table->integer('puntaje');
            $table->integer('escala_id');
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
        Schema::dropIfExists('matriz_priorizacion');
    }
}
