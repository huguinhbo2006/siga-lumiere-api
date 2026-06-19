<?php  

  namespace App\Clases;
  use App\Alumnodescuento;
  class Descuentos{

  	function nuevo($fichaID, $monto, $concepto, $conceptoID, $tipo, $cantidad, $usuarioID){
  		try {
  			return Alumnodescuento::create([
                'idFicha' => $fichaID,
                'monto' => $monto,
                'concepto' => $concepto,
                'idUsuario' => $usuarioID,
                'eliminado' => 0,
                'activo' => 1,
                'idConcepto' => $conceptoID,
                'tipo' => $tipo,
                'cantidad' => $cantidad
            ]);
  		} catch (Exception $e) {
  			return null;
  		}
  	}

  	function eliminar($id){
  		try {
  			$descuento = Alumnodescuento::find($id);
  			$descuento->eliminado = 1;
  			$descuento->save();
  			return $descuento;
  		} catch (Exception $e) {
  			return null;
  		}
  	}
  }

?>