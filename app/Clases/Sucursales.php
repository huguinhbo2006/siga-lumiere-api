<?php

	namespace App\Clases;
	use App\Clases\Egresos;
	use App\Clases\Ingresos;
	use App\Valeadministrativo;
	use Carbon\Carbon;

	class Sucursales{
		function saldo($sucursal){
			try {
				$egresos = new Egresos();
				$ingresos = new Ingresos();
				return floatval($ingresos->totalEfectivo($sucursal)) - floatval($egresos->totalEfectivo($sucursal)) - Valeadministrativo::where('idSucursal', '=', $sucursal)->sum('monto');
			} catch (Exception $e) {
				return null;
			}
		}
	}

	
?>