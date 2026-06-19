<?php  

  namespace App\Clases;
  use App\Medioscontacto;
  use App\Mediospublicitario;
  use App\Viaspublicitaria;
  use App\Motivosinscripcione;
  use App\Motivosbachillerato;
  use App\Campania;
  use App\Empresascurso;
  use App\Publicitario;

  class Datospublicitarios{

  	function listas(){
  		try {
  			return array(
	  			'contacto' => Medioscontacto::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	  			'medios' => Mediospublicitario::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	  			'vias' => Viaspublicitaria::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	  			'motivos' => Motivosinscripcione::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	  			'bachillerato' => Motivosbachillerato::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	  			'campanias' => Campania::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	  			'empresas' => Empresascurso::where('eliminado', '=', 0)->where('activo', '=', 1)->get()
	  		);	
  		} catch (Exception $e) {
  			return null;
  		}
  	}

  	function modificar($id, $contacto, $publicitario, $via, $motivo, $campania, $bachillerato, $empresa, $tomo){
  		try {
  			$dato = Publicitario::find($id);
  			$dato->idMedioContacto = $contacto;
  			$dato->idMedioPublicitario = $publicitario;
  			$dato->idViaPublicitaria = $via;
  			$dato->idMotivoInscripcion = $motivo;
  			$dato->idCampania = $campania;
  			$dato->idMotivoBachillerato = $bachillerato;
  			$dato->idEmpresaCurso = $empresa;
  			$dato->tomoCurso = $tomo;
  			$dato->save();
  			return $dato;
  		} catch (Exception $e) {
  			return null;
  		}
  	}
  }

?>