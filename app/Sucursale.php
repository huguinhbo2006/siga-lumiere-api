<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Sucursale extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $table = 'sucursales';

    protected $fillable = [
        'nombre',
        'activo',
        'direccion',
        'telefono',
        'eliminado',
        'abreviatura',
        'whatsapp',
        'imagen'
    ];

    protected $hidden = [];

    protected $appends = [
        'total_ingresos',
        'total_egresos',
        'total_vales',
        'total_final'
    ];

    /* =========================
       RELACIONES (SOLO EFECTIVO)
       ========================= */

    public function ingresos()
    {
        return $this->hasMany(Ingreso::class, 'idSucursal', 'id')
                    ->where('activo', 1)
                    ->where('eliminado', 0)
                    ->where('idFormaPago', 1)
                    ->where('idCalendario', '>=', 26); // EFECTIVO
    }

    public function egresos()
    {
        return $this->hasMany(Egreso::class, 'idSucursal', 'id')
                    ->where('activo', 1)
                    ->where('eliminado', 0)
                    ->where('idFormaPago', 1)
                    ->where('idCalendario', '>=', 26); // EFECTIVO
    }

    public function valesAdministrativos()
    {
        return $this->hasMany(Valeadministrativo::class, 'idSucursal', 'id')
                    ->where('eliminado', 0)
                    ->where('activo', 1);
    }

    /* =========================
       ACCESSORS
       ========================= */

    public function getTotalIngresosAttribute()
    {
        return $this->ingresos()->sum('monto');
    }

    public function getTotalEgresosAttribute()
    {
        return $this->egresos()->sum('monto');
    }

    public function getTotalValesAttribute()
    {
        return $this->valesAdministrativos()->sum('monto');
    }

    public function getTotalFinalAttribute()
    {
        return
            $this->total_ingresos
            - $this->total_egresos
            - $this->total_vales;
    }
}
