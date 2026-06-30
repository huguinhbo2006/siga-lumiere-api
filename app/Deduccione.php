<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Deduccione extends Model implements AuthenticatableContract, AuthorizableContract
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
        'valorUnitario',
        'cantidad',
        'idFormaPago'
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

        static::saved(function ($deduccion) {
            if ($deduccion->Nomina) {
                $deduccion->Nomina->recalcularTotal();
            }
        });

        static::deleted(function ($deduccion) {
            if ($deduccion->Nomina) {
                $deduccion->Nomina->recalcularTotal();
            }
        });
    }

    public function Nomina()
    {
        return $this->belongsTo(\App\Nomina::class, 'idNomina', 'id');
    }

    public function RelConceptos()
    {
        return $this->belongsTo(\App\Conceptosdeduccione::class, 'idConcepto', 'id');
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