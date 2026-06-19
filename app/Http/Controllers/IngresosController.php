<?php

namespace App\Http\Controllers;
use App\Ingreso;
use App\Ingresosolicitude;
use App\Calendario;

//Clases personalizadas
use App\Clases\Folios;
use App\Clases\Imagenes;
use App\Clases\Ingresos;
use Carbon\Carbon;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngresosController extends BaseController
{
    function nuevo(Request $request){
        try {
            $folios = new Folios();
            $funciones = new Ingresos();

            $ingreso = $funciones->nuevo(
                $request['concepto'],
                $request['monto'],
                $request['observaciones'],
                $request['idRubro'],
                $request['idTipo'],
                $request['sucursalID'],
                $request['idCalendario'],
                $request['idFormaPago'],
                $request['idMetodoPago'],
                $request['usuarioID'],
                $request['referencia'],
                $request['idNivel'],
                $folios->proximoIngreso($request['idNivel'], $request['idCalendario'], $request['sucursalID']),
                ($request['idFormaPago'] === 1) ? null : $request['imagen'],
                ($request['idFormaPago'] === 1) ? null : $request['idBanco'],
                ($request['idFormaPago'] === 1) ? null : $request['numeroReferencia'],
                ($request['idFormaPago'] === 1) ? null : $request['nombreCuenta'],
                $request['idCuenta'],
                (isset($request['fecha']) && strlen($request['fecha']) > 0) ? $request['fecha'] : Carbon::now()
            );
            $ingreso = $funciones->completar($ingreso);
            return response()->json($ingreso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $funciones = new Ingresos();

            $registros = $funciones->busquedaGeneral();
            $datos = $registros->whereRaw("DATE_FORMAT(ingresos.created_at,'%y-%m-%d') = CURDATE()")->
                where('ingresos.idUsuario', '=', $request['usuarioID'])->
                where('ingresos.idSucursal', '=', $request['sucursalID'])->get();

            $respuesta['listas'] = $funciones->listas();
            $respuesta['datos'] = $datos;

            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function buscar(Request $request){
        try{
            $funciones = new Ingresos();
            $busqueda = $funciones->busquedaGeneral();

            ($request['idCalendario'] !== 0) ? $busqueda->where('calendarios.id', '=', $request['idCalendario']) : null;
            ($request['idRubro'] !== 0) ? $busqueda->where('rubros.id', '=', $request['idRubro']) : null;
            ($request['idTipo'] !== 0) ? $busqueda->where('tiposingresos.id', '=', $request['idTipo']) : null;
            ($request['idSucursal'] !== 0) ? $busqueda->where('sucursales.id', '=', $request['idSucursal']) : null;
            ($request['idMetodoPago'] !== 0) ? $busqueda->where('metodospagos.id', '=', $request['idMetodoPago']) : null;
            ($request['idFormaPago'] !== 0) ? $busqueda->where('formaspagos.id', '=', $request['idFormaPago']) : null;
            ($request['idNivel'] !== 0) ? $busqueda->where('niveles.id', '=', $request['idNivel']) : null;
            
            $datos = $busqueda->get();
            
            return response()->json($datos, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try {
            $funciones = new Ingresos();

            $ingreso = Ingreso::find($request['id']);
            $ingreso->activo = 0;
            $ingreso->save();

            if(intval($ingreso->referencia) === 2 || intval($ingreso->referencia) === 3){
                $abono = $funciones->traerAbono($request['id']);
                $abono->eliminado = 1;
                $abono->save();
            }

            $ingreso = $funciones->completar($ingreso);

            return response()->json($ingreso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try {
            $funciones = new Ingresos();
            $ingreso = $funciones->modificar(
                $request['id'],
                $request['concepto'],
                $request['monto'],
                $request['observaciones'],
                $request['idRubro'],
                $request['idTipo'],
                $request['idFormaPago'],
                $request['idMetodoPago'],
                $request['idBanco'],
                $request['nombreCuenta'],
                $request['numeroReferencia'],
                $request['idCuenta']
            );
            $ingreso = $funciones->completar($ingreso);
            return response()->json($ingreso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function gerentes(Request $request){
        try{
            $fechaInicio = Carbon::now()->addDay(1)->format('Y-m-d');
            $fechaFin = ($request['dias'] !== null && $request['dias'] !== '') ? Carbon::now()->subDay($request['dias'])->format('Y-m-d') : Carbon::now()->format('Y-m-d');

            $ingresos = Ingreso::leftjoin('calendarios', 'calendarios.id', '=', 'ingresos.idCalendario')->
                       leftjoin('niveles', 'niveles.id', '=', 'ingresos.idNivel')->
                       leftjoin('rubros', 'rubros.id', '=', 'ingresos.idRubro')->
                       leftjoin('tiposingresos', 'tiposingresos.id', '=', 'ingresos.idTipo')->
                       leftjoin('formaspagos', 'formaspagos.id', '=', 'ingresos.idFormaPago')->
                       leftjoin('vales', 'vales.idIngreso', '=', 'ingresos.id')->
                       leftjoin('cuentas', 'ingresos.idCuenta', '=', 'cuentas.id')->
                       select(
                        'ingresos.id',
                        'ingresos.idFormaPago',
                        'ingresos.idNivel',
                        'ingresos.idMetodoPago',
                        'ingresos.idCalendario',
                        'ingresos.idRubro',
                        'ingresos.idTipo',
                        'ingresos.idCuenta',
                        'ingresos.idBanco',
                        'ingresos.idTipo',
                        'ingresos.numeroReferencia',
                        'ingresos.nombreCuenta',
                        'ingresos.observaciones',
                        'ingresos.concepto',
                        'ingresos.fecha',
                        'ingresos.monto',
                        'ingresos.idUsuario',
                        'ingresos.referencia',
                        'calendarios.nombre as calendario',
                        'niveles.nombre as nivel',
                        'ingresos.folio',
                        DB::raw('DATE_FORMAT(ingresos.created_at, "%d-%m-%Y %H:%i:%s") as fechaFormato'),
                        'rubros.nombre as rubro',
                        DB::raw("(CASE 
                            WHEN(ingresos.idRubro = 2 AND ingresos.idTipo = 3) THEN vales.folio
                            ELSE ingresos.concepto
                            END) AS concepto"),
                        'formaspagos.nombre as forma',
                        DB::raw("IF(ingresos.idFormaPago <> 1, cuentas.nombre, 'N/A') as cuenta"),
                        DB::raw("IF(ingresos.idFormaPago <> 1, IF(LENGTH(ingresos.imagen) > 0, 'SI', 'NO'), 'N/A') as hayVoucher"),
                        DB::raw("CONCAT('$',FORMAT(ingresos.monto,2)) AS montoFormato"),
                        DB::raw("IF(ingresos.activo = 0, 'bg-rojo', '') as bg")
                       )->
                       whereBetween('ingresos.created_at', [$fechaFin, $fechaInicio])->
                       where('ingresos.idSucursal', '=', $request['sucursalID'])->get();
            return response()->json($ingresos, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function cargar(Request $request) {
        try {
            $ingreso = Ingreso::find($request['id']);
            $ingreso->imagen = $request['imagen'];
            $ingreso->save();
            return response()->json($ingreso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function voucher(Request $request){
        try {
            $ingreso = Ingreso::find($request['id']);
            return response()->json($ingreso->imagen, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function solicitudes(Request $request){
        try {
            $funciones = new Ingresos();
            return response()->json($funciones->solicitudes(), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function solicitar(Request $request){
        try {
            $funciones = new Ingresos();
            if($funciones->existeSolicitud($request['id'])){
                return response()->json('Ya existe una solicitud para modificar este ingreso', 400);
            }
            return response()->json(
                $funciones->nuevaSolicitud(
                    $request['id'],
                    $request['usuarioID'],
                    $request['concepto'],
                    $request['monto'],
                    $request['observaciones'],
                    $request['idRubro'],
                    $request['idTipo'],
                    $request['idFormaPago'],
                    $request['idMetodoPago'],
                    $request['fecha'],
                    $request['idBanco'],
                    $request['nombreCuenta'],
                    $request['numeroReferencia'],
                    $request['idCuenta']
                ),
            200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function aceptar(Request $request){
        try {
            $funciones = new Ingresos();
            $solicitud = $funciones->aceptarSolicitud($request['id'], $request['usuarioID']);
            $ingreso = $funciones->modificar(
                $solicitud->idIngreso,
                $solicitud->concepto,
                $solicitud->monto,
                $solicitud->observaciones,
                $solicitud->idRubro,
                $solicitud->idTipo,
                $solicitud->idFormaPago,
                $solicitud->idMetodoPago,
                $solicitud->idBanco,
                $solicitud->nombreCuenta,
                $solicitud->numeroReferencia,
                $solicitud->idCuenta
            );
            return response()->json($solicitud, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function rechazar(Request $request){
        try {
            $funciones = new Ingresos();
            return response()->json($funciones->rechazarSolicitud($request['id'], $request['usuarioID']), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}