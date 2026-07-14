<?php

	namespace App\Clases;
	use Carbon\Carbon;

	use App\Nomina;
	use App\Percepcione;
	use App\Deduccione;
	use App\Egreso;
	use App\Nominaegreso;
	use App\Solicitudnomina;
	//listas
	use App\Nivele;
	use App\Banco;
	use App\Calendario;
	use App\Departamento;
	use App\Puesto;
	use App\Empleado;
	use App\Conceptosdeduccione;
	use App\Conceptospercepcione;
	use App\Sucursale;
	use App\Formaspago;
	use App\Clases\Listas;

	use App\Clases\Folios;
	use Illuminate\Support\Facades\DB;

	class Nominas{

		function nominasSucursal($sucursalID){
			try {
				return Nomina::join('empleados', 'idEmpleado', '=', 'empleados.id')->
	            join('sucursales', 'nominas.idSucursal', '=', 'sucursales.id')->
	            join('calendarios', 'idCalendario', '=', 'calendarios.id')->
	            join('departamentos', 'nominas.idDepartamento', '=', 'departamentos.id')->
	            join('niveles', 'idNivel', '=', 'niveles.id')->
	            select(
	                'nominas.*',
	                'departamentos.nombre as departamento',
	                'niveles.nombre as nivel',
	                'sucursales.nombre as sucursal',
	                'empleados.nombre as empleado',
	                'calendarios.nombre as calendario',
	                DB::raw("(CASE 
	                    WHEN(nominas.estatus = 0) THEN 'bg-rojo'
	                    WHEN(nominas.estatus = 1) THEN 'bg-amarillo'
	                    WHEN(nominas.estatus = 2) THEN 'bg-verde'
	                    WHEN(nominas.estatus = 3) THEN 'bg-azul'
	                    END) AS bg")
	            )->
	            where('nominas.idSucursal', '=', $sucursalID)->
	            where('nominas.eliminado', '=', 0)->orderBy('nominas.created_at', 'DESC')->get();
			} catch (Exception $e) {
				return null;
			}
		}

		function listas(){
			try {
				$empleados = Empleado::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
				$percepciones = array();
				foreach ($empleados as $empleado) {
					if(intval($empleado->idDepartamanto !== 1)){
						$percepciones[] = array(
							'idConcepto' => 1,
							'valorUnitario' => $empleado->sueldoBase,
							'cantidad' => 1,
							'idFormaPago' => 1,
							'idEmpleado' => $empleado->id,
							'id' => 1,
							'monto' => $empleado->sueldoBase
						);

						$percepciones[] = array(
							'idConcepto' => 2,
							'valorUnitario' => $empleado->sueldoFiscal,
							'cantidad' => 1,
							'idFormaPago' => 4,
							'idEmpleado' => $empleado->id,
							'id' => 2,
							'monto' => $empleado->sueldoFiscal
						);

						$percepciones[] = array(
							'idConcepto' => 3,
							'valorUnitario' => $empleado->bonoPuntualidad,
							'cantidad' => 1,
							'idFormaPago' => 1,
							'idEmpleado' => $empleado->id,
							'id' => 3,
							'monto' => $empleado->bonoPuntualidad
						);
					}
				}
				$listas = Listas::listas(['niveles', 'calendarios', 'actuales', 'departamentos', 'puestos', 'empleados', 'conceptosdeducciones', 'conceptospercepciones', 'sucursales', 'formaspagos']);
				$listas['nominas'] = $percepciones;
				return $listas;
			} catch (Exception $e) {
				return null;
			}
		}

		function creadas(){
			try {
				return Nomina::join('empleados', 'idEmpleado', '=', 'empleados.id')->
	            join('sucursales', 'nominas.idSucursal', '=', 'sucursales.id')->
	            join('calendarios', 'idCalendario', '=', 'calendarios.id')->
	            join('departamentos', 'nominas.idDepartamento', '=', 'departamentos.id')->
	            join('niveles', 'idNivel', '=', 'niveles.id')->
	            select(
	            	'nominas.*', 
	            	'departamentos.nombre as departamento',
	            	'niveles.nombre as nivel',
	            	'sucursales.nombre as sucursal',
	            	'empleados.nombre as empleado',
	            	'calendarios.nombre as calendario',
	            	DB::raw('CONCAT("$", nominas.total) as totalFormato')
	            )->
	            where('nominas.eliminado', '=', 0)->
	            where('estatus', '=', 0)->get();
			} catch (Exception $e) {
				return null;
			}
		}

		function autorizadas($sucursalID){
			try {
				$nominas = Nomina::join('empleados', 'idEmpleado', '=', 'empleados.id')->
	               join('sucursales', 'nominas.idSucursal', '=', 'sucursales.id')->
	               join('calendarios', 'idCalendario', '=', 'calendarios.id')->
	               join('departamentos', 'nominas.idDepartamento', '=', 'departamentos.id')->
	               join('niveles', 'idNivel', '=', 'niveles.id')->
	               select('nominas.id', 
	                      'nominas.folio', 
	                      'departamentos.nombre as departamento', 
	                      'niveles.nombre as nivel', 
	                      'sucursales.nombre as sucursal', 
	                      'empleados.nombre as empleado', 
	                      'calendarios.nombre as calendario',
	                      DB::raw("CONCAT('$',FORMAT(nominas.total, 2)) AS total"))->
	               where('nominas.idSucursal', '=', $sucursalID)->
	               where('nominas.eliminado', '=', 0)->
	               where('estatus', '=', 1)->get();
	            return $nominas;
			} catch (Exception $e) {
				return null;
			}
		}

		function datos($nominaID){
			try {
				$nomina = Nomina::find($nominaID);
				$nomina->percepcionesEfectivo = $this->percepcionesEfectivo($nominaID);
				$nomina->percepcionesDeposito = $this->percepcionesDeposito($nominaID);
				$nomina->deduccionesEfectivo = $this->deduccionesEfectivo($nominaID);
				$nomina->deduccionesDeposito = $this->deduccionesDeposito($nominaID);
				$nomina->totalEfectivo = floatval($nomina->percepcionesEfectivo) - floatval($nomina->deduccionesEfectivo);
				$nomina->totalDeposito = floatval($nomina->percepcionesDeposito) - floatval($nomina->deduccionesDeposito);
				return $nomina;
			} catch (Exception $e) {
				return null;
			}
		}

		function percepcionesEfectivo($nominaID){
			try {
				return Percepcione::where('idNomina', '=', $nominaID)->where('idFormaPago', '=', 1)->sum('monto');
			} catch (Exception $e) {
				return null;
			}
		}

		function percepcionesDeposito($nominaID){
			try {
				return Percepcione::where('idNomina', '=', $nominaID)->where('idFormaPago', '=', 4)->sum('monto');
			} catch (Exception $e) {
				return null;
			}
		}

		function deduccionesEfectivo($nominaID){
			try {
				return Deduccione::where('idNomina', '=', $nominaID)->where('idFormaPago', '=', 1)->sum('monto');
			} catch (Exception $e) {
				return null;
			}
		}

		function deduccionesDeposito($nominaID){
			try {
				return Deduccione::where('idNomina', '=', $nominaID)->where('idFormaPago', '=', 4)->sum('monto');
			} catch (Exception $e) {
				return null;
			}
		}

		function crearEgreso($nomina, $usuario, $total, $forma){
			try {
				$egreso = Egreso::create([
                    'concepto' => (intval($forma) === 1) ? 'Pago en Efectivo a Nomina' : 'Pago en Deposito a Nomina',
                    'monto' => $total,
                    'observaciones' => $nomina->observaciones,
                    'idRubro' => 3,
                    'idTipo' => 4,
                    'idSucursal' => $nomina->idSucursal,
                    'idCalendario' => $nomina->idCalendario,
                    'idCuenta' => 1,
                    'idFormaPago' => $forma,
                    'idUsuario' => $usuario,
                    'referencia' => 3,
                    'idNivel' => $nomina->idNivel,
                    'activo' => 1,
                    'eliminado' => 0,
                ]);

                $registro = Nominaegreso::create([
                    'idNomina' => $nomina->id,
                    'idEgreso' => $egreso->id,
                    'eliminado' => 0,
                    'activo' => 1,
                    'tipo' => (intval($forma) === 1) ? 1 : 2
                ]);

                return true;
			} catch (Exception $e) {
				return null;
			}
		}

		function cobrar($nominaID){
			try {
				$nomina = Nomina::find($nominaID);
				$nomina->estatus = 2;
				$nomina->save();
				return $nomina;
			} catch (Exception $e) {
				return null;
			}
		}

		function solicitudes(){
			try {
				return Solicitudnomina::join('nominas', 'idNomina', '=', 'nominas.id')->
	            join('usuarios', 'solicitudnominas.idUsuario', '=', 'usuarios.id')->
	            join('empleados', 'usuarios.idEmpleado', '=', 'empleados.id')->
	            leftjoin('formaspagos', 'solicitudnominas.idFormaPago', '=', 'formaspagos.id')->
	            leftJoin('conceptospercepciones', 'idConcepto', '=', 'conceptospercepciones.id')->
	            select(
	                'nominas.folio as folio',
	                'solicitudnominas.*',
	                'empleados.nombre as empleado',
	                'formaspagos.nombre as forma',
	                'conceptospercepciones.nombre as concepto',
	                DB::raw("IF(solicitudnominas.forma = 1, 'Agregar', 'Eliminar') as accion"),
	                DB::raw("IF(solicitudnominas.tipo = 1, 'Percepcion', 'Deduccion') as tipoCambio"),
	                DB::raw("(CASE 
	                            WHEN(solicitudnominas.estatus = 2) THEN 'bg-verde'
	                            WHEN(solicitudnominas.estatus = 3) THEN 'bg-rojo'
	                            END) AS bg")
	            )->
	            where('solicitudnominas.eliminado', '=', 0)->get();
			} catch (Exception $e) {
				return null;
			}
		}

		function existeEgresoEfectivo($nominaID){
			try {
				return count(Nominaegreso::where('idNomina', '=', $nominaID)->where('tipo', '=', 1)->get()) > 0;
			} catch (Exception $e) {
				return null;
			}
		}

		function existeEgresoDeposito($nominaID){
			try {
				return count(Nominaegreso::where('idNomina', '=', $nominaID)->where('tipo', '=', 2)->get()) > 0;
			} catch (Exception $e) {
				return null;
			}
		}

		function traerSolicitud($solicitudID){
			try {
				return Solicitudnomina::find($solicitudID);
			} catch (Exception $e) {
				return null;
			}
		}

		function agregarPercepcion($nominaID, $formaID, $monto, $conceptoID, $cantidad, $valorUnitario){
			try {
				return Percepcione::create([
                    'idNomina' => $nominaID,
                    'idFormaPago' => $formaID,
                    'monto' => $monto,
                    'idConcepto' => $conceptoID,
                    'cantidad' => $cantidad,
                    'valorUnitario' => $valorUnitario,
                    'eliminado' => 0,
                    'activo' => 1
                ]);
			} catch (Exception $e) {
				return null;
			}
		}

		function eliminarPercepcion($id){
			try {
				$percepcion = Percepcione::find($id);
				$percepcion->eliminado = 1;
				$percepcion->save();
				return $percepcion;
			} catch (Exception $e) {
				return null;
			}
		}

		function agregarDeduccion($nominaID, $formaID, $monto, $conceptoID, $cantidad, $valorUnitario){
			Deduccione::create([
                'idNomina' => $nominaID,
                'idFormaPago' => $formaID,
                'monto' => $monto,
                'idConcepto' => $conceptoID,
                'cantidad' => $cantidad,
                'valorUnitario' => $valorUnitario,
                'eliminado' => 0,
                'activo' => 1
            ]);
		}

		function eliminarDeduccion($id){
			try {
				$deduccion = Deduccione::find($id);
				$deduccion->eliminado = 1;
				$deduccion->save();
				return $deduccion;
			} catch (Exception $e) {
				return null;
			}
		}

		function idEgresoEfectivo($nominaID){
			try {
				return Nominaegreso::where('idNomina', '=', $nominaID)->where('tipo', '=', 1)->get()[0]->idEgreso;
			} catch (Exception $e) {
				return null;
			}
		}

		function idEgresoDeposito($nominaID){
			try {
				return Nominaegreso::where('idNomina', '=', $nominaID)->where('tipo', '=', 2)->get()[0]->idEgreso;
			} catch (Exception $e) {
				return null;
			}
		}

		function agregarDeducciones($deducciones, $nominaID){
			try {
				foreach ($deducciones as $deduccion) {
					Deduccione::create([
		                'idNomina' => $nominaID,
		                'idFormaPago' => $deduccion['idFormaPago'],
		                'monto' => $deduccion['monto'],
		                'idConcepto' => $deduccion['idConcepto'],
		                'cantidad' => $deduccion['cantidad'],
		                'valorUnitario' => $deduccion['valorUnitario'],
		                'eliminado' => 0,
		                'activo' => 1
		            ]);
				}
				return true;
			} catch (Exception $e) {
				return false;
			}
		}

		function agregarPercepciones($percepciones, $nominaID){
			try {
				foreach ($percepciones as $percepcion) {
					Percepcione::create([
	                    'idNomina' => $nominaID,
	                    'idFormaPago' => $percepcion['idFormaPago'],
	                    'monto' => $percepcion['monto'],
	                    'idConcepto' => $percepcion['idConcepto'],
	                    'cantidad' => $percepcion['cantidad'],
	                    'valorUnitario' => $percepcion['valorUnitario'],
	                    'eliminado' => 0,
	                    'activo' => 1
	                ]);
				}
				return true;
			} catch (Exception $e) {
				return false;
			}
		}

		//modificaciones por campo nomina
		function actualizarTotal($nominaID){
			try {
				$percepciones = $this->percepcionesDeposito($nominaID) + $this->percepcionesEfectivo($nominaID);
				$deducciones = $this->deduccionesDeposito($nominaID) + $this->deduccionesEfectivo($nominaID);
				$nomina = Nomina::find($nominaID);
				$nomina->total = $percepciones - $deducciones;
				$nomina->save();
				return true;
			} catch (Exception $e) {
				return false;
			}
		}

		function total($id, $total){
			try {
				$nomina = Nomina::find($id);
				$nomina->total = $total;
				$nomina->save();
				return $nomina;
			} catch (Exception $e) {
				return null;
			}
		}

		function nueva($empleado, $sucursalID){
			try {
				$folios = new Folios();
				return Nomina::create([
		            'idEmpleado' => $empleado['idEmpleado'],
		            'idCalendario' => $empleado['idCalendario'],
		            'idNivel' => $empleado['idNivel'],
		            'idSucursal' => $sucursalID,
		            'idPuesto' => $empleado['idPuesto'],
		            'idDepartamento' => $empleado['idDepartamento'],
		            'quincena' => $empleado['quincena'],
		            'fechaInicio' => $empleado['inicio'],
		            'fechaFin' => $empleado['fin'],
		            'fechaExpedicion' => $empleado['expedicion'],
		            'observaciones' => $empleado['observaciones'],
		            'estatus' => 0,
		            'eliminado' => 0,
		            'activo' => 1,
		            'folio' => $folios->proximoNomina($empleado['idDepartamento'], $empleado['idNivel'], $empleado['idCalendario'], $sucursalID),
		            'idBanco' => $empleado['idBanco']
		        ]);
			} catch (Exception $e) {
				return null;
			}
		}

		function crear($empleadoID, $calendarioID, $nivelID, $sucursalID, $puestoID, $departamentoID, $quincena, $inicio, $fin, $expedicion, $observaciones, $bancoID, $folio){
			try {
				return Nomina::create([
		            'idEmpleado' => $empleadoID,
		            'idCalendario' => $calendarioID,
		            'idNivel' => $nivelID,
		            'idSucursal' => $sucursalID,
		            'idPuesto' => $puestoID,
		            'idDepartamento' => $departamentoID,
		            'quincena' => $quincena,
		            'fechaInicio' => $inicio,
		            'fechaFin' => $fin,
		            'fechaExpedicion' => $expedicion,
		            'observaciones' => $observaciones,
		            'estatus' => 0,
		            'eliminado' => 0,
		            'activo' => 1,
		            'folio' => $folio,
		            'idBanco' => $bancoID
		        ]);
			} catch (Exception $e) {
				return null;
			}
		}

		function traer($id){
			try {
				return Nomina::find($id);
			} catch (Exception $e) {
				return null;
			}
		}

		function modificar($id, $inicio, $fin, $expedicion, $quincena, $observaciones){
			try {
				$nomina = Nomina::find($id);
	            $nomina->quincena = $quincena;
	            $nomina->fechaInicio = $inicio;
	            $nomina->fechaFin = $fin;
	            $nomina->fechaExpedicion = $expedicion;
	            $nomina->observaciones = $observaciones;
	            $nomina->save();
	            return $nomina;
			} catch (Exception $e) {
				return null;
			}
		}

		function estatus($id){
			try {
				return Nomina::find($id)->estatus;
			} catch (Exception $e) {
				return null;
			}
		}

		function percepciones($id){
			try {
				return Percepcione::join('conceptospercepciones', 'idConcepto', '=', 'conceptospercepciones.id')->
                select('percepciones.*', 'conceptospercepciones.nombre as concepto')->
                where('idNomina', '=', $id)->
                where('percepciones.eliminado', '=', 0)->get();
			} catch (Exception $e) {
				return null;
			}
		}

		function deducciones($id){
			try {
				return Deduccione::join('conceptosdeducciones', 'idConcepto', '=', 'conceptosdeducciones.id')->
               	select('deducciones.*', 'conceptosdeducciones.nombre as concepto')->
               	where('idNomina', '=', $id)->
               	where('deducciones.eliminado', '=', 0)->get();
			} catch (Exception $e) {
				return null;
			}
		}

		
	}
?>