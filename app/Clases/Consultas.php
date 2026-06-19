<?php

	namespace App\Clases;
	use Illuminate\Support\Facades\DB;

	class Consultas{
		public static function capturarConsultas(){
			DB::enableQueryLog();
		}

		public static function obtenerConsultas(){
			return DB::getQueryLog();
		}

		public static function start(){
			return DB::beginTransaction();
		}

		public static function commit(){
			return DB::commit();
		}

		public static function rollback(){
			return DB::rollback();
		}
	}

	
?>