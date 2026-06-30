<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use App\Sucursale;
use App\Calendario;
use App\Nivele;

class Egreso extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $fillable = [
        'id',
        'concepto',
        'monto',
        'observaciones',
        'idRubro',
        'idTipo',
        'idSucursal',
        'idCalendario',
        'idFormaPago',
        'idUsuario',
        'activo',
        'eliminado',
        'created_at',
        'update_at',
        'referencia',
        'idNivel',
        'folio',
        'idCuenta',
        'voucher',
        'idSucursalGasto'
    ];

    protected $hidden = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($egreso) {

            if (empty($egreso->folio)) {

                $cantidad = self::where('idNivel', $egreso->idNivel)
                    ->where('idCalendario', $egreso->idCalendario)
                    ->where('idSucursal', $egreso->idSucursal)
                    ->count();

                $sucursal = Sucursale::find($egreso->idSucursal);
                $calendario = Calendario::find($egreso->idCalendario);
                $nivel = Nivele::find($egreso->idNivel);

                if ($sucursal && $calendario && $nivel) {

                    $separados = explode('-', $calendario->nombre);

                    $egreso->folio =
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