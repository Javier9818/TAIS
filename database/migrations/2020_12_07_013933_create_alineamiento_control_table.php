<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlineamientoControlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alineamiento_control', function (Blueprint $table) {
            $table->id();
            $table->string('simbolo');
            $table->integer('peso');
            $table->timestamps();
        });

        Schema::table('alineamiento_control', function($table) {    
            $table->unsignedBigInteger('alineamiento_fk');      
            $table->foreign('alineamiento_fk')->references('id')->on('metas_alineamiento'); 

            $table->unsignedBigInteger('control_fk');      
            $table->foreign('control_fk')->references('id')->on('objetivos_control');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alineamiento_control');
    }
}
