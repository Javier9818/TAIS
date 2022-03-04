<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetasEmpresarialesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metas_empresariales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('sigla');
        });

        Schema::table('metas_empresariales', function($table) {    
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
        Schema::dropIfExists('metas_empresariales');
    }
}
