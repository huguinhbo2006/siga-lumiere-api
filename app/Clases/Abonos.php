<?php  

  namespace App\Clases;
  use App\Alumnoabono;

  class Abonos{

  	function nuevo($fichaID, $ingresoID, $usuarioID, $monto, $concepto, $metodoID, $formaID, $conceptoID){
  		try {
  			return Alumnoabono::create([
                'idFicha' => $fichaID,
                'idIngreso' => $ingresoID,
                'idUsuario' => $usuarioID,
                'monto' => $monto,
                'concepto' => $concepto,
                'idMetodoPago' => $metodoID,
                'idFormaPago' => $formaID,
                'activo' => 1, 
                'eliminado' => 0,
                'idConcepto' => $conceptoID
            ]);
  		} catch (Exception $e) {
  			return null;
  		}
  	}

    function eliminar($id){
      try {
        $abono = Alumnoabono::find($id);
        $abono->eliminado = 1;
        $abono->save();
        return $abono;
      } catch (Exception $e) {
        return null;
      }
    }
  }

?>