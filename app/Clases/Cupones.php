<?php  

  namespace App\Clases;
  use App\Cupone;

  class Cupones{

  	function todos(){
  		try {
	  		return Cupone::count();
  		} catch (Exception $e) {
  			return null;
  		}
  	}

  	function nuevo($monto, $usuarioID, $cantidad, $fichaID, $cupon){
  		try {
  			return Cupone::create([
                'monto' => $monto,
                'idUsuario' => $usuarioID,
                'cantidad' => $cantidad,
                'idFicha' => $fichaID,
                'cupon' => $cupon,
                'eliminado' => 0,
                'activo' => 1
            ]);
  		} catch (Exception $e) {
  			return null;
  		}
  	}
  }

?>