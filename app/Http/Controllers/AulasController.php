<?php

namespace App\Http\Controllers;
use App\Clases\Aulas;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class AulasController extends BaseController
{
    function nuevo(Request $request){
        try{
            $funciones = new Aulas();
            return response()->json($funciones->nueva($request['nombre'], $request['cupo'], $request['sucursalID']), 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $funciones = new Aulas();
            return response()->json($funciones->traer($request['sucursalID']), 200);
        } catch (Exception $e) {
            return response()->json('Error de servidor', 400);
        }
    }

    function activos(Request $request){
        try {
            $funciones = new Aulas();
            return response()->json($funciones->activos($request['sucursalID']), 200);
        } catch (Exception $e) {
            return response()->json('Error de servidor', 400);
        }
    }

    function activar(Request $request){
        try{
            $funciones = new Aulas();
            return response()->json($funciones->activar($request['id']), 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function desactivar(Request $request){
        try{
            $funciones = new Aulas();
            return response()->json($funciones->desactivar($request['id']), 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try{
            $funciones = new Aulas();
            return response()->json($funciones->eliminar($request['id']), 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try{
            $funciones = new Aulas();
            return response()->json($funciones->modificar($request['id'], $request['nombre'], $request['cupo']), 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }
}