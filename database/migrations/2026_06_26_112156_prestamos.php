<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Prestamos extends Migration
{
    public function up()
    {
        Schema::create('prestamos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idEmpleado');
            $table->integer('idFormaPago');
            $table->integer('idCuenta');
            $table->integer('idEgreso');
            $table->float('monto');
            $table->integer('activo');
            $table->integer('eliminado');
            $table->timestamps();
        });
    }
    
    
    public function down()
    {
        Schema::dropIfExists('prestamos');
    }
}
