<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Creditos extends Migration
{
    public function up()
    {
        Schema::create('creditos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idPrestador');
            $table->double('monto');
            $table->integer('idFormaPago');
            $table->integer('idCuenta')->nullable();
            $table->integer('idIngreso')->nullable();
            $table->integer('idUsuario');
            $table->integer('idSucursal');
            $table->integer('idNivel');
            $table->integer('idCalendario');
            $table->string('observaciones');
            $table->integer('activo');
            $table->integer('eliminado');
            $table->timestamps();
        });
    }
    
    
    public function down()
    {
        Schema::dropIfExists('creditos');
    }
}
