<?php

	namespace App\Clases;
	use Carbon\Carbon;
	use App\Egreso;
	use App\Rubrosegreso;
	use App\Tiposegreso;
	use App\Calendario;
	use App\Formaspago;
	use App\Metodospago;
	use App\Nivele;
	use App\Cuenta;
	use App\Ingreso;
	use App\Alumnodevolucione;
	use App\Egresosolicitude;
	use App\Sucursale;
	use App\Clases\Egresos;

	use Illuminate\Support\Facades\DB;

	class Egresos{
		function totalEfectivo($sucursal){
			$total = Egreso::where('activo', '=', 1)->where('idSucursal', '=', $sucursal)->where('idFormaPago', '=', 1)->sum('monto');
			return $total + 0;
		}

		function busquedaGeneral(){
			try {
				$registros = Egreso::leftjoin('calendarios', 'calendarios.id', '=', 'egresos.idCalendario')->
	               leftjoin('niveles', 'niveles.id', '=', 'egresos.idNivel')->
	               leftjoin('rubrosegresos', 'rubrosegresos.id', '=', 'egresos.idRubro')->
	               leftjoin('tiposegresos', 'tiposegresos.id', '=', 'egresos.idTipo')->
	               leftjoin('formaspagos', 'formaspagos.id', '=', 'egresos.idFormaPago')->
	               leftjoin('nominaegresos', 'nominaegresos.idEgreso', '=', 'egresos.id')->
	               leftjoin('nominas', 'nominas.id', '=', 'nominaegresos.idNomina')->
	               leftjoin('empleados', 'empleados.id', '=', 'nominas.idEmpleado')->
	               leftjoin('departamentos', 'departamentos.id', '=', 'nominas.idDepartamento')->
	               leftjoin('sucursales', 'egresos.idSucursal', '=', 'sucursales.id')->
	               leftjoin('vales', 'vales.idEgreso', '=', 'egresos.id')->
	               leftjoin('cuentas', 'egresos.idCuenta', '=', 'cuentas.id')->
	               select(
	                'egresos.id',
	                'egresos.referencia',
	                'egresos.monto',
	                'egresos.idRubro',
	                'egresos.idTipo',
	                'egresos.idCalendario',
	                'egresos.idNivel',
	                'egresos.idFormaPago',
	                'egresos.observaciones',
	                'egresos.idCuenta',
	                'niveles.nombre as nivel',
	                'calendarios.nombre as calendario',
	                'egresos.folio',
	                DB::raw('DATE_FORMAT(egresos.created_at, "%d-%m-%Y %H:%i:%s") as fechaFormato'),
	                'rubrosegresos.nombre as rubro',
	                DB::raw("(CASE 
	                    WHEN(egresos.idRubro = 3 AND egresos.idTipo = 4) THEN empleados.nombre
	                    ELSE egresos.concepto
	                    END) AS concepto"),
	                DB::raw("(CASE 
	                    WHEN(egresos.idRubro = 3 AND egresos.idTipo = 4) THEN departamentos.nombre
	                    WHEN(egresos.idRubro = 2 AND egresos.idTipo = 2) THEN vales.folio
	                    ELSE tiposegresos.nombre
	                    END) AS tipo"),
	                'formaspagos.nombre as forma',
	                DB::raw("CONCAT('$',FORMAT(egresos.monto,2)) AS montoFormato"),
	                DB::raw("IF(egresos.activo = 0, 'bg-rojo', '') AS bg"),
	                'cuentas.nombre as cuenta'
	               );
                return $registros;
			} catch (Exception $e) {
				return null;
			}
		}

		function listas(){
			try {
				return array(
	            	'rubros' => Rubrosegreso::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	            	'tipos' => Tiposegreso::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	            	'calendarios' => Calendario::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	            	'formas' => Formaspago::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	            	'metodos' => Metodospago::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	            	'niveles' => Nivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	            	'cuentas' => Cuenta::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	            	'sucursales' => Sucursale::where('eliminado', '=', 0)->where('activo', '=', 1)->get()
	            );
			} catch (Exception $e) {
				return null;
			}
		}

		function completar($egreso){
			try {
				$fecha = Carbon::parse($egreso->created_at);
	            $egreso->calendario = Calendario::find($egreso->idCalendario)->nombre;
	            $egreso->nivel = Nivele::find($egreso->idNivel)->nombre;
	            $egreso->fechaFormato = $fecha->format('d-m-Y h:i:s');
	            $egreso->rubro = Rubrosegreso::find($egreso->idRubro)->nombre;
	            $egreso->tipo = Tiposegreso::find($egreso->idTipo)->nombre;
	            $egreso->forma = Formaspago::find($egreso->idFormaPago)->nombre;
	            $egreso->montoFormato = '$'.number_format($egreso->monto, 2, '.', ',');

	            return $egreso;	
			} catch (Exception $e) {
				return null;
			}
		}

		function eliminarDevolucion($egreso){
			try {
				$devolucion = Alumnodevolucione::where('idEgreso', '=', $egreso)->get();
				if(count($devolucion) > 0){
					$devolucionFinal = Alumnodevolucione::find($devolucion->id);
	                $devolucionFinal->eliminado = 1;
	                $devolucionFinal->save();	
				}
                
                return true;
			} catch (Exception $e) {
				return response()->json('Error en el servidor', 400);
			}
		}

		function egresosDiariosUsuario($usuarioID, $sucursalID){
			try {
				return Egreso::join('rubrosegresos', 'idRubro', '=', 'rubrosegresos.id')->
	            join('formaspagos', 'idFormaPago', '=', 'formaspagos.id')->
	            select(
	                'egresos.folio',
	                'rubrosegresos.nombre as rubro',
	                'egresos.concepto',
	                DB::raw("CONCAT('$',FORMAT(egresos.monto,2)) AS monto"),
	                'formaspagos.nombre as forma',
	                DB::raw("(CASE 
	                            WHEN(egresos.referencia = 1) THEN 'Comun'
	                            WHEN(egresos.referencia = 2) THEN 'Devolucion'
	                            WHEN(egresos.referencia = 3) THEN 'Nomina'
	                            WHEN(egresos.referencia = 4) THEN 'Vale'
	                            WHEN(egresos.referencia = 5) THEN 'Transferencia'
	                            ELSE 'Desconocido'
	                            END) AS referencia"),
	                DB::raw('DATE_FORMAT(egresos.created_at, "%d-%m-%Y %H:%i:%s") as fecha')
	            )->
	            where('idUsuario', '=', $usuarioID)->
	            where('idSucursal', '=', $sucursalID)->
	            where('egresos.eliminado', '=', 0)->
	            whereRaw("DATE_FORMAT(egresos.created_at,'%y-%m-%d') = CURDATE()")->get();
			} catch (Exception $e) {
				return null;
			}
		}

		function totalEfectivoUsuarioDia($sucursal, $usuario){
			$total = Egreso::where('activo', '=', 1)->
			where('idSucursal', '=', $sucursal)->
			where('idFormaPago', '=', 1)->
			where('idUsuario', '=', $usuario)->
			whereRaw("DATE_FORMAT(egresos.created_at,'%y-%m-%d') = CURDATE()")->
			sum('monto');
			return $total + 0;
		}

		function existeSolicitud($egresoID){
			try {
				$dato = Egresosolicitude::where('idEgreso', '=', $egresoID)->where('estatus', '=', 1)->get();
				return (count($dato) > 0);
			} catch (Exception $e) {
				return null;
			}
		}

		function crearSolicitud($datos){
			try {
				return Egresosolicitude::create([
	                'idUsuarioSolicito' => $datos['usuarioID'],
	                'idUsuarioAcepto' => 0,
	                'idEgreso' => $datos['id'],
	                'concepto' => $datos['concepto'],
	                'monto' => $datos['monto'],
	                'observaciones' => $datos['observaciones'],
	                'idRubro' => $datos['idRubro'],
	                'idTipo' => $datos['idTipo'],
	                'idFormaPago' => $datos['idFormaPago'],
	                'idCuenta' => $datos['idCuenta'],
	                'estatus' => 1,
	                'eliminado' => 0,
	                'activo' => 1
	            ]);
			} catch (Exception $e) {
				return null;
			}
		}

		function solicitudes(){
			try {
				return Egresosolicitude::join('egresos', 'idEgreso', '=', 'egresos.id')->
	            join('usuarios', 'idUsuarioSolicito', '=', 'usuarios.id')->
	            join('empleados', 'usuarios.idEmpleado', '=', 'empleados.id')->
	            join('rubrosegresos', 'egresosolicitudes.idRubro', '=', 'rubrosegresos.id')->
	            join('tiposegresos', 'egresosolicitudes.idTipo', '=', 'tiposegresos.id')->
	            leftjoin('cuentas', 'egresosolicitudes.idCuenta', '=', 'cuentas.id')->
	            join('formaspagos', 'egresosolicitudes.idFormaPago', '=', 'formaspagos.id')->
	            select(
	                'egresos.folio',
	                'egresosolicitudes.*',
	                'empleados.nombre as empleado',
	                'rubrosegresos.nombre as rubro',
	                'tiposegresos.nombre as tipo',
	                'cuentas.nombre as cuenta',
	                'formaspagos.nombre as forma',
	                DB::raw("(CASE 
	                            WHEN(egresosolicitudes.estatus = 2) THEN 'bg-verde'
	                            WHEN(egresosolicitudes.estatus = 3) THEN 'bg-rojo'
	                            END) AS bg")
	            )->get();
			} catch (Exception $e) {
				return null;
			}
		}

		function aceptarModificacion($id, $usuarioID){
			try {
				$modificacion = Egresosolicitude::find($id);
	            $modificacion->estatus = 2;
	            $modificacion->idUsuarioAcepto = $usuarioID;
	            $modificacion->save();
	            return $modificacion;
			} catch (Exception $e) {
				return null;
			}
		}

		function rechazarModificacion($id, $usuarioID){
			try {
				$modificacion = Egresosolicitude::find($id);
	            $modificacion->estatus = 3;
	            $modificacion->idUsuarioAcepto = $requestusuarioID;
	            $modificacion->save();
	            return $modificacion;
			} catch (Exception $e) {
				return null;
			}
		}

		//Funciones de egresos

		function actualizarEgreso($id, $concepto, $monto, $observaciones, $rubroID, $tipoID, $formaID, $cuentaID){
			try {
				Egreso::find($id);

	            $egreso->concepto = $concepto;
	            $egreso->monto = $monto;
	            $egreso->observaciones = $observaciones;
	            $egreso->idRubro = $rubroID;
	            $egreso->idTipo = $tipoID;
	            $egreso->idFormaPago = $formaID;
	            $egreso->idCuenta = $cuentaID;
	            $egreso->save();
	            return $egreso;
			} catch (Exception $e) {
				return null;
			}
		}

		function modificarEgreso($egresoID, $datos){
			try {
				Egreso::find($egresoID);

	            $egreso->concepto = $datos['concepto'];
	            $egreso->monto = $datos['monto'];
	            $egreso->observaciones = $datos['observaciones'];
	            $egreso->idRubro = $datos['idRubro'];
	            $egreso->idTipo = $datos['idTipo'];
	            $egreso->idFormaPago = $datos['idFormaPago'];
	            $egreso->idCuenta = $datos['idCuenta'];
	            $egreso->save();
	            return $egreso;
			} catch (Exception $e) {
				return null;
			}
		}

		function crearEgreso($datos, $folio){
			try {
				return Egreso::create([
	                'concepto' => $datos['concepto'],
	                'monto' => $datos['monto'],
	                'observaciones' => $datos['observaciones'],
	                'idRubro' => $datos['idRubro'],
	                'idTipo' => $datos['idTipo'],
	                'idSucursal' => $datos['sucursalID'],
	                'idSucursalGasto' => isset($datos['idSucursalGasto']) ? $datos['idSucursalGasto'] : $datos['sucursalID'],
	                'idCalendario' => $datos['idCalendario'],
	                'idFormaPago' => $datos['idFormaPago'],
	                'idUsuario' => $datos['usuarioID'],
	                'referencia' => $datos['referencia'],
	                'idNivel' => $datos['idNivel'],
	                'folio' => $folio,
	                'idCuenta' => $datos['idCuenta'],
	                'voucher' => $datos['voucher'],
	                'activo' => 1,
	                'eliminado' => 0,
	            ]);
			} catch (Exception $e) {
				return null;
			}
		}

		function nuevoEgreso($concepto, $monto, $observaciones, $rubroID, $tipoID, $sucursalID, $calendarioID, $formaID, $usuarioID, $referencia, $nivelID, $folio, $bancoID, $voucher, $sucursalGastoID){
			try {
				return Egreso::create([
	                'concepto' => $concepto,
	                'monto' => $monto,
	                'observaciones' => $observaciones,
	                'idRubro' => $rubroID,
	                'idTipo' => $tipoID,
	                'idSucursal' => $sucursalID,
	                'idSucursalGasto' => isset($sucursalGastoID) ? $sucursalGastoID : $sucursalID,
	                'idCalendario' => $calendarioID,
	                'idFormaPago' => $formaID,
	                'idUsuario' => $usuarioID,
	                'referencia' => $referencia,
	                'idNivel' => $nivelID,
	                'folio' => $folio,
	                'idCuenta' => $bancoID,
	                'voucher' => $voucher,
	                'activo' => 1,
	                'eliminado' => 0,
	            ]);
			} catch (Exception $e) {
				return null;
			}
		}

		function actualizar($id, $concepto, $monto, $observaciones, $rubroID, $tipoID, $formaID, $cuentaID){
			try {
				$egreso = Egreso::find($id);

	            $egreso->concepto = $concepto;
	            $egreso->monto = $monto;
	            $egreso->observaciones = $observaciones;
	            $egreso->idRubro = $rubroID;
	            $egreso->idTipo = $tipoID;
	            $egreso->idFormaPago = $formaID;
	            $egreso->idCuenta = $cuentaID;
	            $egreso->save();
	            return $egreso;
			} catch (Exception $e) {
				return null;
			}
		}

		//Modificaciones a campos
		function monto($id, $monto){
			try {
				$egreso = Egreso::find($id);
				$egreso->monto = $monto;
				$egreso->save();
			} catch (Exception $e) {
				return null;
			}
		}
	}
?>