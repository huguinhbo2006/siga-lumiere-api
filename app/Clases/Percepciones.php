<?php  

  namespace App\Clases;
  use App\Conceptospercepcione;

  class Percepciones{

  	function nuevo($nominaID, $formaID, $monto, $conceptoID, $cantidad, $valor){
  		try{
  			return Percepcione::create([
                'idNomina' => $nominaID,
                'idFormaPago' => $formaID,
                'monto' => $monto,
                'idConcepto' => $conceptoID,
                'cantidad' => $cantidad,
                'valorUnitario' => $valor,
                'eliminado' => 0,
                'activo' => 1
            ]);
  		}catch(Exception ){
  			return null;
  		}
  	}

  	function conceptos($docentes){
  		try {
  			return Conceptospercepcione::where('docentes', '=', $docentes)->get();
  		} catch (Exception $e) {
  			return null;
  		}
  	}
  }

?>