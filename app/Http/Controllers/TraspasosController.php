<?php

namespace App\Http\Controllers;

use App\Models\Traspaso;
use App\Egreso;
use App\Ingreso;

use App\Clases\Consultas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TraspasosController extends Controller
{
    function mostrar(){
        try {
            $datos = Traspaso::where('eliminado', '=', 0)->get();
            return response()->json($datos, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevo(Request $request){
        try {
        	$consultas = new Consultas();
        	$consultas->start();
        	$egresoData['concepto'] = "Traspaso";
        	$egresoData['monto'] = $request['ingreso']['monto'];
        	$egresoData['observaciones'] = '';
        	$egresoData['idRubro'] = 22;
        	$egresoData['idTipo'] = 198;
        	$egresoData['idSucursal'] = 10;
        	$egresoData['idSucursalGasto'] = 10;
        	$egresoData['idCalendario'] = $request['calendarioID'];
        	$egresoData['idFormaPago'] = ($request['egreso']['efectivo']) ? 1 : 4;
        	$egresoData['idUsuario'] = $request['usuarioID'];
        	$egresoData['referencia'] = 120;
        	$egresoData['idNivel'] = 1;
        	$egresoData['idCuenta'] = $request['egreso']['id'];
        	$egresoData['voucher'] = '';

        	$egreso = Egreso::create($egresoData);

        	$ingresoData['concepto'] = 'Traspaso';
        	$ingresoData['monto'] =  $request['ingreso']['monto'];
        	$ingresoData['observaciones'] = '';
        	$ingresoData['idRubro'] = 6;
        	$ingresoData['idTipo'] = 26;
        	$ingresoData['idSucursal'] = 10;
        	$ingresoData['idCalendario'] = $request['calendarioID'];
        	$ingresoData['idFormaPago'] = ($request['egreso']['efectivo']) ? 4 : $request['ingreso']['idFormaPago'];
        	$ingresoData['idCuenta'] = $request['ingreso']['idCuenta'];
        	$ingresoData['idMetodoPago'] = 1;
        	$ingresoData['idUsuario'] = $request['usuarioID'];
        	$ingresoData['referencia'] = 120;
        	$ingresoData['idNivel'] = 1;
        	$ingresoData['imagen'] = '';
        	$ingresoData['idBanco'] = 0;
        	$ingresoData['numeroReferencia'] = '';
        	$ingresoData['nombreCuenta'] = '';
        	$ingresoData['fecha'] = Carbon::now();

        	$ingreso = Ingreso::create($ingresoData);


        	$traspasoData['idIngreso'] = $ingreso->id;
        	$traspasoData['idEgreso'] = $egreso->id;
        	$traspasoData['monto'] = $request['ingreso']['monto'];
        	$traspasoData['idCuenta'] = $request['ingreso']['idCuenta'];
        	$traspasoData['idFormaPago'] = $request['ingreso']['idFormaPago'];

        	$traspaso = Traspaso::create($traspasoData);
        	$consultas->commit();

            return response()->json($traspaso, 200);
        } catch (Exception $e) {
        	$consultas->rollback();
            return response()->json('Error en el servidor', 400);
        }
    }
}