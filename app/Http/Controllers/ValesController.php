<?php

namespace App\Http\Controllers;
use App\Vale;
use App\Calendario;
use App\Sucursale;
use App\Ingreso;
use App\Egreso;
use App\Nivele;
use App\Clases\Vales;
use App\Clases\Egresos;
use App\Clases\Folios;
use App\Clases\Consultas;
use App\Clases\Sucursales;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValesController extends BaseController
{
    function nuevo(Request $request){
        try{
            $funciones = new Vales();
            $egresos = new Egresos();
            $folios = new Folios();
            $consultas = new Consultas();
            $sucursales = new Sucursales();

            if(floatval($request['monto']) > $sucursales->saldo($request['sucursalID'])){
                return response()->json('No se cuenta con el saldo suficiente para realizar este vale', 400);
            }

            $consultas->start();
            $egreso = $egresos->nuevoEgreso(
                'Vale',
                $request['monto'],
                $request['observaciones'],
                2,
                2,
                $request['sucursalID'],
                $request['idCalendario'],
                1,
                $request['usuarioID'],
                4,
                $request['idNivel'],
                $folios->proximoEgreso($request['idNivel'], $request['idCalendario'], $request['sucursalID']),
                0,
                ''
            );

            $vale = $funciones->crearVale(
                $request['sucursalID'],
                $request['monto'],
                $request['idCalendario'],
                $request['observaciones'],
                $request['usuarioID'],
                $egreso->id,
                $folios->proximoVale($request['sucursalID'], $request['idCalendario']),
                $request['idNivel']
            );
            $consultas->commit();
            return response()->json($vale, 200);
        }catch(Exception $e){
            $consultas->rollback();
            return response()->json('Error de servidor', 400);
        }
    }

    function creados(Request $request){
        try {
            $funciones = new Vales();
            $respuesta = array(
                'datos' => $funciones->creados($request['sucursalID']),
                'listas' => $funciones->listas() 
            );
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function recibidos(){
        try {
            $funciones = new Vales();
            return response()->json($funciones->recibidos(), 200);
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

    function modificar(Request $request){
        try{
            $vale = Vale::find($request['id']);
            $vale->monto = $request['monto'];
            $vale->observaciones = $request['observaciones'];
            $vale->idSucursalEntrada = $request['idSucursalEntrada'];
            $vale->idCalendario = $request['idCalendario'];
            $vale->save();

            $egreso = Egreso::find($vale->idEgreso);
            $egreso->monto = $vale->monto;
            $egreso->save();

            return response()->json($request, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }

    function eliminar(Request $request){
        try{
            $vale = Vale::find($request['id']);
            $vale->eliminado = 1;
            $vale->save();

            return response()->json($vale, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }

    function aceptar(Request $request){
        try{
            $funciones = new Vales();
            $ingreso = $funciones->crearIngreso($request);
            $vale = Vale::find($request['id']);   
            $vale->idUsuarioAcepto = $request['usuarioID'];
            $vale->idIngreso = $ingreso->id;
            $vale->idSucursalEntrada = $request['idRecepcion'];
            $vale->aceptado = 1;
            $vale->save();
            return response()->json($vale, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }

    function rechazar(Request $request){
        try{
            $vale = Vale::find($request['id']);
            $vale->aceptado = 2;
            $vale->save();

            return response()->json($vale, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }
}