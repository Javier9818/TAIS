<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaEmpresarialEmpresaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meta_empresarial_empresa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('version_fk');  
            $table->timestamps();
        });

        Schema::table('meta_empresarial_empresa', function($table) {    
            $table->unsignedBigInteger('meta_empresarial_fk');      
            $table->foreign('meta_empresarial_fk')->references('id')->on('metas_empresariales');
            
            // $table->unsignedBigInteger('version_fk');      
            // $table->foreign('version_fk')->references('id')->on('versiones');

            $table->unsignedBigInteger('empresa_fk');      
            $table->foreign('empresa_fk')->references('id')->on('empresas');

            $table->unsignedBigInteger('objetivo_fk');      
            $table->foreign('objetivo_fk')->references('id')->on('objetivos_estrategicos');      
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meta_empresarial_empresa');
    }
}
