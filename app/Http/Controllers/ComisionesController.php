<?php

namespace App\Http\Controllers;
use App\Clases\Comisiones;
use Carbon\Carbon;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComisionesController extends BaseController
{
    function mostrar(){
        try {
            $funciones = new Comisiones();
            return response()->json($funciones->listas(), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function comisiones(Request $request){
        try {
            $funciones = new Comisiones();
            $comisiones = $funciones->comisiones($request['mes'], $request['year'], $request['idEmpleado']);
            $calculadas = $funciones->calcular($comisiones);
            return response()->json(
                array(
                    'comisiones' => $calculadas,
                    'total' => $funciones->comisionTotal($calculadas)
                )
                , 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traer(){
        try {
            $funciones = new Comisiones();
            return response(
                array(
                    'listas' => $funciones->listas(),
                    'datos' => $funciones->comisionesActuales()
            ) , 200);
        } catch (Exception $e) {
            return null;
        }
    }

    function nuevo(Request $request){
        try {
            $funciones = new Comisiones();
            if($funciones->existeComisionCurso($request['idCalendario'], $request['idCurso'])){
                return response()->json('Ya existe comision para este curso en este calendario', 400);
            }
            $comision = $funciones->nuevaComisionCurso($request['idCalendario'], $request['idCurso'], $request['tipo'], $request['comision']);
            return response()->json($funciones->formatearComisionCurso($comision), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try {
            $funciones = new Comisiones();
            $comision = $funciones->modificarComisionCurso($request['id'], $request['tipo'], $request['comision']);
            return response()->json($funciones->formatearComisionCurso($comision), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try {
            $funciones = new Comisiones();
            return response()->json($funciones->eliminarComisionCurso($request['id']), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}