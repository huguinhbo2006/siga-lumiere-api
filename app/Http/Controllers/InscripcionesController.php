<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Clases\Inscripciones;
use App\Clases\Consultas;
use App\Clases\Alumnos;
use App\Clases\CRM;
use App\Clases\Folios;

class InscripcionesController extends BaseController
{

    function nuevo(Request $request){
        try{
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

            //Verificar existencia de seguimientos en crm
            if($crm->hayProspecto($request['alumno'])){
                $prospecto = $crm->obtenerProspecto($request['alumno']);
                if($crm->haySeguimientoProspecto($prospecto->id)){
                    $seguimiento = $crm->obtenerSeguimientoProspecto($prospecto->id);
                    $informacionID = $seguimiento->idUsuario;
                }
            }else if(isset($request['idSeguimiento'])){
                $seguimiento = $crm->obtenerSeguimiento($request['idSeguimiento']);
                $informacionID = $seguimiento->idUsuario;
            }else{
                $informacionID = $request['usuarioID'];
            }

            //Alta de alumno
            $respuesta['alumno'] = $funciones->nuevoAlumno($request['alumno']);
            if(is_null($respuesta['alumno']))
                return response()->json('Error al crear el alumno', 400);

            //Alta de tutor
            $datosTutor = $request['tutor'];
            $datosTutor['idAlumno'] = $respuesta['alumno']->id;
            $respuesta['tutor'] = $funciones->nuevoTutor($datosTutor);
            if(is_null($respuesta['tutor']))
                return response()->json('Error al crear el tutor', 400);

            //Alta de domicilio de alumno
            $datosDomicilio = $request['domicilio'];
            $datosDomicilio['idAlumno'] = $respuesta['alumno']->id;
            $respuesta['domicilio'] = $funciones->nuevoDomicilioAlumno($datosDomicilio);
            if(is_null($respuesta['domicilio']))
                return response()->json('Error al crear el domicilio del alumno', 400);

            //Alta de la ficha
            $datosInscripcion = $request['inscripcion'];
            $datosInscripcion['idAlumno'] = $respuesta['alumno']->id;
            $datosInscripcion['intentos'] = $request['escolares']['intentos'];
            $datosInscripcion['idSucursalInscripcion'] = $request['idSucursalInscripcion'];
            $datosInscripcion['idUsuario'] = $request['usuarioID'];
            $datosInscripcion['idUsuarioInformacion'] = (intval($informacionID) === 0) ? $request['usuarioID'] : $informacionID;
            $respuesta['ficha'] = $funciones->nuevaFicha($datosInscripcion);
            if(is_null($respuesta['ficha']))
                return response()->json('Error al crear la ficha', 400);
            //Alta de datos publicitarios
            $datosPublicitarios = $request['publicitarios'];
            $datosPublicitarios['idMedioContacto'] = (isset($seguimiento->idMedioContacto)) ? $seguimiento->idMedioContacto :  $datosPublicitarios['idMedioContacto'];
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

            //Alta de datos escolares
            $datosEscolares['idSubnivel'] = $request['inscripcion']['idSubnivel'];
            $datosEscolares['idAlumno'] = $respuesta['alumno']->id;
            $respuesta['escolares'] = $funciones->nuevoEscolares($datosEscolares);
            if(is_null($respuesta['escolares']))
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
        }catch(Exception $e){
            $consultas->rollback();
            return response()->json('Error de servidor', 400);
        }
    }

    function existeAlumno(Request $request){
        try {
            $codigoAlumno = substr($request['nombre'], 0, 2).substr($request['apellidoPaterno'],0 ,2).substr($request['apellidoMaterno'],0 ,2).$request['fechaNacimiento'];
            $codigoAlumno = str_replace('-', '', $codigoAlumno);
            $codigoAlumno = strtoupper($codigoAlumno);
            $consulta = "SELECT * FROM alumnos WHERE codigo LIKE '%$codigoAlumno%' AND eliminado = 0";
            $existe = DB::select($consulta, array());
            $resultado = array();
            $respuesta = array();
            foreach ($existe as $alumno) {
                $resultado['id'] = $alumno->id;
                $resultado['nombre'] = $alumno->nombre.' '.$alumno->apellidoPaterno.' '.$alumno->apellidoMaterno;
                $respuesta[] = $resultado;
            }
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try{
            $funciones = new Inscripciones();
            return response()->json(
                array(
                    'fichas' => $funciones->fichas($request['calendarioID'], $request['sucursalID']),
                    'grupos' => $funciones->grupos(),
                    'listas' => array(
                        'alumnos' => $funciones->listasAlumnos(),
                        'inscripcion' => $funciones->listasInscripcion(),
                        'domicilio' => $funciones->listasDomicilio(),
                        'escolares' => $funciones->listasEscolares(),
                        'publicitarios' => $funciones->listasPublicitarios(),
                        'cuenta' => $funciones->listasCuenta()
                    ),
                    'cupos' => $funciones->cupos(),
                    'codigos' => $funciones->codigos()
                )
            , 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }

    function cupo(Request $request){
        try {
            $paridad = Cursosparidade::where('idCurso', '=', $request['idCurso'])->get();
            $cupo = 0;
            $inscritos = 0;
            $lugares = 0;
            $reservaciones = Reservacionesaula::where('idGrupo', '=', $request['idGrupo'])->
                                                where('idSucursal', '=', $request['idSucursal'])->get();
            if(count($reservaciones) < 1){
                return response()->json('No se ha reservado un aula para este grupo', 400);
            }
            
            foreach ($reservaciones as $reservacion) {
                $aula = Aula::find($reservacion->idAula);
                $cupo = intval($cupo) + intval($aula->cupo);
            }

            $paridadesGrupo = traerGruposParidad($request['idGrupo']);
            foreach ($paridadesGrupo as $grupo) {
                $fichas = Ficha::where('idSucursalImparticion', '=', $request['idSucursal'])->
                                 where('idGrupo', '=', $grupo)->
                                 where('estatus', '=', 1)->get();
                $inscritos = intval($inscritos) + intval(count($fichas));
            }
            $respuesta['cupo'] = $cupo;
            $respuesta['inscritos'] = $inscritos;
            $respuesta['lugares'] = (intval($cupo) - intval($inscritos));
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function horarioBloqueado(Request $request){
        try {
            $bloqueo = Bloqueohorario::where('idGrupo', '=', $request['idGrupo'])->where('idSucursal', '=', $request['idSucursal'])->get();
            if(count($bloqueo) > 0){
                return response()->json('Horario bloqueado', 400);
            }else{
                return response()->json($bloqueo, 200);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function canjearCupon(Request $request){
        try {
            $total = $request['total'];
            $posible = strtoupper($request['cupon']);
            $cupones = Cupone::where('cupon', '=', $posible)->
                            where('eliminado', '=', 0)->
                            where('activo', '=', 1)->
                            where('cantidad', '>', 0)->get();
            if(count($cupones) > 0){
                $descuento = Cupone::find($cupones[0]->id);
                $descuento->cantidad = intval($descuento->cantidad) - 1;
                $descuento->save();
                return response()->json($cupones[0], 200);
            }else{
                return response()->json('Cupon agotado', 400);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    //Listas Componentes

    function listasInscripcion(){
        try {
            $hoy = Carbon::now();
            $consulta = "SELECT * FROM calendarios WHERE fin > NOW() AND eliminado = 0 AND activo = 1";
            $calendarios = DB::select($consulta, array());
            $niveles = Nivele::where('eliminado', '=', 0)->get();
            $subniveles = Subnivele::where('eliminado', '=', 0)->get();
            $categorias = Categoria::where('eliminado', '=', 0)->get();
            $modalidades = Modalidade::where('eliminado', '=', 0)->get();
            $cursos = Curso::where('eliminado', '=', 0)->get();
            $sedes = Sede::where('eliminado', '=', 0)->get();
            $turnos = Turno::where('eliminado', '=', 0)->get();
            $selectHorarios = "SELECT CONCAT(inicio, ' - ', fin) as nombre, id FROM horarios WHERE eliminado = 0";
            $horarios = DB::select($selectHorarios, array());
            $sucursales = Sucursale::where('eliminado', '=', 0)->get();
            $sedessucursales = Sedesucursale::where('eliminado', '=', 0)->get();
            $grupos = Grupo::join('altacursos', 'idAltaCurso', '=', 'altacursos.id')->
                      join('calendarios', 'altacursos.idCalendario', '=', 'calendarios.id')->
                      select(
                        'grupos.id as id',
                        'altacursos.idCalendario',
                        'altacursos.idNivel',
                        'altacursos.idSubnivel',
                        'altacursos.idCategoria',
                        'altacursos.idModalidad',
                        'altacursos.idSede',
                        'altacursos.idCurso',
                        'altacursos.inicio',
                        'altacursos.fin',
                        'altacursos.limitePago',
                        'altacursos.precio',
                        'grupos.idHorario', 
                        'grupos.idTurno')->
                      where('calendarios.fin', '>', $hoy)->get();
                        
            $listas['calendarios'] = $calendarios;
            $listas['niveles'] = $niveles;
            $listas['subniveles'] = $subniveles;
            $listas['categorias'] = $categorias;
            $listas['modalidades'] = $modalidades;
            $listas['cursos'] = $cursos;
            $listas['sedes'] = $sedes;
            $listas['turnos'] = $turnos;
            $listas['horarios'] = $horarios;
            $listas['sucursales'] = $sucursales;
            $listas['sedesucursales'] = $sedessucursales;
            $listas['grupos'] = $grupos;
            return response()->json($listas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function listasConceptos(){
        $metodosPago = Metodospago::where('eliminado', '=', 0)->get();
        $formasPago = Formaspago::where('eliminado', '=', 0)->get();
        $bancos = Banco::where('eliminado', '=', 0)->get();
        $cuentas = Cuenta::where('eliminado', '=', 0)->get();
        $conceptosAbonos = Conceptosabono::where('eliminado', '=', 0)->get();
        $conceptosCargos = Conceptoscargo::where('eliminado', '=', 0)->get();
        $conceptosDescuentos = Conceptosdescuento::where('eliminado', '=', 0)->get();
        $tiposPago = Tipopago::where('eliminado', '=', 0)->get();

        $listas['metodosPago'] = $metodosPago;
        $listas['formasPago'] = $formasPago;
        $listas['bancos'] = $bancos;
        $listas['cuentas'] = $cuentas;
        $listas['conceptosAbonos'] = $conceptosAbonos;
        $listas['conceptosCargos'] = $conceptosCargos;
        $listas['conceptosDescuentos'] = $conceptosDescuentos;
        $listas['tiposPago'] = $tiposPago;

        return response()->json($listas, 200);
    }

    function listasComponenteAlumno(){
        try {
            $sexos = Sexo::where('eliminado', '=', 0)->get();
            $listas['sexos'] = $sexos;
            return response()->json($listas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function listasComponenteDomicilio(){
        try {
            $estados = Estado::where('eliminado', '=', 0)->get();
            $municipios = Municipio::where('eliminado', '=', 0)->get();

            $listas['estados'] = $estados;
            $listas['municipios'] = $municipios;
            return response()->json($listas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function listasComponenteEscolares(){
        try {
            $hoy = Carbon::now();
            $tipoEscuelas = Tipoescuela::where('eliminado', '=', 0)->get();
            $escuelas = Escuela::where('eliminado', '=', 0)->get();
            $universidades = Universidade::where('eliminado', '=', 0)->get();
            $centrosUniversitarios = Centrosuniversitario::where('eliminado', '=', 0)->get();
            $carreras = Carrera::join('calendarios', 'idCalendario', '=', 'calendarios.id')->
            select('carreras.*')->
            where('calendarios.fin', '>', $hoy)->
            where('carreras.eliminado', '=', 0)->get();
            $estados = Estado::where('eliminado', '=', 0)->get();
            $municipios = Municipio::where('eliminado', '=', 0)->get();

            $listas['tipoEscuelas'] = $tipoEscuelas;
            $listas['escuelas'] = $escuelas;
            $listas['universidades'] = $universidades;
            $listas['centrosUniversitarios'] = $centrosUniversitarios;
            $listas['carreras'] = $carreras;
            $listas['estados'] = $estados;
            $listas['municipios'] = $municipios;
            return response()->json($listas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function listasComponentePublicitarios(){
        try {
            $mediosContacto = Medioscontacto::where('eliminado', '=', 0)->get();
            $mediosPublicitarios = Mediospublicitario::where('eliminado', '=', 0)->get();
            $viasPublicitarias = Viaspublicitaria::where('eliminado', '=', 0)->get();
            $motivosInscripcion = Motivosinscripcione::where('eliminado', '=', 0)->get();
            $motivosBachillerato = Motivosbachillerato::where('eliminado', '=', 0)->get();
            $campanias = Campania::where('eliminado', '=', 0)->get();
            $empresasCursos = Empresascurso::where('eliminado', '=', 0)->get();

            $listas['mediosContacto'] = $mediosContacto;
            $listas['mediosPublicitarios'] = $mediosPublicitarios;
            $listas['viasPublicitarias'] = $viasPublicitarias;
            $listas['motivosInscripcion'] = $motivosInscripcion;
            $listas['motivosBachillerato'] = $motivosBachillerato;
            $listas['campanias'] = $campanias;
            $listas['empresasCursos'] = $empresasCursos;
            return response()->json($listas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}