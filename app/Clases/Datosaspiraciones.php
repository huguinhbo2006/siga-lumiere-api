<?php  

  namespace App\Clases;
  use App\Universidade;
  use App\Centrosuniversitario;
  use App\Carrera;
  use App\Aspiracione;

  class Datosaspiraciones{
  	function listas(){
  		try{
  			return array(
  				'universidades' => Universidade::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
  				'centros' => Centrosuniversitario::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
  				'carreras' => Carrera::where('eliminado', '=', 0)->where('activo', '=', 1)->get() 
  			);
  		}catch(Exception ){
  			return null;
  		}
  	}

  	function modificar($id, $universidadID, $centroID, $carreraID){
  		try {
  			$dato = Aspiracione::find($id);
  			$dato->idUniversidad = $universidadID;
  			$dato->idCentroUniversitario = $centroID;
  			$dato->idCarrera = $carreraID;
  			$dato->save();
  			return $dato;
  		} catch (Exception $e) {
  			return null;
  		}
  	}
  }

?>