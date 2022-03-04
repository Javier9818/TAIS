<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObjetivosControlEmpresaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('objetivos_control_empresa', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::table('objetivos_control_empresa', function($table) {    
            $table->unsignedBigInteger('empresa_fk');      
            $table->foreign('empresa_fk')->references('id')->on('empresas'); 

            $table->unsignedBigInteger('control_fk');      
            $table->foreign('control_fk')->references('id')->on('objetivos_control');

            $table->unsignedBigInteger('version_fk');      
            $table->foreign('version_fk')->references('id')->on('version_cobit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('objetivos_control_empresa');
    }
}
