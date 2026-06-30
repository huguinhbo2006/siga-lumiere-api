<?php

namespace App\Http\Controllers;
use App\Clases\Nominas;
use App\Clases\Sucursales;
use App\Clases\Folios;
use App\Clases\Consultas;
use App\Clases\Egresos;
use App\Clases\Percepciones;
use App\Clases\Deducciones;
use App\Clases\Solicitudesnominas;
use App\Nivele;
use App\Nomina;
use App\Percepcione;
use App\Deduccione;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NominasController extends BaseController
{

    function nuevo(Request $request){
        try {
            $funciones = new Nominas();
            $consultas = new Consultas();

            $consultas->start();
            $nomina = $funciones->nueva($request['empleado'], $request['sucursalID']);
            if(!$funciones->agregarPercepciones($request['desgloce']['percepciones'], $nomina->id)){
                return response()->json('Error al agregar las percepciones', 400);
            }
            if(!$funciones->agregarDeducciones($request['desgloce']['deducciones'], $nomina->id)){
                return response()->json('Error al agregar las deducciones', 400);
            }
            if(!$funciones->actualizarTotal($nomina->id)){
                return response()->json('Error al actualizar el total de la nomina', 400);
            }
            $consultas->commit();
            return response()->json($nomina, 200);
        } catch (Exception $e) {
            $consultas->rollback();
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $funciones = new Nominas();

            return response()->json(array(
                'datos' => $funciones->nominasSucursal($request['sucursalID']),
                'listas' => $funciones->listas()
            ), 200);
        } catch (Exception $e) {
            return response()->json('Erro en el servidor', 400);
        }
    }

    function nomina(Request $request){
        try {
            $funciones = new Nominas();
            return response()->json(array(
                'listas' => $funciones->listas(),
                'dato' => $funciones->traer($request['id']) 
            ), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function creadas(){
        try {
            $funciones = new Nominas();
            return response()->json(array(
                'nominas' => $funciones->creadas(),
                'listas' => $funciones->listas() 
            ), 200);
        } catch (Exception $e) {
            return response()->json('Erro en el servidor', 400);
        }
    }

    function autorizadas(Request $request){
        try {
            $funciones = new Nominas();
            return response()->json($funciones->autorizadas($request['sucursalID']), 200);
        } catch (Exception $e) {
            return response()->json('Erro en el servidor', 400);
        }
    }

    function autorizar(Request $request){
        try {
            foreach ($request['nominas'] as $registro) {
                $nomina = Nomina::find($registro['id']);
                $nomina->estatus = 1;
                $nomina->save();
            }
            return response()->json($request, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function cobrar(Request $request){
        $consultas = new Consultas();
        try {
            $funciones = new Nominas();
            $sucursales = new Sucursales();

            $consultas->start();
            $nomina = $funciones->datos($request['id']);
            $totalEfectivo = floatval($nomina->percepcionesEfectivo) - floatval($nomina->deduccionesEfectivo);
            $totalDeposito = floatval($nomina->percepcionesDeposito) - floatval($nomina->deduccionesDeposito);

            $saldoSucursal = $sucursales->saldo($request['sucursalID']);
            if($totalEfectivo > $saldoSucursal){
                return response()->json("No cuentas con suficiente saldo para realizar este egreso", 400);
            }

            if(floatval($totalEfectivo) > 0){
                $funciones->crearEgreso($nomina, $request['usuarioID'], $totalEfectivo, 1);    
            }

            if(floatval($totalDeposito)){
                $funciones->crearEgreso($nomina, $request['usuarioID'], $totalDeposito, 4);    
            }

            $actualizar = $funciones->cobrar($nomina->id);
            $consultas->commit();
            return response()->json($actualizar, 200);
        } catch (Exception $e) {
            $consultas->rollback();
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try {
            $funciones = new Nominas();
            return response()->json($funciones->modificar($request['id'], $request['fechaInicio'], $request['fechaFin'], $request['fechaExpedicion'], $request['quincena'], $request['observaciones']), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function cuenta(Request $request) {
        try {
            $funciones = new Nominas();
            $percepciones = new Percepciones();
            $deducciones = new Deducciones();
            
            $nomina = $funciones->traer($request['id']);
            $docentes = (intval($nomina->idDepartamento) === 1) ? 1 : 2;

            return response()->json(
                array(
                    'listas' => array(
                        'percepciones' => $percepciones->conceptos($docentes),
                        'deducciones' => $deducciones->conceptos($docentes) 
                    ),
                    'datos' => array(
                        'percepciones' => $funciones->percepciones($request['id']),
                        'deducciones' => $funciones->deducciones($request['id']),
                        'departamento' => $nomina->idDepartamento
                    )
            ), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function agregarPercepcion(Request $request) {
        try {
            $data = $request->only('idFormaPago', 'monto', 'idConcepto', 'cantidad', 'valorUnitario');
            $data['idNomina'] = $request['id'];
            $percepcion = Percepcione::create($data);
            return response()->json($percepcion, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function agregarDeduccion(Request $request) {
        try {
            $data = $request->only('idFormaPago', 'monto', 'idConcepto', 'cantidad', 'valorUnitario');
            $data['idNomina'] = $request['id'];
            Deduccione::create($data);

            return response()->json(true, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarPercepcion(Request $request) {
        try {
            Percepcione::destroy($request['id']);
            return response()->json(true, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarDeduccion(Request $request) {
        try {
            Deduccione::destroy($request['id']);
            return response()->json(true, 200);  
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function solicitudes(){
        try {
            $funciones = new Nominas();
            return response()->json($funciones->solicitudes(), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function aceptarSolicitud(Request $request){
        try {
            $funciones = new Nominas();
            $consultas = new Consultas();
            $egresos = new Egresos();

            $consultas->start();
            $solicitud = $funciones->traerSolicitud($request['id']);

            if(intval($request['tipo']) === 1){
                if(intval($solicitud->forma) === 1){
                    $funciones->agregarPercepcion(
                        $solicitud->idNomina, 
                        $solicitud->idFormaPago, 
                        (floatval($solicitud->cantidad) * floatval($solicitud->valorUnitario)),
                        $solicitud->idConcepto,
                        $solicitud->cantidad,
                        $solicitud->valorUnitario
                    );
                }else{
                    $funciones->eliminarPercepcion($solicitud->idModificacion);
                }
            }else{
                if(intval($solicitud->forma) === 1){
                    $funciones->agregarDeduccion(
                        $solicitud->idNomina,
                        $solicitud->idFormaPago,
                        (floatval($solicitud->cantidad) * floatval($solicitud->valorUnitario)),
                        $solicitud->idConcepto,
                        $solicitud->cantidad,
                        $solicitud->valorUnitario
                    );
                }else{
                    $funciones->eliminarDeduccion($solicitud->idModificacion);
                }
            }

            $solicitud->estatus = 2;
            $solicitud->save();
            $nomina = $funciones ->datos($solicitud->idNomina);

            if($nomina->totalEfectivo > 0){
                if($funciones->existeEgresoEfectivo($solicitud->idNomina)){
                    $egresos->monto(
                        $funciones->idEgresoEfectivo($solicitud->idNomina),
                        $nomina->totalEfectivo
                    );
                }else{
                    $funciones->crearEgreso($nomina, $request['usuarioID'], $nomina->totalEfectivo, 1);
                }
            }

            if($nomina->totalDeposito > 0){
                if($funciones->existeEgresoDeposito($solicitud->idNomina)){
                    $egresos->monto(
                        $funciones->idEgresoDeposito($solicitud->idNomina),
                        $nomina->totalDeposito
                    );
                }else{
                    $funciones->crearEgreso($nomina, $request['usuarioID'], $nomina->totalDeposito, 4);
                }
            }
            $nomina = $funciones->total($nomina->id, ($nomina->totalEfectivo + $nomina->totalDeposito));
            return response()->json($solicitud, 400);
        } catch (Exception $e) {
            $consultas->rollback();
            return response()->json('Error en el servidor', 400);
        }
    }

    function rechazarSolicitud(Request $request){
        try {
            $solicitud = Solicitudnomina::find($request['id']);
            $solicitud->estatus = 3;
            $solicitud->save();
            return response()->json($solicitud, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}