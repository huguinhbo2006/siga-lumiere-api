<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Creditoabonos extends Migration
{
    public function up()
    {
        Schema::create('creditoabonos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idCredito');
            $table->double('monto');
            $table->integer('idFormaPago');
            $table->integer('idUsuario');
            $table->integer('tipo');
            $table->integer('activo');
            $table->integer('eliminado');
            $table->timestamps();
        });
    }
    
    
    public function down()
    {
        Schema::dropIfExists('creditoabonos');
    }
}
