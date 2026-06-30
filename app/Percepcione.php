<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Percepcione extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $fillable = [
        'updated_at',
        'created_at',
        'activo',
        'eliminado',
        'id',
        'idConcepto',
        'monto',
        'idNomina',
        'cantidad',
        'idFormaPago',
        'valorUnitario'
    ];

    protected $hidden = [];

    protected $attributes = [
        'activo' => 1,
        'eliminado' => 0
    ];

    protected $appends = [
        'concepto',
        'forma'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($percepcion) {
            if ($percepcion->Nomina) {
                $percepcion->Nomina->recalcularTotal();
            }
        });

        static::deleted(function ($percepcion) {
            if ($percepcion->Nomina) {
                $percepcion->Nomina->recalcularTotal();
            }
        });
    }

    public function Nomina()
    {
        return $this->belongsTo(\App\Nomina::class, 'idNomina', 'id');
    }

    public function RelConceptos()
    {
        return $this->belongsTo(\App\Conceptospercepcione::class, 'idConcepto', 'id');
    }

    public function RelFormasPagos()
    {
        return $this->belongsTo(\App\Formaspago::class, 'idFormaPago', 'id');
    }

    public function getConceptoAttribute()
    {
        return optional($this->RelConceptos)->nombre;
    }

    public function getFormaAttribute()
    {
        return optional($this->RelFormasPagos)->nombre;
    }
}