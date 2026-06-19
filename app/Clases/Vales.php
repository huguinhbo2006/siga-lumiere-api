<?php

	namespace App\Clases;
	use Illuminate\Support\Facades\DB;
	use App\Vale;
	use App\Ingreso;
	use App\Nivele;
	use App\Calendario;
	use App\Clases\Folios;

	class Vales{

		function recibidos(){
			try {
				return Vale::leftjoin('calendarios', 'vales.idCalendario', '=', 'calendarios.id')->
	            join('sucursales', 'idSucursalSalida', '=', 'sucursales.id')->
	            select(
	                'vales.*',
	                'calendarios.nombre as calendario',
	                DB::raw('CONCAT(calendarios.inicio, " - ", calendarios.fin) as fecha'),
	                'sucursales.nombre as sucursal',
	                DB::raw("CONCAT('$',FORMAT(vales.monto,2)) AS montoFormato"),
	                DB::raw("DATE_FORMAT(vales.created_at, '%d-%m-%Y %H:%i:%s') as fechaFormato")
	            )->
	            where('vales.eliminado', '=', 0)->
	            where('vales.aceptado', '=', 0)->get();
			} catch (Exception $e) {
				return null;
			}
		}

		function creados($sucursalID){
			try {
				return Vale::leftjoin('calendarios', 'idCalendario', '=', 'calendarios.id')->
                    select(
                        'vales.id',
                        'vales.monto',
                        'calendarios.nombre as calendario',
                        'vales.idSucursalEntrada',
                        'vales.idSucursalSalida',
                        'vales.idCalendario',
                        'vales.idEgreso',
                        'vales.observaciones',
                        'vales.folio',
                        DB::raw("(CASE 
                            WHEN(vales.aceptado = 0) THEN 'bg-amarillo'
                            WHEN(vales.aceptado = 1) THEN 'bg-verde'
                            WHEN(vales.aceptado = 2) THEN 'bg-rojo'
                            END) AS bg")
                        )->
                    where('vales.aceptado', '<>', 2)->
                    where('vales.aceptado', '<>', 1)->
                    where('vales.idSucursalSalida', '=', $sucursalID)->
                    where('vales.eliminado', '=', 0)->get();
			} catch (Exception $e) {
				return null;
			}
		}

		function crearIngreso($datos){
			try {
				$funcionesFolios = new Folios();
				$folio = $funcionesFolios->proximoIngreso($datos['idNivel'], $datos['idCalendario'], 1);

				$ingreso = Ingreso::create([
	                'concepto' => 'Vale de Sucursal '.$datos['entrada'],
	                'monto' => $datos['monto'],
	                'observaciones' => $datos['observaciones'],
	                'idRubro' => 2,
	                'idTipo' => 3,
	                'idSucursal' => $datos['idRecepcion'],
	                'idCalendario' => $datos['idCalendario'],
	                'idFormaPago' => 1,
	                'idMetodoPago' => 1,
	                'idUsuario' => $datos['usuarioID'],
	                'idNivel' => $datos['idNivel'],
	                'folio' => $folio,
	                'referencia' => 4,
	                'activo' => 1,
	                'eliminado' => 0,
	            ]);
				return $ingreso;
			} catch (Exception $e) {
				return null;
			}
		}

		function listas(){
			try {
				return array(
					'calendarios' => Calendario::where('eliminado', '=', 0)->get(),
					'niveles' => Nivele::where('eliminado', '=', 0)->get()
				);
			} catch (Exception $e) {
				return null;
			}
		}

		function crearVale($sucursalID, $monto, $calendarioID, $observaciones, $usuarioID, $egresoID, $folio, $nivelID){
			try {
				return Vale::create([
	                'idSucursalSalida' => $sucursalID,
	                'idSucursalEntrada' => -1,
	                'monto' => $monto,
	                'aceptado' => 0,
	                'idCalendario' => $calendarioID,
	                'observaciones' => $observaciones,
	                'idUsuarioCreo' => $usuarioID,
	                'idEgreso' => $egresoID,
	                'folio' => $folio,
	                'idNivel' => $nivelID,
	                'activo' => 1,
	                'eliminado' => 0
	            ]);
			} catch (Exception $e) {
				return null;
			}
		}
	}

	
?>