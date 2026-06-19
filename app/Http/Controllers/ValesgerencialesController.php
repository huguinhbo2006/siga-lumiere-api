<?php

namespace App\Http\Controllers;
use App\Clases\Valesgerenciales;
use App\Clases\Consultas;
use App\Clases\Sucursales;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValesgerencialesController extends BaseController
{
    function nuevo(Request $request){
        try{
            $consultas = new Consultas();
            $funciones = new Valesgerenciales();
            $sucursales = new Sucursales();

            $consultas->start();
            if(floatval($request['monto']) > $sucursales->saldo($request['sucursalID'])){
                return response()->json('No existe suficiente saldo en sucursal para crear el vale', 400);
            }

            $respuesta['egreso'] = $funciones->crearEgreso($request, $request['sucursalID'], $request['usuarioID']);

            $respuesta['vale'] = $funciones->crearVale($request, $respuesta['egreso']);
            $consultas->commit();
            return response()->json($respuesta['vale'], 200);
        }catch(Exception $e){
            $consultas->rollback();
            return response()->json('Error de servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $funciones = new Valesgerenciales();
            $respuesta['datos'] = $funciones->mostrar($request['sucursalID']);
            $respuesta['listas'] = $funciones->listas();
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function buscar(Request $request){
        try {
            $vales = Vale::leftjoin('calendarios', 'idCalendario', '=', 'calendarios.id')->
                    select(
                        'vales.id',
                        'vales.monto',
                        'calendarios.nombre as calendario',
                        'vales.idSucursalEntrada',
                        'vales.idSucursalSalida',
                        'vales.idCalendario',
                        'vales.idEgreso',
                        'vales.observaciones',
                        'vales.folio',
                        DB::raw("(CASE 
                            WHEN(vales.aceptado = 0) THEN 'bg-amarillo'
                            WHEN(vales.aceptado = 1) THEN 'bg-verde'
                            WHEN(vales.aceptado = 2) THEN 'bg-rojo'
                            END) AS bg")
                        )->
                    where('vales.idSucursalSalida', '=', $request['idSucursal'])->
                    where('vales.eliminado', '=', 0)->
                    where('vales.idUsuarioCreo', '=', $request['idUsuario'])->
                    where('vales.idCalendario', '=', $request['idCalendario'])->get();
            return response()->json($vales, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function aceptarModificacion(Request $request){
        try{
            $solicitud = Solicitudesvalesgerenciale::find($request['idSolicitud']);
            $saldoTotalSucursal = saldoTotalSucursal($request['idSucursal']);
            if($solicitud->monto > $saldoTotalSucursal){
                return response()->json("No se cuenta con el saldo suficiente para realizar este vale", 400);
            }
            DB::beginTransaction();

            $vale = Valesgerenciale::find($request['id']);
            $vale->monto = $solicitud->monto;
            $vale->observaciones = $solicitud->observaciones;
            $vale->estatus = 1;
            $vale->save();

            $vale->calendario = Calendario::find($vale->idCalendario)->nombre;
            $vale->bg = 'bg-verde';

            $egreso = Egreso::find($vale->idEgreso);
            $egreso->monto = $vale->monto;
            $egreso->save();

            $solicitud->delete();
            DB::commit();
            return response()->json($vale, 200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json('Error de servidor', 400);
        }
    }

    function rechazarModificacion(Request $request){
        try {
            DB::beginTransaction();
            $vale = Valesgerenciale::find($request['id']);
            $vale->estatus = 1;
            $vale->save();
            $vale->calendario = Calendario::find($vale->idCalendario)->nombre;
            $vale->bg = 'bg-verde';

            $solicitud = Solicitudesvalesgerenciale::find($request['idSolicitud']);
            $solicitud->delete();
            DB::commit();
            return response()->json($vale, 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try {
            $funciones = new Valesgerenciales();
            $consultas = new Consultas();
            
            $consultas->start();

            $vale = $funciones->crearSolicitud($request);

            $consultas->commit();
            return response()->json($vale, 200);
        } catch (Exception $e) {
            $consultas->rollback();
            return response()->json('Error en el servidor', 400);
        }
    }

    function aceptar(Request $request){
        try{
            DB::beginTransaction();
            $vale = Valesgerenciale::find($request['id']);

            $folio = proximoFolioIngreso($request['idNivel'], $request['idCalendario'], $request['idSucursal']);
            $ingreso = Ingreso::create([
                'concepto' => 'Vale Gerencial de Sucursal '.$request['sucursal'],
                'monto' => $request['monto'],
                'observaciones' => $request['observaciones'],
                'idRubro' => 2,
                'idTipo' => 3,
                'idSucursal' => $request['idSucursal'],
                'idCalendario' => $request['idCalendario'],
                'idFormaPago' => 1,
                'idMetodoPago' => 1,
                'idUsuario' => $request['usuario'],
                'idNivel' => $vale->idNivel,
                'folio' => $folio,
                'referencia' => 4,
                'activo' => 1,
                'eliminado' => 0,
            ]);

            
            
            $vale->idUsuarioRetorno = $request['usuario'];
            $vale->idIngreso = $ingreso->id;
            $vale->estatus = 2;
            $vale->save();
            $vale->calendario = Calendario::find($vale->idCalendario)->nombre;
            $vale->bg = (intval($vale->estatus) === 1) ? 'bg-verde' : 'bg-rojo';
            DB::commit();
            return response()->json($vale, 200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json('Error de servidor', 400);
        }
    }
}