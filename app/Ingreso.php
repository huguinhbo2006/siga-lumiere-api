<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Carbon\Carbon;
use App\Sucursale;
use App\Vale;
use App\Alumnoabono;
use App\Calendario;
use App\Nivele;


class Ingreso extends Model implements  AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->format('d-m-Y');
    }
    
    public function getConceptoAttribute($value){
        return $value;
    }

    
    protected $fillable = [
        'id', 'concepto', 'monto', 'observaciones', 'idRubro', 'idTipo', 'idSucursal', 'idCalendario', 'idFormaPago', 'idMetodoPago', 'idUsuario', 'activo', 'eliminado', 'created_at', 'update_at', 'referencia', 'folio', 'idNivel', 'imagen', 'idBanco', 'nombreCuenta', 'numeroReferencia', 'idCuenta', 'fecha', 'auditado'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $attributes = [
        'activo' => 1,
        'eliminado' => 0
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ingreso) {

            if (is_null($ingreso->activo)) {
                $ingreso->activo = 1;
            }

            if (is_null($ingreso->eliminado)) {
                $ingreso->eliminado = 0;
            }

            if (empty($ingreso->folio)) {

                $cantidad = self::where('idNivel', $ingreso->idNivel)
                    ->where('idCalendario', $ingreso->idCalendario)
                    ->where('idSucursal', $ingreso->idSucursal)
                    ->count();

                $sucursal = Sucursale::find($ingreso->idSucursal);
                $calendario = Calendario::find($ingreso->idCalendario);
                $nivel = Nivele::find($ingreso->idNivel);

                if ($sucursal && $calendario && $nivel) {

                    $separados = explode('-', $calendario->nombre);

                    $ingreso->folio =
                        substr($separados[0], -2) .
                        $separados[1] .
                        substr($nivel->nombre, 0, 1) .
                        $sucursal->abreviatura .
                        '-' .
                        ($cantidad + 1);
                }
            }
        });
    }
}
