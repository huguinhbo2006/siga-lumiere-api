<?php  

  namespace App\Clases;
  use App\Alumnoextra;
  class Extras{

  	function nuevo($fichaID, $conceptoID, $concepto, $monto, $usuarioID){
  		try{
  			return Alumnoextra::create([
  				'idFicha' => $fichaID,
  				'idUsuario' => $usuarioID,
  				'monto' => $monto,
  				'idConcepto' => $conceptoID,
  				'concepto' => $concepto,
  				'activo' => 1,
  				'eliminado' => 0
  			]);
  		}catch(Exception ){
  			return null;
  		}
  	}

  	function eliminar($id){
  		try {
  			$extra = Alumnoextra::find($id);
  			$extra->eliminado = 1;
  			$extra->save();
  			return $extra;
  		} catch (Exception $e) {
  			return null;
  		}
  	}
  }

?>