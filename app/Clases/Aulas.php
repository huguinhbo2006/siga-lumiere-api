<?php  

  namespace App\Clases;
  use App\Aula;

  class Aulas{


  	function nueva($nombre, $cupo, $sucursalID){
  		try {
  			return Aula::create([
  				'nombre' => $nombre,
  				'cupo' => $cupo,
  				'idSucursal' => $sucursalID,
  				'activo' => 1,
  				'eliminado' => 0
  			]);
  		} catch (Exception $e) {
  			return null;
  		}
  	}

  	function traer($sucursalID){
  		try {
  			return Aula::where('idSucursal', '=', $sucursalID)->where('eliminado', '=', 0)->where('activo', '=', 1)->get();
  		} catch (Exception $e) {
  			return null;
  		}
  	}

    function activos($sucursalID){
      try {
        return Aula::where('eliminado', '=', 0)->where('activo', '=', 1)->where('idSucursal', '=', $sucursalID)->get();
      } catch (Exception $e) {
        return null;
      }
    }

    function activar($id){
      try {
        $aula = Aula::find($id);
        $aula->activo = 1;
        $aula->save();
        return $aula;
      } catch (Exception $e) {
        return null;
      }
    }

    function desactivar($id){
      try {
        $aula = Aula::find($id);
        $aula->activo = 0;
        $aula->save();
        return $aula;
      } catch (Exception $e) {
        return null;
      }
    }

    function eliminar($id){
      try {
        $aula = Aula::find($id);
        $aula->eliminado = 1;
        $aula->save();
        return $aula;
      } catch (Exception $e) {
        return null;
      }
    }

    function modificar($id, $nombre, $cupo){
      try {
        $aula = Aula::find($id);
        $aula->nombre = $nombre;
        $aula->cupo = $cupo;
        $aula->save();
        return $aula;
      } catch (Exception $e) {
        return null;
      }
    }
  }

?>