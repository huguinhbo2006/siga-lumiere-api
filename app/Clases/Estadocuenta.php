<?php  

  namespace App\Clases;
  use App\Alumnoabono;
  use App\Alumnocargo;
  use App\Alumnodescuento;
  use App\Alumnodevolucione;
  use App\Alumnoextra;
  use App\Conceptosabono;
  use App\Conceptoscargo;
  use App\Conceptosdescuento;
  use App\Conceptosdevolucione;
  use App\Conceptosextra;
  use App\Formaspago;
  use App\Metodospago;
  use App\Banco;
  use App\Cuenta;
  use App\Ficha;
  use App\Clases\Fichas;
  class Estadocuenta{

    function listas(){
      try {
        return array(
          'conceptosabonos' => Conceptosabono::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
          'conceptoscargos' => Conceptoscargo::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
          'conceptosdevoluciones' => Conceptosdevolucione::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
          'conceptosdescuentos' => Conceptosdescuento::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
          'conceptosextras' => Conceptosextra::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
          'formas' => Formaspago::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
          'metodos' => Metodospago::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
          'bancos' => Banco::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
          'cuentas' => Cuenta::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
        );
      } catch (Exception $e) {
        return null;
      }
    }

    function cuenta($fichaID){
      try {
        $fichas = new Fichas();
        return array(
          'abonos' => Alumnoabono::where('idFicha', '=', $fichaID)->where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
          'cargos' => Alumnocargo::where('idFicha', '=', $fichaID)->where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
          'descuentos' => Alumnodescuento::where('idFicha', '=', $fichaID)->where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
          'devoluciones' => Alumnodevolucione::where('idFicha', '=', $fichaID)->where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
          'extras' => Alumnoextra::where('idFicha', '=', $fichaID)->where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
          'ficha' => Ficha::find($fichaID),
          'costo' => $fichas->precio($fichaID)
        );
      } catch (Exception $e) {
        return null;
      }
    }
  }

?>