<?php  

  namespace App\Clases;
  use App\Alumnocargo;

  class Cargos{

  	function nuevo($fichaID, $conceptoID, $concepto, $monto, $usuarioID){
  		try {
  			return Alumnocargo::create([
                'idFicha' => $fichaID,
                'monto' => $monto,
                'concepto' => $concepto,
                'idUsuario' => $usuarioID,
                'eliminado' => 0,
                'activo' => 1,
                'idConcepto' => $conceptoID
            ]);
  		} catch (Exception $e) {
  			return null;
  		}
  	}

  	function eliminar($id){
  		try {
  			$cargo = Alumnocargo::find($id);
  			$cargo->eliminado = 1;
  			$cargo->save();
  			return $cargo;
  		} catch (Exception $e) {
  			return null;
  		}
  	}
  }

?>