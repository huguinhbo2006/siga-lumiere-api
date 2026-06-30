<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Nomina extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $fillable = [
        'updated_at',
        'created_at',
        'activo',
        'eliminado',
        'id',
        'folio',
        'idEmpleado',
        'idCalendario',
        'idNivel',
        'idSucursal',
        'idDepartamento',
        'idPuesto',
        'estatus',
        'quincena',
        'fechaInicio',
        'fechaFin',
        'fechaExpedicion',
        'total',
        'idBanco',
        'observaciones'
    ];

    protected $hidden = [];

    protected $attributes = [
        'activo' => 1,
        'eliminado' => 0,
        'total' => 0
    ];

    public function Percepciones()
    {
        return $this->hasMany(\App\Percepcione::class, 'idNomina', 'id');
    }

    public function Deducciones()
    {
        return $this->hasMany(\App\Deduccione::class, 'idNomina', 'id');
    }

    public function recalcularTotal()
    {
        $percepciones = $this->Percepciones()
            ->where('eliminado', 0)
            ->sum('monto');

        $deducciones = $this->Deducciones()
            ->where('eliminado', 0)
            ->sum('monto');

        $this->total = $percepciones - $deducciones;
        $this->save();

        return $this->total;
    }
}