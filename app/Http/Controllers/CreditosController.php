<?php

namespace App\Http\Controllers;

use App\Credito;
use App\Ingreso;
use App\Egreso;
use App\Creditoabono;
use App\Clases\Listas;
use App\Clases\Consultas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreditosController extends Controller
{
    function mostrar(){
        try {
            $datos['datos'] = Credito::where('eliminado', '=', 0)->get();
            $datos['listas'] = Listas::listas([
            	'formaspagos',
            	'cuentas',
            	'prestadores',
            	'sucursales',
            	'niveles',
            	'bancos',
                'actuales',
                'antiguos'
            ]);
            return response()->json($datos, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevo(Request $request){
        try {
            $consultas = new Consultas();
            $consultas->start();
            $ingreso = Ingreso::create([
                'concepto' => 'Credito',
                'monto' => $request['monto'],
                'observaciones' => $request['observaciones'],
                'idRubro' => 7,
                'idTipo' => 27,
                'idSucursal' => 10,
                'idCalendario' => $request['idCalendario'],
                'idFormaPago' => $request['idFormaPago'],
                'idMetodoPago' => 1,
                'idUsuario' => $request['usuarioID'],
                'referencia' => 100,
                'idNivel' => $request['idNivel'],
                'imagen' => '',
                'idBanco' => 0,
                'numeroReferencia' => '',
                'nombreCuenta' => '',
                'idCuenta' => $request['idCuenta'],
                'fecha' => Carbon::now()
            ]);

            $egreso = null;
            if(intval($request['tipo']) === 2){
                $egreso = Egreso::create([
                    'concepto' => 'Credito',
                    'monto' => $request['monto'],
                    'observaciones' => $request['observaciones'],
                    'idRubro' => 23,
                    'idTipo' => 199,
                    'idSucursal' => 10,
                    'idSucursalGasto' => 10,
                    'idCalendario' => $request['idPrestador'],
                    'idFormaPago' => $request['idFormaPago'],
                    'idUsuario' => $request['usuarioID'],
                    'referencia' => 100,
                    'idNivel' => $request['idNivel'],
                    'idCuenta' => $request['idCuenta'],
                    'voucher' => '',
                    'activo' => 1,
                    'eliminado' => 0,
                ]);
            }

            $data = $request->only(['idPrestador', 'idFormaPago', 'idCuenta', 'monto', 'observaciones', 'idNivel', 'idCalendario', 'tipo']);
            $data['idIngreso'] = $ingreso->id;
            $data['idEgreso'] = (is_null($egreso)) ? 0 : $egreso->id;
            $data['idUsuario'] = $request['usuarioID'];
            $data['idSucursal'] = 1;
            $dato = Credito::create($data);
            $consultas->commit();
            return response()->json($dato, 200);
        } catch (Exception $e) {
            $consultas->rollback();
            return response()->json('Error en el servidor', 400);
        }
    }

    function traer(Request $request){
        try {
            $respuesta['datos'] = Credito::find($request['id']);
            $respuesta['listas'] = Listas::listas(['formaspagos', 'cuentas']);
            return response()->json($respuesta, 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function abono(Request $request){
        try {
            $consultas = new Consultas();
            $consultas->start();
            $egreso = Egreso::create([
                'concepto' => 'Credito',
                'monto' => $request['monto'],
                'observaciones' => '',
                'idRubro' => 7,
                'idTipo' => 200,
                'idSucursal' => 10,
                'idSucursalGasto' => 10,
                'idCalendario' => $request['idCalendario'],
                'idFormaPago' => $request['idFormaPago'],
                'idUsuario' => $request['usuarioID'],
                'referencia' => 100,
                'idNivel' => $request['idNivel'],
                'idCuenta' => $request['idCuenta'],
                'voucher' => '',
                'activo' => 1,
                'eliminado' => 0,
            ]);
            $ingreso = array();
            if(intval($request['tipoPrestador']) === 2){
                $ingreso = Ingreso::create([
                    'concepto' => 'Credito',
                    'monto' => $request['monto'],
                    'observaciones' => '',
                    'idRubro' => 7,
                    'idTipo' => 28,
                    'idSucursal' => 10,
                    'idCalendario' => $request['idPrestador'],
                    'idFormaPago' => $request['idFormaPago'],
                    'idMetodoPago' => 1,
                    'idUsuario' => $request['usuarioID'],
                    'referencia' => 100,
                    'idNivel' => $request['idNivel'],
                    'imagen' => '',
                    'idBanco' => 0,
                    'numeroReferencia' => '',
                    'nombreCuenta' => '',
                    'idCuenta' => $request['idCuenta'],
                    'fecha' => Carbon::now()
                ]);
            }
            $datos = $request->only(['idFormaPago', 'idCuenta', 'monto', 'tipo']);
            $datos['idUsuario'] = $request['usuarioID'];
            $datos['idIngreso'] = ($ingreso) ? $ingreso->id : 0;
            $datos['idEgreso'] = $egreso->id;
            $datos['idCredito'] = $request['id'];
            $abono = Creditoabono::create($datos);
            $consultas->commit();
            return response()->json($abono, 200);
        } catch (Exception ) {
            $consultas->rollback();
            return response()->json('Error en el servidor', 400);
        }
    }
}