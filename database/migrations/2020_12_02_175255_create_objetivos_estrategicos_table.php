<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObjetivosEstrategicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('objetivos_estrategicos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripcion');
            $table->integer('version_fk')->default(0);
            $table->boolean('IS_PRIO')->default(1);
            $table->timestamps();
        });

        Schema::table('objetivos_estrategicos', function($table) {    
            $table->unsignedBigInteger('empresa_fk');      
            $table->foreign('empresa_fk')->references('id')->on('empresas'); 
            
            $table->unsignedBigInteger('perspectiva_fk');   
            $table->foreign('perspectiva_fk')->references('id')->on('perspectivas');   
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('objetivos_estrategicos');
    }
}
