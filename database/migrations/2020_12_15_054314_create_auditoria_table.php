<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditoriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auditoria', function (Blueprint $table) {
            $table->id();
            $table->string('terminal')->nullable();
            $table->string('navegador')->nullable();
            $table->string('tabla')->nullable();
            $table->string('accion')->nullable();
            $table->timestamps();
        });

        Schema::table('auditoria', function($table) {    
            $table->unsignedBigInteger('user_fk');      
            $table->foreign('user_fk')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auditoria');
    }
}
