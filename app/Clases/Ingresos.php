<?php

	namespace App\Clases;
	use Carbon\Carbon;
	use App\Ingreso;
	use App\Rubro;
	use App\Tiposingreso;
	use App\Calendario;
	use App\Formaspago;
	use App\Metodospago;
	use App\Nivele;
	use App\Cuenta;
	use App\Banco;
	use App\Sucursale;
	use App\Alumnoabono;
	use App\Ingresosolicitude;
	use Illuminate\Support\Facades\DB;

	class Ingresos{

		function nuevo($concepto, $monto, $observaciones, $rubroID, $tipoID, $sucursalID, $calendarioID, $formaID, $metodoID, $usuarioID, $referencia, $nivelID, $folio, $imagen, $bancoID, $numeroReferencia, $nombreCuenta, $cuentaID, $fecha){
			try {
				return Ingreso::create([
	                'concepto' => $concepto,
	                'monto' => $monto,
	                'observaciones' => $observaciones,
	                'idRubro' => $rubroID,
	                'idTipo' => $tipoID,
	                'idSucursal' => $sucursalID,
	                'idCalendario' => $calendarioID,
	                'idFormaPago' => $formaID,
	                'idMetodoPago' => $metodoID,
	                'idUsuario' => $usuarioID,
	                'referencia' => $referencia,
	                'idNivel' => $nivelID,
	                'folio' => $folio,
	                'imagen' => $imagen,
	                'idBanco' => $bancoID,
	                'numeroReferencia' => $numeroReferencia,
	                'nombreCuenta' => $nombreCuenta,
	                'idCuenta' => $cuentaID,
	                'fecha' => $fecha,
	                'activo' => 1,
	                'eliminado' => 0,
	            ]);
			} catch (Exception $e) {
				return null;
			}
		}

		function totalEfectivo($sucursal){
			$total = Ingreso::where('activo', '=', 1)->where('idSucursal', '=', $sucursal)->where('idFormaPago', '=', 1)->where('eliminado', '=', 0)->sum('monto');
			return $total + 0;
		}

		function listas(){
			try {
	            return array(
	            	'rubros' => Rubro::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	            	'tipos' => Tiposingreso::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	            	'calendarios' => Calendario::where('eliminado', 0)
                        ->where('activo', 1)
                        ->where('inicio', '>=', Carbon::now()->subMonths(18))
                        ->orderBy('inicio', 'asc')
                        ->get(),
	            	'formas' => Formaspago::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	            	'metodos' => Metodospago::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	            	'niveles' => Nivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	            	'cuentas' => Cuenta::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	            	'bancos' => Banco::where('eliminado', '=', 0)->get(),
	            	'sucursales' => Sucursale::where('eliminado', '=', 0)->get()
	            );
			} catch (Exception $e) {
				return null;
			}
		}

		function completar($ingreso){
			try {
				$ingreso->calendario = Calendario::find($ingreso->idCalendario)->nombre;
		        $ingreso->nivel = Nivele::find($ingreso->idNivel)->nombre;
		        $ingreso->rubro = Rubro::find($ingreso->idRubro)->nombre;
		        $ingreso->forma = Formaspago::find($ingreso->idFormaPago)->nombre;
		        $ingreso->cuenta = (intval($ingreso->idCuenta) > 0) ? Cuenta::find($ingreso->idCuenta)->nombre : 'N/A';
		        $ingreso->montoFormato = '$'.number_format($ingreso->monto, 2, '.', ',');
		        if(intval($ingreso->idFormaPago) !== 1 ){
		            if(strlen($ingreso->imagen) <= 0){
		                $ingreso->title = "Falta Voucher";
		                $ingreso->hayVoucher = 'fas fa-times text-danger';
		            }else{
		                $ingreso->hayVoucher = 'fas fa-check text-success';
		            }
		        }else{
		            $ingreso->hayVoucher = 'N/A';
		        }

		        return $ingreso;
			} catch (Exception $e) {
				return null;
			}
		}

		function busquedaGeneral(){
			try {
				$registros = Ingreso::leftjoin('calendarios', 'idCalendario', '=', 'calendarios.id')->
		            leftjoin('sucursales', 'idSucursal', '=', 'sucursales.id')->
		            leftjoin('rubros', 'idRubro', '=', 'rubros.id')->
		            leftjoin('tiposingresos', 'idTipo', '=', 'tiposingresos.id')->
		            leftjoin('formaspagos', 'idFormaPago', '=', 'formaspagos.id')->
		            leftjoin('metodospagos', 'idMetodoPago', '=', 'metodospagos.id')->
		            leftjoin('usuarios', 'idUsuario', '=', 'usuarios.id')->
		            leftjoin('niveles', 'idNivel', '=', 'niveles.id')->
		            leftjoin('cuentas', 'idCuenta', '=', 'cuentas.id')->
		            leftjoin('vales', 'vales.idIngreso', '=', 'ingresos.id')->
		            select(
		                'ingresos.id',
		                'ingresos.monto',
		                'ingresos.observaciones',
		                'ingresos.idRubro', 
		                'ingresos.idTipo',
		                'ingresos.idSucursal',
		                'ingresos.idCalendario',
		                'ingresos.idFormaPago',
		                'ingresos.idMetodoPago',
		                'ingresos.idNivel',
		                'ingresos.idUsuario',
		                'ingresos.referencia',
		                'ingresos.activo',
		                'ingresos.eliminado',
		                'ingresos.created_at as fecha',
		                'ingresos.updated_at',
		                'ingresos.folio',
		                'ingresos.idBanco',
		                'ingresos.nombreCuenta',
		                'ingresos.numeroReferencia',
		                'ingresos.idCuenta',
		                'calendarios.nombre as calendario',
		                'niveles.nombre as nivel',
		                'rubros.nombre as rubro',
		                'formaspagos.nombre as forma',
		                'metodospagos.nombre as metodo',
		                DB::raw("DATE_FORMAT(ingresos.created_at, '%d-%m-%Y %H:%i:%s') as fechaFormato"),
		                DB::raw("(CASE 
		                            WHEN(ingresos.idRubro = 2 AND ingresos.idTipo = 3) THEN vales.folio
		                            ELSE ingresos.concepto
		                            END) AS concepto"),
		                        'formaspagos.nombre as forma',
		                DB::raw("IF(ingresos.idFormaPago <> 1, cuentas.nombre, 'N/A') as cuenta"),
		                DB::raw("IF(ingresos.idFormaPago <> 1, IF(LENGTH(ingresos.imagen) > 0, 'SI', 'NO'), 'N/A') as hayVoucher"),
		                DB::raw("CONCAT('$',FORMAT(ingresos.monto,2)) AS montoFormato"),
		                DB::raw("IF(ingresos.activo = 0, 'bg-rojo', '') as bg")
		            );
		        return $registros;
			} catch (Exception $e) {
				return null;
			}
		}

		function traerAbono($id){
			try {
				$abono = Alumnoabono::where('idIngreso', '=', $id)->get();
				return (count($abono) > 0) ? $abono[0] : null;
			} catch (Exception $e) {
				return null;
			}
		}

		function solicitudes(){
			try {
				$solicitudes = Ingresosolicitude::join('ingresos', 'idIngreso', '=', 'ingresos.id')->
		        join('usuarios', 'idUsuarioSolicito', '=', 'usuarios.id')->
		        join('empleados', 'usuarios.idEmpleado', '=', 'empleados.id')->
		        join('rubros', 'ingresosolicitudes.idRubro', '=', 'rubros.id')->
		        join('tiposingresos', 'ingresosolicitudes.idTipo', '=', 'tiposingresos.id')->
		        join('formaspagos', 'ingresosolicitudes.idFormaPago', '=', 'formaspagos.id')->
		        join('metodospagos', 'ingresosolicitudes.idMetodoPago', '=', 'metodospagos.id')->
		        leftjoin('bancos', 'ingresosolicitudes.idBanco', '=', 'bancos.id')->
		        leftjoin('cuentas', 'ingresosolicitudes.idCuenta', '=', 'cuentas.id')->
		        select(
		            'ingresos.folio',
		            'ingresosolicitudes.*',
		            'empleados.nombre as empleado',
		            'rubros.nombre as rubro',
		            'tiposingresos.nombre as tipo',
		            'formaspagos.nombre as forma',
		            'metodospagos.nombre as metodo',
		            'bancos.nombre as banco',
		            'cuentas.nombre as cuenta',
		            DB::raw("(CASE 
		                        WHEN(ingresosolicitudes.estatus = 2) THEN 'bg-verde'
		                        WHEN(ingresosolicitudes.estatus = 3) THEN 'bg-rojo'
		                        END) AS bg")
		        )->get();

		        return $solicitudes;
			} catch (Exception $e) {
				return null;
			}
		}

		function modificar($id, $concepto, $monto, $observaciones, $rubroID, $tipoID, $formaID, $metodoID, $bancoID, $nombreCuenta, $numeroReferencia, $cuentaID){
			try {
				$ingreso = Ingreso::find($id);
	            $ingreso->concepto = $concepto;
	            $ingreso->monto = $monto;
	            $ingreso->observaciones = $observaciones;
	            $ingreso->idRubro = $rubroID;
	            $ingreso->idTipo = $tipoID;
	            $ingreso->idFormaPago = $formaID;
	            $ingreso->idMetodoPago = $metodoID;
	            $ingreso->idBanco = $bancoID;
	            $ingreso->nombreCuenta = $nombreCuenta;
	            $ingreso->numeroReferencia = $numeroReferencia;
	            $ingreso->idCuenta = $cuentaID;
	            $ingreso->save();

	            return $ingreso;
			} catch (Exception $e) {
				return null;
			}
		}

		function ingresosDiariosUsuario($usuarioID, $sucursalID){
			try {
		        return Ingreso::join('rubros', 'idRubro', '=', 'rubros.id')->
		        join('formaspagos', 'idFormaPago', '=', 'formaspagos.id')->
		        select(
		            'ingresos.folio',
		            'rubros.nombre as rubro',
		            'ingresos.concepto',
		            DB::raw("CONCAT('$',FORMAT(ingresos.monto,2)) AS monto"),
		            'formaspagos.nombre as forma',
		            DB::raw("(CASE 
		                        WHEN(ingresos.referencia = 1) THEN 'Comun'
		                        WHEN(ingresos.referencia = 2) THEN 'Inscripcion'
		                        WHEN(ingresos.referencia = 3) THEN 'Abonos'
		                        WHEN(ingresos.referencia = 4) THEN 'Vale'
		                        WHEN(ingresos.referencia = 5) THEN 'Transferencia'
		                        ELSE 'Desconocido'
		                        END) AS referencia"),
		            DB::raw('DATE_FORMAT(ingresos.created_at, "%d-%m-%Y %H:%i:%s") as fecha')
		        )->
		        where('idUsuario', '=', $usuarioID)->
		        where('idSucursal', '=', $sucursalID)->
		        where('ingresos.eliminado', '=', 0)->
		        whereRaw("DATE_FORMAT(ingresos.created_at,'%y-%m-%d') = CURDATE()")->get();
	      	} catch (Exception $e) {
	        	return null;
	      	}
		}

		function totalEfectivoUsuarioDia($sucursal, $usuario){
			$total = Ingreso::where('activo', '=', 1)->
			where('idSucursal', '=', $sucursal)->
			where('idFormaPago', '=', 1)->
			where('idUsuario', '=', $usuario)->
			whereRaw("DATE_FORMAT(ingresos.created_at,'%y-%m-%d') = CURDATE()")->
			sum('monto');
			return $total + 0;
		}

		function existeSolicitud($ingresoID){
			try {
				return (Ingresosolicitude::where('idIngreso', '=', $ingresoID)->where('estatus', '=', 1)->count() > 0) ;
			} catch (Exception $e) {
				return null;
			}
		}

		function nuevaSolicitud($ingresoID, $usuarioID, $concepto, $monto, $observaciones, $rubroID, $tipoID, $formaID, $metodoID, $fecha, $bancoID, $nombreCuenta, $numeroReferencia, $cuentaID){
			try {
				return Ingresosolicitude::create([
	                'idUsuarioSolicito' => $usuarioID,
	                'idUsuarioAcepto' => 0,
	                'idIngreso' => $ingresoID,
	                'concepto' => $concepto,
	                'monto' => $monto,
	                'observaciones' => $observaciones,
	                'idRubro' => $rubroID,
	                'idTipo' => $tipoID,
	                'idFormaPago' => $formaID,
	                'idMetodoPago' => $metodoID,
	                'fecha' => $fecha,
	                'idBanco' => $bancoID,
	                'nombreCuenta' => $nombreCuenta,
	                'numeroReferencia' => $numeroReferencia,
	                'idCuenta' => $cuentaID,
	                'estatus' => 1,
	                'eliminado' => 0,
	                'activo' => 1
	            ]);
			} catch (Exception $e) {
				return null;
			}
		}

		function aceptarSolicitud($id, $usuarioID){
			try {
				$modificacion = Ingresosolicitude::find($id);
	            $modificacion->estatus = 2;
	            $modificacion->idUsuarioAcepto = $usuarioID;
	            $modificacion->save();
	            return $modificacion;
			} catch (Exception $e) {
				return null;
			}
		}

		function rechazarSolicitud($id, $usuarioID){
			try {
				$modificacion = Ingresosolicitude::find($id);
	            $modificacion->estatus = 3;
	            $modificacion->idUsuarioAcepto = $usuarioID;
	            $modificacion->save();
	            return $modificacion;
			} catch (Exception $e) {
				return null;
			}
		}
	}
?>