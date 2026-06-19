<?php

	namespace App\Clases;
	use Carbon\Carbon;
	use App\Egreso;
	use App\Ingreso;
	use App\Sucursale;
	use App\Calendario;
	use App\Nivele;
	use App\Vale;
	use App\Ficha;
	use App\Valesgerenciale;
	use App\Nomina;
	use App\Departamento;
	use Illuminate\Support\Facades\DB;

	class Folios{
		function proximoIngreso($idNivel, $idCalendario, $idSucursal){
			try {
				$cantidad = Ingreso::where('idNivel', '=', $idNivel)->
	            where('idCalendario', '=', $idCalendario)->
	            where('idSucursal', '=', $idSucursal)->get();
	            $sucursal = Sucursale::find($idSucursal);
	            $calendario = Calendario::find($idCalendario);
	            $nivel = Nivele::find($idNivel);
	            $separados = explode("-", $calendario->nombre);
	            $folio = substr($separados[0], -2).$separados[1].substr($nivel->nombre, 0, 1).$sucursal->abreviatura.'-'.(count($cantidad) + 1);
	            return $folio;
			} catch (Exception $e) {
				return null;
			}
		}

		function proximoEgreso($idNivel, $idCalendario, $idSucursal){
			try {
				$cantidad = Egreso::where('idNivel', '=', $idNivel)->
	            where('idCalendario', '=', $idCalendario)->
	            where('idSucursal', '=', $idSucursal)->get();
	            $sucursal = Sucursale::find($idSucursal);
	            $calendario = Calendario::find($idCalendario);
	            $nivel = Nivele::find($idNivel);
	            $separados = explode("-", $calendario->nombre);
	            $folio = substr($separados[0], -2).$separados[1].substr($nivel->nombre, 0, 1).$sucursal->abreviatura.'-'.(count($cantidad) + 1);
	            return $folio;
			} catch (Exception $e) {
				return null;
			}
		}

		function proximoVale($sucursalID, $calendarioID){
			try {
				return 'V-'.Vale::where('idSucursalSalida', '=', $sucursalID)->where('idCalendario', '=', $calendarioID)->get()->count()+1;
			} catch (Exception $e) {
				return null;
			}
		}

		function proximoFicha($calendarioID, $nivelID, $sucursalID){
			try {
				$fichas = Ficha::where('idCalendario', '=', $calendarioID)->
				where('idSucursalInscripcion', '=', $sucursalID)->
				where('idNivel', '=', $nivelID)->get();
				
				$nivel = Nivele::find($nivelID);
				$sucursal = Sucursale::find($sucursalID);
				$calendario = Calendario::find($calendarioID);
				$separacion = explode("-", $calendario->nombre);

	            $folioFicha = substr($separacion[0], -2).$separacion[1].substr($nivel->nombre, 0, 1)."-".$sucursal->abreviatura.(count($fichas)+1);
	            return $folioFicha;
			} catch (Exception $e) {
				return null;
			}
		}

		function proximoValeGerencial($calendarioID, $sucursalID){
			try {
				return 'V-'.Valesgerenciale::where('idCalendario', '=', $calendarioID)->where('idSucursal', '=', $sucursalID)->get()->count()+1;
			} catch (Exception $e) {
				return null;
			}
		}

		function proximoNomina($departamentoID, $nivelID, $calendarioID, $sucursalID){
			try {
				$cantidad = Nomina::where('idCalendario', '=', $calendarioID)->
                                where('idNivel', '=', $nivelID)->
                                where('idDepartamento', '=', $departamentoID)->
                                where('idSucursal', '=', $sucursalID)->get();
	            $departamento = Departamento::find($departamentoID);
	            $calendario = Calendario::find($calendarioID);
	            $nivel = Nivele::find($nivelID);

	            $separado[] = explode('-', $calendario->nombre);
		        $year = $separado[0][0];
		        $letra = $separado[0][1]; 
		        $folio = substr($year, -2).$letra.substr($nivel->nombre, 0, 1).'-'.$departamento->abreviatura.(count($cantidad)+1);

		        return $folio;
			} catch (Exception $e) {
				return null;
			}
		}
	}

	
?>