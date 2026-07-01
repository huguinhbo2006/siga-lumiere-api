<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Trapasos extends Migration
{
    public function up()
    {
        Schema::create('traspasos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idIngreso');
            $table->integer('idEgreso');
            $table->integer('idFormaPago');
            $table->integer('activo');
            $table->integer('eliminado');
            $table->timestamps();
        });
    }
    
    
    public function down()
    {
        Schema::dropIfExists('traspasos');
    }
}
