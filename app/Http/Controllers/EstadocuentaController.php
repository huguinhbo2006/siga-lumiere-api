<?php

namespace App\Http\Controllers;

use App\Clases\Estadocuenta;
use App\Clases\Cargos;
use App\Clases\Abonos;
use App\Clases\Ingresos;
use App\Clases\Egresos;
use App\Clases\Consultas;
use App\Clases\Folios;
use App\Clases\Descuentos;
use App\Clases\Devoluciones;
use App\Clases\Extras;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EstadocuentaController extends Controller
{
    function mostrar(Request $request){
        try {
            $funciones = new Estadocuenta();
            return response()->json(
                array(
                    'datos' => $funciones->cuenta($request['id']),
                    'listas' => $funciones-> listas() 
                ), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function agregarCargo(Request $request){
        try {
            $cargos = new Cargos();
            return response()->json($cargos->nuevo($request['id'], $request['idConcepto'], $request['concepto'], $request['monto'], $request['usuarioID']), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function quitarCargo(Request $request){
        try {
            $cargos = new Cargos();
            return response()->json($cargos->eliminar($request['id']));
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }


    function agregarAbono(Request $request){
        try {
            $cargos = new Cargos();
            $abonos = new Abonos();
            $ingresos = new Ingresos();
            $consultas = new Consultas();
            $folios = new Folios();

            $consultas->start();

            if($request['iva']){
                $cargos->nuevo($request['id'], 0, 'IVA ABONO', (floatval($request['monto']) * .16), $request['usuarioID']);
            }

            if($request['comision']){
                $cargos->nuevo($request['id'], 0, 'COMISION POR PAGO DE ABONO', $request['cantidad'], $request['usuarioID']);
            }

            $ingreso = $ingresos->nuevo(
                $request['concepto'],
                $request['monto'],
                'ABONO',
                1,
                2,
                $request['sucursalID'],
                $request['idCalendario'],
                $request['idFormaPago'],
                $request['idMetodoPago'],
                $request['usuarioID'],
                3,
                $request['idNivel'],
                $folios->proximoIngreso($request['idNivel'], $request['idCalendario'], $request['sucursalID']),
                $request['imagen'],
                $request['idBanco'],
                $request['referencia'],
                $request['propietario'],
                $request['idCuenta'],
                Carbon::now()
            );

            $abono = $abonos->nuevo($request['id'], $ingreso->id, $request['usuarioID'], $request['monto'], $request['concepto'], $request['idMetodoPago'], $request['idFormaPago'], $request['idConcepto']);


            $consultas->commit();
            return response()->json($abono, 200);
        } catch (Exception $e) {
            $consultas->rollback();
            return response()->json('Error en el servidor', 400);
        }
    }

    function quitarAbono(Request $request){
        try {
            $funciones = new Abonos();
            return response()->json($funciones->eliminar($request['id']), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function agregarDescuento(Request $request){
        try {
            $descuentos = new Descuentos();
            return response()->json($descuentos->nuevo($request['id'], $request['monto'], $request['concepto'], $request['idConcepto'], $request['idTipo'], $request['cantidad'], $request['usuarioID']), 200);
            ;
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function quitarDescuento(Request $request){
        try {
            $descuentos = new Descuentos();
            return response()->json($descuentos->eliminar($request['id']), 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function agregarDevolucion(Request $request){
        try {
            $folios = new Folios();
            $consultas = new Consultas();
            $egresos = new Egresos();
            $devoluciones = new Devoluciones();

            $consultas->start();

            $egreso = $egresos->nuevoEgreso(
                $request['concepto'],
                $request['monto'],
                'DEVOLUCION',
                1,
                1,
                $request['sucursalID'],
                $request['idCalendario'],
                $request['idFormaPago'],
                $request['usuarioID'],
                2,
                $request['nivel'],
                $folios->proximoEgreso($request['idNivel'], $request['idCalendario'], $request['sucursalID']),
                $request['idBanco'],
                ''
            );

            $devolucion = $devoluciones->nuevo($request['id'], $request['idConcepto'], $request['idFormaPago'], $request['monto'], $request['concepto'], $request['usuarioID'], $egreso->id);

            $consultas->commit();
            return response()->json($devolucion, 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function quitarDevolucion(Request $request){
        try {
            $devoluciones = new Devoluciones();
            $egresos = new Egresos();

            $egresos->eliminar($request['idEgreso']);
            $devolucion = $devoluciones->eliminar($request['id']);

            return response()->json($devolucion, 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function agregarExtra(Request $request){
        try {
            $extras = new Extras();
            return response()->json($extras->nuevo($request['id'], $request['idConcepto'], $request['concepto'], $request['monto'], $request['usuarioID']), 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function quitarExtra(Request $request){
        try {
            $extras = new Extras();
            return response()->json($extras->eliminar($request['id']), 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }
}