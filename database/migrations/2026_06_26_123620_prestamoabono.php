<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Prestamoabono extends Migration
{
    public function up()
    {
        Schema::create('prestamoabonos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idPrestamo');
            $table->double('monto');
            $table->integer('idFormaPago');
            $table->integer('idUsuario');
            $table->integer('idCuenta');
            $table->integer('activo');
            $table->integer('eliminado');
            $table->timestamps();
        });
    }
    
    
    public function down()
    {
        Schema::dropIfExists('prestamoabonos');
    }
}
