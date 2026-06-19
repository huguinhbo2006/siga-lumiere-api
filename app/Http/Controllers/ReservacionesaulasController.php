<?php

namespace App\Http\Controllers;
use App\Clases\Reservaciones;
use App\Clases\Paridades;
use App\Clases\Grupos;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class ReservacionesaulasController extends BaseController
{
    function mostrar(Request $request){
        try{
            $funciones = new Reservaciones();
            $grupos = new Grupos();
            $listaGrupos = $grupos->calendario($request['sucursalID'], $request['calendarioID']);
            $respuesta = array(
                'datos' => $funciones->aulas($listaGrupos, $request['sucursalID']),
                'listas' => $funciones->listas($request['sucursalID']) 
            );
            return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function reservar(Request $request){
        try{
            $grupos = new Grupos();
            $paridades = new Paridades();
            $funciones = new Reservaciones();

            if($funciones->existe($request['idAula'], $request['idGrupo']['idCalendario'], $request['sucursalID'], $request['idGrupo']['id'])){
                return response()->json('El aula ya fue reservada por este grupo', 400);
            }
            $paridad = $grupos->paridad($request['idGrupo']['id']);
            $cursos = $paridades->cursos($paridad);
            $datos = array();
            foreach ($cursos as $curso) {
                $dato = $grupos->grupo($request['idGrupo']['idNivel'], $request['idGrupo']['idSubnivel'], $request['idGrupo']['idCalendario'], $request['idGrupo']['idModalidad'], $request['idGrupo']['idCategoria'], $request['idGrupo']['idSede'], $request['idGrupo']['idTurno'], $request['idGrupo']['idHorario'], $curso->id);
                if(!is_null($dato))
                    $datos[] = $dato;
            }

            foreach ($datos as $dato) {
                $funciones->reservar($request['idAula'], $request['idGrupo']['idCalendario'], $request['sucursalID'], $dato->id);
            }

            return response()->json($datos, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function reservadas(Request $request){
        try {
            $funciones = new Reservaciones();

            return response()->json($funciones->reservadas($request['idGrupo'], $request['sucursalID'], $request['idCalendario']), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try {
            $gruposParidades = traerGruposParidad($request['idGrupo']);
            foreach ($gruposParidades as $grupo) {
                $reservacion = Reservacionesaula::where('idAula', '=', $request['idAula'])->
                                                  where('idSucursal', '=', $request['idSucursal'])->
                                                  where('idGrupo', '=', $grupo)->get();
                $eliminar = Reservacionesaula::find($reservacion[0]->id);
                $eliminar->delete();
            }

            return response()->json($reservacion, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function listas() {
        try {
            $funciones = new Reservaciones();
            return response()->json($funciones->listas(0), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function horarios(Request $request) {
        try {
            $funciones = new Reservaciones();
            $paridades = new Paridades();
            $horarios = $paridades->horarios($request['idParidad'], $request['idCalendario']);
            $cupos = $paridades->cupos($request['idParidad'], $request['idCalendario'], $request['idSucursal']);
            $respuesta = $paridades->control($horarios, $cupos);

            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function liberar(Request $request){
        try {
            $funciones = new Reservaciones();
            return response()->json($funciones->liberar($request['id']), 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }
}