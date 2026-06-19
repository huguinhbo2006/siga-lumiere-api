<?php

namespace App\Http\Controllers;
use App\Bloqueohorario;
use App\Clases\Bloqueos;
use Carbon\Carbon;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BloqueohorariosController extends BaseController
{
    function bloquear(Request $request){
        try {
            $funciones = new Bloqueos();
            $bloqueo = $funciones->bloquear($request['id'], $request['sucursalID']);
            return response()->json($bloqueo, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function desbloquear(Request $request){
        try {
            $funciones = new Bloqueos();
            $desbloqueo = $funciones->desbloquear($request['id']);
            return response()->json($desbloqueo, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $funciones = new Bloqueos();
            return response()->json($funciones->listas($request['sucursalID']), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}