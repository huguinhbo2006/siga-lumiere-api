<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Clases\Alumnos;
use App\Clases\Fichas;
use App\Clases\Inscripciones;
use App\Clases\Consultas;
use App\Clases\Cupones;
use App\Clases\CRM;
use App\Clases\Datospublicitarios;
use App\Clases\Datosaspiraciones;
use App\Alumno;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlumnosController extends BaseController
{
    function buscar(Request $request){
        try {
            $funciones = new Alumnos();
            return response()->json($funciones->buscar($request['busqueda']), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function datos(Request $request){
        try {
            $funciones = new Alumnos();

            return response()->json(
                array(
                    'listas' => $funciones->listas(),
                    'datos' => array(
                        'nombre' => $funciones->nombre($request['id']),
                        'generales' => $funciones->personales($request['id']),
                        'tutor' => $funciones->tutor($request['id']),
                        'domicilio' => $funciones->domicilio($request['id']),
                        'escolares' => $funciones->escolares($request['id'])
                    )
            ), 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarPersonales(Request $request){
        try {
            $funciones = new Alumnos();
            return response()->json($funciones->modificarPersonales($request['id'], $request['telefono'], $request['celular'], $request['correo'], $request['fechaNacimiento']), 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarTutor(Request $request){
        try {
            $funciones = new Alumnos();
            return response()->json($funciones->modificarTutor($request['id'], $request['telefono'], $request['celular'], $request['nombre']), 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarDomicilio(Request $request){
        try {
            $funciones = new Alumnos();
            return response()->json($funciones->modificarDomicilio($request['id'], $request['calle'], $request['numeroExterior'], $request['numeroInterior'], $request['colonia'], $request['codigoPostal'], $request['idEstado'], $request['idMunicipio']), 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function fichas(Request $request){
        try {
            $funciones = new Alumnos();
            $publicitarios = new Datospublicitarios();
            $aspiracion = new Datosaspiraciones();
            $fichas = $funciones->fichas($request['id']);
            $respuesta = array(
                'datos' => $funciones->completarFichas($fichas),
                'listas' => array(
                    'publicitarios' => $publicitarios->listas(),
                    'aspiracion' => $aspiracion->listas()
                )
            );
            return response()->json($respuesta, 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function actualizarNumeroRegistro(Request $request){
        try {
            $fichas = new Fichas();
            return response()->json($fichas->actualizarNumeroRegistro($request['id'], $request['registro']), 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function actualizarEstatusFicha(Request $request){
        try {
            $consultas = new Consultas();
            $fichas = new Fichas();
            $consultas->start();
            $dato = $fichas->actualizarEstatus($request['id'], $request['estatus']);
            if(intval($request['estatus']) === 3){
                $cupones = new Cupones();
                $cupon = $cupones->nuevo($request['monto'], $request['usuarioID'], 1, $request['id'], 'AD'.$cupones->todos());
            }
            if(intval($request['estatus']) === 1){
                if(!$fichas->activarEstadoCuenta($request['id'])){
                    $consultas->rollback();
                    return response()->json('Error al activar el estado de cuenta', 400);
                }
            }else{
                if(!$fichas->desactivarEstadoCuenta($request['id'])){
                    return response()->json('Error al desactivar el estado de cuenta', 400);
                }
            }
            $consultas->commit();
            return response()->json($dato, 200);
        } catch (Exception ) {
            $consultas->rollback();
            return response()->json('Error en el servidor', 400);
        }
    }

    function actualizarDatosPublicitarios(Request $request){
        try {
            $publicitarios = new Datospublicitarios();
            return response()->json($publicitarios->modificar($request['id'], $request['idMedioContacto'], $request['idMedioPublicitario'], $request['idViaPublicitaria'], $request['idMotivoInscripcion'], $request['idCampania'], $request['idMotivoBachillerato'], $request['idEmpresa'], $request['curso']), 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function actualizarDatosAspiracion(Request $request){
        try {
            $aspiraciones = new Datosaspiraciones();
            return response()->json($aspiraciones->modificar($request['id'], $request['idUniversidad'], $request['idCentroUniversitario'], $request['idCarrera']), 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traer(Request $request){
        try {
            $inscripciones = new Inscripciones();

            $respuesta = array(
                'grupos' => $inscripciones->grupos(),
                'listas' => array(
                    'alumnos' => $inscripciones->listasAlumnos(),
                    'inscripcion' => $inscripciones->listasInscripcion(),
                    'domicilio' => $inscripciones->listasDomicilio(),
                    'escolares' => $inscripciones->listasEscolares(),
                    'publicitarios' => $inscripciones->listasPublicitarios(),
                    'cuenta' => $inscripciones->listasCuenta()
                ),
                'cupos' => $inscripciones->cupos(),
                'codigos' => $inscripciones->codigos()
            );
            return response()->json($respuesta, 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function inscripcion(Request $request){
        try {
            $funciones = new Inscripciones();
            $consultas = new Consultas();
            $crm = new CRM();
            $informacionID = 0;
            $respuesta = array();
            $seguimiento = array();


            $consultas->start();

            if($funciones->existeBloqueo($request['inscripcion']['idGrupo'], $request['inscripcion']['idSucursalImparticion'])){
                return response()->json('Este grupo ha sido bloqueado en esta sucursal', 400);
            }

            //Alta de la ficha
            $datosInscripcion = $request['inscripcion'];
            $datosInscripcion['idAlumno'] = $request['idAlumno'];
            $datosInscripcion['intentos'] = $request['escolares']['intentos'];
            $datosInscripcion['idSucursalInscripcion'] = $request['sucursalID'];
            $datosInscripcion['idUsuario'] = $request['usuarioID'];
            $datosInscripcion['idUsuarioInformacion'] = $request['usuarioID'];
            $respuesta['ficha'] = $funciones->nuevaFicha($datosInscripcion);
            if(is_null($respuesta['ficha']))
                return response()->json('Error al crear la ficha', 400);

            //Alta de datos publicitarios
            $datosPublicitarios = $request['publicitarios'];
            $datosPublicitarios['idMedioContacto'] = (count($seguimiento) > 0) ? $seguimiento->idMedioContacto :  $datosPublicitarios['idMedioContacto'];
            $datosPublicitarios['idFicha'] = $respuesta['ficha']->id;
            $respuesta['publicitarios'] = $funciones->nuevoPublicitarios($datosPublicitarios);
            if(is_null($respuesta['publicitarios']))
                return response()->json('Error al guardar los datos publicitarios', 400);

            //Alta de datos de aspiracion
            $datosEscolares = $request['escolares'];
            $datosEscolares['idFicha'] = $respuesta['ficha']->id;
            $respuesta['aspiracion'] = $funciones->nuevoAspiracion($datosEscolares);
            if(is_null($respuesta['aspiracion']))
                return response()->json('Error al guardar los datos publicitarios', 400);

            //Alta de cargos
            $cargos = $request['cuenta']['cargos'];
            $respuesta['cargos'] = $funciones->agregarCargos($cargos, $respuesta['ficha']->id, $request['usuarioID']);
            if(count($cargos) === null){
                if($respuesta['cargos'] < 1){
                    return response()->json('Error al guardar los cargos', 400);
                }
            }

            //Alta de abonos
            $abonos = $request['cuenta']['abonos'];
            $respuesta['abonos'] = $funciones->agregarAbonos($abonos, $respuesta['ficha']->id, $request['inscripcion'], (intval($request['sucursalID']) === 1) ? $respuesta['ficha']->idSucursalImparticion : $request['sucursalID'], $request['usuarioID']);
            if(count($abonos) > 1){
                if($respuesta['abonos'] === null){
                    return response()->json('Error al guardar los cargos', 400);
                }
            }

            //Alta de descuentos
            $descuentos = $request['cuenta']['descuentos'];
            $respuesta['descuentos'] = $funciones->agregarDescuentos($descuentos, $respuesta['ficha']->id, $request['usuarioID']);
            if(count($abonos) > 1){
                if($respuesta['descuentos'] === null){
                    return response()->json('Error al guardar los cargos', 400);
                }
            }

            $consultas->commit();

            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function alumno(Request $request){
        try {
            $alumno = Alumno::find($request['id']);
            return response()->json($alumno, 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarNombre(Request $request){
        try {
            $alumno = Alumno::find($request['id']);
            $alumno->nombre = $request['nombre'];
            $alumno->apellidoPaterno = $request['apellidoPaterno'];
            $alumno->apellidoMaterno = $request['apellidoMaterno'];
            $alumno->codigo = $request['codigo'];
            $alumno->save();
            return response()->json($alumno, 200);
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }
}