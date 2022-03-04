<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresarialAlineamientoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresarial_alineamiento', function (Blueprint $table) {
            $table->id();
            $table->string('simbolo');
            $table->integer('peso');
            $table->timestamps();
        });

        Schema::table('empresarial_alineamiento', function($table) {    
            $table->unsignedBigInteger('meta_fk');      
            $table->foreign('meta_fk')->references('id')->on('metas_empresariales');

            $table->unsignedBigInteger('alineamiento_fk');      
            $table->foreign('alineamiento_fk')->references('id')->on('metas_alineamiento'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empresarial_alineamiento');
    }
}
