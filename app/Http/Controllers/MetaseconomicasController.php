<?php

namespace App\Http\Controllers;

use App\Metaseconomica;
use App\Clases\Metaseconomicas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MetaseconomicasController extends Controller
{
    function mostrar(Request $request){
        try {
            $funciones = new Metaseconomicas();

            return response()->json(array(
                'listas' => $funciones->listas(),
                'datos' => $funciones->mostrar($request['calendarioID']) 
            ), 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevo(Request $request){
        try {
            $funciones = new Metaseconomicas();
            if($funciones->existe($request['idUsuario'], $request['idCalendario'], $request['mes'], $request['meta'])){
                $dato = $funciones->nuevo($request['idUsuario'], $request['idCalendario'], $request['mes'], $request['meta']);
                return response()->json($funciones->completar($dato->id), 200);
            }else{
                return response()->json('El usuario ya tiene un registro en el mes y calendario ingresado', 400);
            }
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try {
            $funciones = new Metaseconomicas();
            $dato = $funciones->modificar($request['id'], $request['meta']);
            return response()->json($funciones->completar($dato->id), 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traer(Request $request){
        try {
            $funciones = new Metaseconomicas();
            return response()->json($funciones->traer($request['idUsuario'], $request['calendarioID']), 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function obtener(Request $request){
        try {
            $funciones = new Metaseconomicas();
            return response()->json($funciones->obtener($request['calendarioID']), 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }
}