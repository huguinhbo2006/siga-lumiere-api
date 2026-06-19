<?php  

  namespace App\Clases;
  use App\Prospecto;
  use App\Seguimiento;

  class CRM{

  	function existeProspecto($celular, $nombre, $apellidoPaterno, $apellidoMaterno){
  		try {
  			return Prospecto::where('celular', '=', $celular)->
  			orWhereRaw("(
  				nombre = '$nombre' AND apellidoPaterno = '$apellidoPaterno' AND apellidoMaterno = '$apellidoMaterno'
  			)")->count() > 0;
  		} catch (Exception $e) {
  			return false;
  		}
  	}

  	function hayProspecto($datos){
  		try {
  			$celular = $datos['celular'];
  			$nombre = $datos['nombre'];
  			$apellidoPaterno = $datos['apellidoPaterno'];
  			$apellidoMaterno = $datos['apellidoMaterno'];
  			return Prospecto::where('celular', '=', $celular)->
  			orWhereRaw("(
  				nombre = '$nombre' AND apellidoPaterno = '$apellidoPaterno' AND apellidoMaterno = '$apellidoMaterno'
  			)")->count() > 0;
  		} catch (Exception $e) {
  			return false;
  		}
  	}

  	function traerProspecto($celular, $nombre, $apellidoPaterno, $apellidoMaterno){
  		try {
  			return Prospecto::where('celular', '=', $celular)->
  			orWhereRaw("(
  				nombre = '$nombre' AND apellidoPaterno = '$apellidoPaterno' AND apellidoMaterno = '$apellidoMaterno'
  			)")->get()[0];
  		} catch (Exception $e) {
  			return false;
  		}
  	}

  	function obtenerProspecto($datos){
  		try {
  			$celular = $datos['celular'];
  			$nombre = $datos['nombre'];
  			$apellidoPaterno = $datos['apellidoPaterno'];
  			$apellidoMaterno = $datos['apellidoMaterno'];
  			return Prospecto::where('celular', '=', $celular)->
  			orWhereRaw("(
  				nombre = '$nombre' AND apellidoPaterno = '$apellidoPaterno' AND apellidoMaterno = '$apellidoMaterno'
  			)")->get()[0];
  		} catch (Exception $e) {
  			return false;
  		}
  	}

  	function obtenerSeguimiento($id){
  		try {
  			return Seguimiento::find($id);
  		} catch (Exception $e) {
  			return null;
  		}
  	}

  	function haySeguimientoProspecto($prospectoID){
  		try {
  			return Seguimiento::where('idProspecto', '=', $prospectoID)->
  			where(function($query){
                $query->where('estatus', '=', 0)
                ->orWhere('estatus', '=', 1);
            })->count() > 0;
  		} catch (Exception $e) {
  			return null;
  		}
  	}

  	function obtenerSeguimientoProspecto($prospectoID){
  		try {
  			return Seguimiento::where('idProspecto', '=', $prospectoID)->
  			where(function($query){
                $query->where('estatus', '=', 0)
                ->orWhere('estatus', '=', 1);
            })->get()[0];
  		} catch (Exception $e) {
  			return null;
  		}
  	}
  }

?>