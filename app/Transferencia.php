<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Transferencia extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'updated_at',
        'created_at',
        'idSucursalSalida',
        'idSucursalEntrada',
        'monto',
        'id',
        'aceptado',
        'observaciones',
        'idCalendario',
        'idEgreso',
        'eliminado',
        'activo',
        'idUsuarioCreo',
        'idUsuarioAcepto',
        'idNivel',
        'idIngreso'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Appends
     *
     * @var array
     */
    protected $appends = [
        'entrada',
        'salida',
        'calendario',
        'creo',
        'acepto',
        'nivel',
        'formato',
        'bg',
        'date'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function sucursalEntrada()
    {
        return $this->belongsTo(Sucursale::class, 'idSucursalEntrada');
    }

    public function sucursalSalida()
    {
        return $this->belongsTo(Sucursale::class, 'idSucursalSalida');
    }

    public function calendarioSeleccionado()
    {
        return $this->belongsTo(Calendario::class, 'idCalendario');
    }

    public function usuarioCreo()
    {
        return $this->belongsTo(Usuario::class, 'idUsuarioCreo');
    }

    public function usuarioAcepto()
    {
        return $this->belongsTo(Usuario::class, 'idUsuarioAcepto');
    }

    public function nivelSeleccionado()
    {
        return $this->belongsTo(Nivele::class, 'idNivel');
    }

    public function ingresoSeleccionado()
    {
        return $this->belongsTo(Ingreso::class, 'idIngreso');
    }

    public function egresoSeleccionado()
    {
        return $this->belongsTo(Egreso::class, 'idEgreso');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getEntradaAttribute()
    {
        return $this->sucursalEntrada
            ? $this->sucursalEntrada->nombre
            : null;
    }

    public function getSalidaAttribute()
    {
        return $this->sucursalSalida
            ? $this->sucursalSalida->nombre
            : null;
    }

    public function getCalendarioAttribute()
    {
        return $this->calendarioSeleccionado
            ? $this->calendarioSeleccionado->nombre
            : null;
    }

    public function getCreoAttribute()
    {
        return $this->usuarioCreo
            ? $this->usuarioCreo->nombre
            : null;
    }

    public function getAceptoAttribute()
    {
        return $this->usuarioAcepto
            ? $this->usuarioAcepto->nombre
            : null;
    }

    public function getNivelAttribute()
    {
        return $this->nivelSeleccionado
            ? $this->nivelSeleccionado->nombre
            : null;
    }

    public function getFormatoAttribute()
    {
        return '$' . number_format($this->monto, 2, '.', ',');
    }

    public function getBgAttribute()
    {
        return $this->aceptado == 1
            ? 'bg-verde'
            : 'bg-rojo';
    }

    public function getDateAttribute()
    {
        return $this->created_at
            ? date('d/m/Y H:i:s', strtotime($this->created_at))
            : null;
    }
}