<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaAlineamientoEmpresaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meta_alineamiento_empresa', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::table('meta_alineamiento_empresa', function($table) {    
            $table->unsignedBigInteger('empresa_fk');      
            $table->foreign('empresa_fk')->references('id')->on('empresas'); 

            $table->unsignedBigInteger('alineamiento_fk');      
            $table->foreign('alineamiento_fk')->references('id')->on('metas_alineamiento');

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
        Schema::dropIfExists('meta_alineamiento_empresa');
    }
}
