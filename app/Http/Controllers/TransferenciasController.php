<?php

namespace App\Http\Controllers;
use App\Clases\Transferencias;
use App\Clases\Consultas;
use App\Clases\Sucursales;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferenciasController extends BaseController
{
    function nuevo(Request $request){
        try{
            $funciones = new Transferencias();
            $sucursales = new Sucursales();
            if($request['monto'] > $sucursales->saldo($request['sucursalID'])){
                return response()->json("No se cuenta con el saldo suficiente para realizar este vale", 400);
            }
            $transferencia = $funciones->nueva($request['monto'], $request['idCalendario'], $request['idNivel'], $request['idSucursal'], $request['usuarioID'], $request['sucursalID']);
            $transferencia = $funciones->completar($transferencia);
            

            return response()->json($transferencia, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }

    function creadas(Request $request){
        try {
            $funciones = new Transferencias();
            $respuesta = array(
                'datos' => $funciones->creadas($request['calendarioID']),
                'listas' => $funciones->listas() 
            );
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function recibidas(Request $request){
        try {
            $funciones = new Transferencias();
            return response()->json($funciones->recibidas($request['sucursalID']), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try{
            $transferencia = Transferencia::find($request['id']);
            $transferencia->monto = $request['monto'];
            $transferencia->idSucursalEntrada = $request['idSucursalEntrada'];
            $transferencia->idCalendario = $request['idCalendario'];
            $transferencia->idNivel = $request['idNivel'];
            $transferencia->save();

            return response()->json($transferencia, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }

    function eliminar(Request $request){
        try{
            $transferencia = Transferencia::find($request['id']);
            $transferencia->delete();

            return response()->json($transferencia, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }

    function aceptar(Request $request){
        try{
            $funciones = new Transferencias($request['id']);
            $consultas = new Consultas();

            $consultas->start();
            $ingreso = $funciones->agregarIngreso($request['monto'], $request['sucursalID'], $request['usuarioID'], $request['id']);

            $egreso = $funciones->agregarEgreso($request['monto'], $request['idSucursalSalida'], $request['idUsuarioCreo'], $request['id']);

            $dato = $funciones->aceptarTransferencia($ingreso->id, $egreso->id, $request['usuarioID'], $request['id']);

            $consultas->commit();
            return response()->json($dato, 200);
        }catch(Exception $e){
            $consultas->rollback();
            return response()->json('Error de servidor', 400);
        }
    }

    function rechazar(Request $request){
        try{
            $funciones = new Transferencias();

            return response()->json($funciones->rechazarTransferencia($request['id']), 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }
}