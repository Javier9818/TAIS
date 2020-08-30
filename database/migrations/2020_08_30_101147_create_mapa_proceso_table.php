<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMapaProcesoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mapa_proceso', function (Blueprint $table) {
            $table->id();
            $table->integer('unidad_negocio_id');
            $table->integer('proceso_maestro');
            $table->longText('objeto');
            $table->boolean('mega');
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
        Schema::dropIfExists('mapa_proceso');
    }
}
