<?php  

  namespace App\Clases;
  use App\Conceptosdeduccione;

  class Deducciones{

  	function conceptos($docentes){
  		try{
  			return Conceptosdeduccione::where('docentes', '=', $docentes)->get();
  		}catch(Exception ){
  			return null;
  		}
  	}
  }

?>