<?php

namespace App\Http\Controllers;
use App\Clases\Fichas;
use App\Clases\Metasingresos;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MetasingresosController extends BaseController
{
    function mostrar(Request $request){
        try {
            $funciones = new Metasingresos();
            return response()->json(array(
                'listas' => $funciones->listas(),
                'datos' => $funciones->mostrar($request['calendarioID']) 
            ), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevo(Request $request){
        try {
            $funciones = new Metasingresos();
            if($funciones->existe($request['idCalendario'], $request['idSucursal'], $request['mes'])){
                return response()->json('Ya existe una meta para el calendario, sucursal y mes seleccionados', 400);
            }
            $meta = $funciones->nuevo($request['idCalendario'], $request['idSucursal'], $request['mes'], $request['meta']);
            return response()->json($funciones->completar($meta->id), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try {
            $funciones = new Metasingresos();
            $meta = $funciones->modificar($request['id'], $request['meta']);
            return response()->json($funciones->completar($meta->id), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try {
            $funciones = new Metasingresos();
            return response()->json($funciones->eliminar($request['id']), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function metas(Request $request){
        try {
            $funciones = new Metasingresos();
            if(intval($request['sucursalID']) === 1){
                $metas = $funciones->metasCalendario($request['calendarioID']);
                return response()->json($funciones->ventas($metas), 200);
            }else{
                return response()->json('No se pueden mostrar las estadisticas en esta sucursal', 400);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}