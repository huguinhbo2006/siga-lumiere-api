<?php

namespace App\Http\Controllers;

use App\Models\Prestamo;
use App\Models\Prestamoabono;
use App\Ingreso;
use App\Egreso;
use App\Clases\Listas;
use App\Clases\Consultas;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class PrestamosController extends Controller
{
    function mostrar(){
        try {
            $datos['datos'] = Prestamo::where('eliminado', '=', 0)->get();
            $datos['listas'] = Listas::listas([
                'formaspagos',
                'cuentas',
                'empleados'
            ]);
            return response()->json($datos, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    function nuevo(Request $request){
        try {
            $consultas = new Consultas();
            $consultas->start();

            // Generamos el Egreso base para el préstamo
            $egreso = Egreso::create([
                'concepto' => 'Prestamo',
                'monto' => $request['monto'],
                'observaciones' => '',
                'idRubro' => 0,
                'idTipo' => 0,
                'idSucursal' => $request['sucursalID'],
                'idSucursalGasto' => 1,
                'idCalendario' => $request['calendarioID'],
                'idFormaPago' => $request['idFormaPago'],
                'idUsuario' => $request['usuarioID'],
                'referencia' => 110,
                'idNivel' => 1,
                'idCuenta' => $request['idCuenta'],
                'voucher' => '',
                'activo' => 1,
                'eliminado' => 0,
            ]);

            // Mapeo incluyendo el campo 'idCalendario'
            $data = $request->only([
                'idEmpleado', 
                'idFormaPago', 
                'idCuenta', 
                'monto'
            ]);
            
            $data['idEgreso'] = $egreso->id; 
            $data['idCalendario'] = $request['calendarioID'];
            $data['activo'] = 1;
            $data['eliminado'] = 0;

            $dato = Prestamo::create($data);
            
            $consultas->commit();
            return response()->json($dato, 200);
        } catch (Exception $e) {
            $consultas->rollback();
            return response()->json($e->getMessage(), 400);
        }
    }

    function traer(Request $request){
        try {
            $respuesta['datos'] = Prestamo::find($request['id']);
            $respuesta['listas'] = Listas::listas(['formaspagos', 'cuentas']);
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    function abono(Request $request){
        try {
            $consultas = new Consultas();
            $consultas->start();

            // Generamos el Ingreso base para el abono del préstamo
            $ingreso = Ingreso::create([
                'concepto' => 'Prestamo',
                'monto' => $request['monto'],
                'observaciones' => '',
                'idRubro' => 0,
                'idTipo' => 0,
                'idSucursal' => $request['sucursalID'],
                'idCalendario' => $request['calendarioID'],
                'idFormaPago' => $request['idFormaPago'],
                'idMetodoPago' => 1,
                'idUsuario' => $request['usuarioID'],
                'referencia' => 110,
                'idNivel' => 1,
                'imagen' => '',
                'idBanco' => 0,
                'numeroReferencia' => '',
                'nombreCuenta' => '',
                'idCuenta' => $request['idCuenta'],
                'fecha' => Carbon::now()
            ]);

            // Mapeo de campos para el abono incluyendo los campos extras
            $datos = $request->only([
                'idFormaPago', 
                'idCuenta', 
                'monto'
            ]);
            
            $datos['idUsuario'] = $request['usuarioID'];
            $datos['idPrestamo'] = $request['id']; 
            $datos['idEgreso'] = 0; 
            $datos['activo'] = 1;
            $datos['eliminado'] = 0;
            $datos['idCalendario'] = $request['calendarioID'];

            $abono = Prestamoabono::create($datos);

            $consultas->commit();
            return response()->json($abono, 200);
        } catch (Exception $e) {
            $consultas->rollback();
            return response()->json($e->getMessage(), 400);
        }
    }
}