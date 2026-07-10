<?php

namespace App\Http\Controllers;
use App\Egreso;
use App\Ingreso;
use App\Calendario;
use App\Egresosolicitude;
use Carbon\Carbon;

use App\Clases\Egresos;
use App\Clases\Ingresos;
use App\Clases\Folios;
use App\Clases\Consultas;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EgresosController extends BaseController
{
    function nuevo(Request $request){
        try {
            $funciones = new Egresos();
            $ingresos = new Ingresos();
            $folios = new Folios();

            $saldoEgresos = Egreso::where('activo', '=', 1)->where('idSucursal', '=', $request['sucursalID'])->where('idFormaPago', '=', 1)->where('idCalendario', '>=', 26)->sum('monto');
            $saldoIngresos = Ingreso::where('activo', '=', 1)->where('idSucursal', '=', $request['sucursalID'])->where('idFormaPago', '=', 1)->where('idCalendario', '>=', 26)->sum('monto');
            $fondo = $saldoIngresos - $saldoEgresos;
            if(floatval($request['monto']) > $fondo && intval($request['idFormaPago']) === 1){
                return response()->json("No cuentas con suficiente saldo para realizar este egreso su fondo es de ".$fondo, 400);
            }
            $folio = $folios->proximoEgreso($request['idNivel'], $request['idCalendario'], $request['sucursalID']);

            $egreso = $funciones->crearEgreso($request, $folio);
            $egreso = $funciones->completar($egreso);
            
            return response()->json($egreso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $funciones = new Egresos();
            $egresos = $funciones->busquedaGeneral();
            $datos = $egresos->whereRaw("DATE_FORMAT(egresos.created_at,'%y-%m-%d') = CURDATE()")->
            where('egresos.idSucursal', '=', $request['sucursalID'])->
            where('egresos.idUsuario', '=', $request['usuarioID'])->get();

            $respuesta['datos'] = $datos;
            $respuesta['listas'] = $funciones->listas();

            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try {
            $funciones = new Egresos();
            $egreso = $funciones->actualizar($request['id'], $request['concepto'], $request['monto'], $request['observaciones'], $request['idRubro'], $request['idTipo'], $request['idFormaPago'], $request['idCuenta']);

            $egreso = $funciones->completar($egreso);

            return response()->json($egreso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try {
            $funciones = new Egresos();
            $egreso = Egreso::find($request['id']);
            $egreso->activo = 0;
            $egreso->save();

            $funciones->eliminarDevolucion($egreso->id);
            return response()->json($egreso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function buscar(Request $request){
        try{
            $funciones = new Egresos();
            $busqueda = $funciones->busquedaGeneral();

            ($request['idCalendario'] !== 0) ? $busqueda->where('calendarios.id', '=', $request['idCalendario']) : null;
            ($request['idRubro'] !== 0) ? $busqueda->where('rubrosegresos.id', '=', $request['idRubro']) : null;
            ($request['idTipo'] !== 0) ? $busqueda->where('tiposegresos.id', '=', $request['idTipo']) : null;
            ($request['idSucursal'] !== 0) ? $busqueda->where('sucursales.id', '=', $request['idSucursal']) : null;
            ($request['idFormaPago'] !== 0) ? $busqueda->where('formaspagos.id', '=', $request['idFormaPago']) : null;
            ($request['idNivel'] !== 0) ? $busqueda->where('niveles.id', '=', $request['idNivel']) : null;
            
            $datos = $busqueda->get();
            return response()->json($datos, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function actualizarVoucher(Request $request){
        try {
            $funciones = new Egresos();

            $egreso = Egreso::find($request['id']);
            $egreso->voucher = $request['imagen'];
            $egreso->save();

            return response()->json($egreso->id, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerVoucher(Request $request){
        try {
            $egreso = Egreso::find($request['id']);
            return response()->json($egreso->voucher, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function actualizarFecha(Request $request){
        try {
            $egreso = Egreso::find($request['id']);
            $egreso->created_at = $request['fecha']['fecha'];
            $egreso->save();
            return response()->json($egreso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function gerentes(Request $request){
        try{
            $fechaInicio = Carbon::now()->addDay(1)->format('Y-m-d');
            $fechaFin = ($request['dias'] !== null && $request['dias'] !== '') ? Carbon::now()->subDay($request['dias'])->format('Y-m-d') : Carbon::now()->format('Y-m-d');

            $egresos = Egreso::leftjoin('calendarios', 'calendarios.id', '=', 'egresos.idCalendario')->
                       leftjoin('niveles', 'niveles.id', '=', 'egresos.idNivel')->
                       leftjoin('rubrosegresos', 'rubrosegresos.id', '=', 'egresos.idRubro')->
                       leftjoin('tiposegresos', 'tiposegresos.id', '=', 'egresos.idTipo')->
                       leftjoin('formaspagos', 'formaspagos.id', '=', 'egresos.idFormaPago')->
                       leftjoin('nominaegresos', 'nominaegresos.idEgreso', '=', 'egresos.id')->
                       leftjoin('nominas', 'nominas.id', '=', 'nominaegresos.idNomina')->
                       leftjoin('empleados', 'empleados.id', '=', 'nominas.idEmpleado')->
                       leftjoin('departamentos', 'departamentos.id', '=', 'nominas.idDepartamento')->
                       leftjoin('vales', 'vales.idEgreso', '=', 'egresos.id')->
                       leftjoin('cuentas', 'egresos.idCuenta', '=', 'cuentas.id')->
                       select(
                        'egresos.id',
                        'egresos.referencia',
                        'egresos.monto',
                        'egresos.idRubro',
                        'egresos.idTipo',
                        'egresos.idCalendario',
                        'egresos.idNivel',
                        'egresos.idFormaPago',
                        'egresos.observaciones',
                        'egresos.idCuenta',
                        'niveles.nombre as nivel',
                        'calendarios.nombre as calendario',
                        'egresos.folio',
                        DB::raw('DATE_FORMAT(egresos.created_at, "%d-%m-%Y %H:%i:%s") as fechaFormato'),
                        'rubrosegresos.nombre as rubro',
                        DB::raw("(CASE 
                            WHEN(egresos.idRubro = 3 AND egresos.idTipo = 4) THEN empleados.nombre
                            ELSE egresos.concepto
                            END) AS concepto"),
                        DB::raw("(CASE 
                            WHEN(egresos.idRubro = 3 AND egresos.idTipo = 4) THEN departamentos.nombre
                            WHEN(egresos.idRubro = 2 AND egresos.idTipo = 2) THEN vales.folio
                            ELSE tiposegresos.nombre
                            END) AS tipo"),
                        'formaspagos.nombre as forma',
                        DB::raw("CONCAT('$',FORMAT(egresos.monto,2)) AS montoFormato"),
                        DB::raw("IF(egresos.activo = 0, 'bg-rojo', '') AS bg"),
                        'cuentas.nombre as cuenta'
                       )->
                       whereBetween('egresos.created_at', [$fechaFin, $fechaInicio])->
                       where('egresos.idSucursal', '=', $request['sucursalID'])->get();

            return response()->json($egresos, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function traerComprobante(Request $request){
        try {
            $egreso = Egreso::find($request['id']);
            $respuesta['imagen'] = $egreso->voucher;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el sercidor', 400);
        }
    }

    function actualizarComprobante(Request $request){
        try {
            $egreso = Egreso::find($request['id']);
            $egreso->voucher = $request['imagen'];
            $egreso->save();

            return response()->json($egreso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function solicitarModificacion(Request $request){
        try {
            $funciones = new Egresos();
            if($funciones->existeSolicitud($request['id'])){
                return response()->json('Ya existe una solicitud para modificar este egreso', 400);
            }
            $solicitud = $funciones->crearSolicitud($request);
            return response()->json($solicitud, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrarSolicitudes(){
        try {
            $funciones = new Egresos();
            return response()->json($funciones->solicitudes(), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function aceptarModificacion(Request $request){
        try {
            $funciones = new Egresos();
            $modificacion = $funciones->aceptarModificacion($request['id'], $request['usuarioID']);
            if(is_null($modificacion)){
                $egreso->modificarEgreso($modificacion->idEgreso, $modificacion);
                return response()->json($modificacion, 200);
            }
            return response()->json($modificacion, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function rechazarModificacion(Request $request){
        try {
            $funciones = new Egresos();
            return response()->json($funciones->rechazarModificacion($request['id'], $request['usuarioID']), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}