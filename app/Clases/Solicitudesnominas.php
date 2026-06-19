<?php  

  namespace App\Clases;
  use App\Solicitudnomina;

  class Solicitudesnominas{

  	function nuevo($nominaID, $formaID, $monto, $conceptoID, $cantidad, $valor, $usuarioID, $tipo, $forma, $id){
		try {
			return Solicitudnomina::create([
                'forma' => $forma,
                'idModificacion' => $id,
                'idNomina' => $nominaID,
                'idFormaPago' => $formaID,
                'monto' => $monto,
                'idConcepto' => $conceptoID,
                'cantidad' => $cantidad,
                'valorUnitario' => $valor,
                'idUsuario' => $usuarioID,
                'estatus' => 1,
                'tipo' => $tipo,
                'eliminado' => 0,
                'activo' => 1
            ]);
		} catch (Exception $e) {
			return null;
		}
	}
  }

?>