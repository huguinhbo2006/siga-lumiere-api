<?php  

  namespace App\Clases;
  use App\Alumnodevolucione;
  class Devoluciones{
  	function nuevo($fichaID, $conceptoID, $formaID, $monto, $concepto, $usuarioID, $egresoID){
  		try{
  			return Alumnodevolucione::create([
                'idUsuario' => $usuarioID, 
                'monto' => $monto,
                'idConcepto' => $conceptoID,
                'concepto' => $concepto,
                'idFicha' => $fichaID,
                'idEgreso' => $egresoID,
                'idFormaPago' => $formaID,
                'eliminado' => 0,
                'activo' => 1
            ]);
  		}catch(Exception ){
  			return null;
  		}
  	}

  	function eliminar($id){
  		try{
  			$devolucion = Alumnodevolucione::find($id);
  			$devolucion->eliminado = 1;
  			$devolucion->save();
  			return $devolucion;
  		}catch(Exception ){
  			return null;
  		}
  	}
  }

?>