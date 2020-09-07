<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVersionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('version', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->longText('procesos')->nullable();
            $table->longText('mapa_proceso')->nullable();
            $table->longText('priorizacion')->nullable();
            $table->longText('seguimiento')->nullable();
            $table->integer('unidad_negocio_id');
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
        Schema::dropIfExists('version');
    }
}
