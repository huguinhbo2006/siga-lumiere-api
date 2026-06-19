<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Clases\Fichas;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FichasController extends BaseController
{
    function traer(Request $request){
        try{
            $funciones = new Fichas();
            return response()->json(array(
                'ficha' => $funciones->ficha($request['id']),
                'alta' => $funciones->grupo($request['id']),
                'listas' => $funciones->listas() 
            ), 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function actualizar(Request $request){
        try {
            $funciones = new Fichas();
            return response()->json($funciones->modificar(
                $request['id'],
                $request['idGrupo'],
                $request['observaciones'],
                $request['idCalendario'],
                $request['idSucursalImparticion']), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function catalogos(){
        try {
            $respuesta = array();
            $metodos = Metodospago::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $respuesta['metodos'] = $metodos;
            $formas = Formaspago::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $respuesta['formas'] = $formas;
            $cuentas = Cuenta::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $respuesta['cuentas'] = $cuentas;
            $bancos = Banco::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $respuesta['bancos'] = $bancos;
            $tiposPago = Tipopago::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $respuesta['tiposPago'] = $tiposPago;

            $cg = Conceptoscargo::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $respuesta['cg'] = $cg;
            $ca = Conceptosabono::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $respuesta['ca'] = $ca;
            $cd = Conceptosdescuento::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $respuesta['cd'] = $cd;
            $ce = Conceptosextra::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $respuesta['ce'] = $ce;
            $cs = Conceptosdevolucione::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $respuesta['cs'] = $cs;

            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadoCuenta(Request $request){
        try {
            $respuesta = array();
            $elementos = array();
            
            $cargos = Alumnocargo::where('idFicha', '=', $request['idFicha'])->
            where('eliminado', '=', 0)->get();
            foreach ($cargos as $cargo) {
                $cargo->existe = true;
                $elementos[] = $cargo;
            }
            $cargos = $elementos;
            $elementos = [];

            $abonos = Alumnoabono::where('idFicha' , '=', $request['idFicha'])->
            where('eliminado', '=', 0)->get();
            foreach ($abonos as $abono) {
                $abono->existe = true;
                $elementos[] = $abono;
            }
            $abonos = $elementos;
            $elementos = [];

            $descuentos = Alumnodescuento::where('idFicha', '=', $request['idFicha'])->
            where('eliminado', '=', 0)->get();
            foreach ($descuentos as $descuento) {
                $descuento->existe = true;
                $elementos[] = $descuento;
            }
            $descuentos = $elementos;
            $elementos = [];

            $devoluciones = Alumnodevolucione::where('idFicha', '=', $request['idFicha'])->
            where('eliminado', '=', 0)->get();
            foreach ($devoluciones as $devolucion) {
                $devolucion->existe = true;
                $elementos[] = $devolucion;
            }

            $extras = Alumnoextra::where('idFicha', '=', $request['idFicha'])->
            where('eliminado', '=', 0)->get();
            foreach ($extras as $extra) {
                $extra->existe = true;
                $elementos[] = $extra;
            }

            $fichaa = Ficha::find($request['idFicha']);

            $respuesta['estatus'] = $fichaa->estatus;
            $respuesta['cargos'] = $cargos;
            $respuesta['abonos'] = $abonos;
            $respuesta['descuentos'] = $descuentos;
            $respuesta['devoluciones'] = $devoluciones;
            $respuesta['extras'] = $extras;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function agregarCupon(Request $request){
        try {
            $ficha = Ficha::find($request['idFicha']);
            /*if(intval($ficha->idSucursalImparticion) !== intval($request['sucursal']) && intval($ficha->idSucursalInscripcion) !== intval($request['sucursal']) && intval($ficha->idUsuario) !== $request['idUsuario']){
                return response()->json('No cuentas con permisos para modificar esta ficha', 400);
            }*/
            $total = $request['total'];
            $posible = strtoupper($request['cupon']);
            $cupones = Cupone::where('cupon', '=', $posible)->
                            where('eliminado', '=', 0)->
                            where('activo', '=', 1)->
                            where('cantidad', '>', 0)->get();
            if(count($cupones) > 0){
                $cupon = $cupones[0];
                $anteriormente = Fichacupone::where('idFicha', '=', $request['idFicha'])->where('idCupon', '=', $cupon->id)->get();
                if(count($anteriormente) > 0){
                    return response()->json('Ya has canjeado este cupon anteriormente', 400);
                }
                if(floatval($cupon->monto) > floatval($total)){
                    return response()->json('No se puede canjear el cupon ya que el monto del cupon es mayor a el total de la deuda de el estado de cuenta', 400);
                }
                $descuento = Alumnodescuento::create([
                    'idFicha' => $request['idFicha'],
                    'monto' => $cupon->monto,
                    'concepto' => 'Cupon de descuento '.$cupon->cupon,
                    'idUsuario' => $request['idUsuario'],
                    'eliminado' => 0,
                    'activo' => 1,
                    'idConcepto' => 0,
                    'idCupon' => $cupon->id,
                    'cantidad' => 0,
                    'tipo' => 0,
                    'canitdad' => $cupon->monto
                ]);
                $reducir = Cupone::find($cupon->id);
                $reducir->cantidad = $reducir->cantidad-1;
                $reducir->save();
                $nuevo = Fichacupone::create([
                    'idFicha' => $request['idFicha'],
                    'idCupon' => $cupon->id,
                    'eliminado' => 0,
                    'activo' => 1
                ]);
                return response()->json($descuento, 200);
            }else{
                return response()->json('Cupon no existente', 400);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarDatosAspiracion(Request $request){
        try {
            if(intval($request['id']) === 0){
                $aspiraciones = Aspiracione::create([
                    'idFicha' => $request['idFicha'],
                    'idUniversidad' => $request['idUniversidad'],
                    'idCentroUniversitario' => $request['idCentroUniversitario'],
                    'idCarrera' => $request['idCarrera'],
                    'eliminado' => 0,
                    'activo' => 1
                ]);

                return response()->json($aspiraciones, 200);
            }else{
                $aspiraciones = Aspiracione::find($request['id']);
                $aspiraciones->idUniversidad = $request['idUniversidad'];
                $aspiraciones->idCentroUniversitario = $request['idCentroUniversitario'];
                $aspiraciones->idCarrera = $request['idCarrera'];
                $aspiraciones->save();

                return response()->json($aspiraciones, 200);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarTipoPago(Request $request){
        try {
            $ficha = Ficha::find($request['idFicha']);
            $ficha->idTipoPago = $request['idTipoPago'];
            $ficha->save();
            return response()->json($ficha, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarDatosPublicitarios(Request $request){
        try {
            $datos = Publicitario::find($request['id']);
            $datos->idMedioContacto = $request['idMedioContacto'];
            $datos->idMedioPublicitario = $request['idMedioPublicitario'];
            $datos->idViaPublicitaria = $request['idViaPublicitaria'];
            $datos->idMotivoInscripcion = $request['idMotivoInscripcion'];
            $datos->idMotivoBachillerato = $request['idMotivoBachillerato'];
            $datos->idCampania = $request['idCampania'];
            $datos->tomoCurso = $request['tomoCurso'];
            if(intval($request['tomoCurso']) === 0)
                $datos->idEmpresaCurso = 0;
            else    
                $datos->idEmpresaCurso = $request['idEmpresaCurso'];
            $datos->save();

            return response()->json($datos, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}