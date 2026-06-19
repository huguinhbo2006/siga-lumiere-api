<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cuentacorte extends Model
{
    // Nombre de la tabla SIEMPRE en minúsculas
    protected $table = 'cuentacortes';

    // Llave primaria
    protected $primaryKey = 'id';

    // Auto-incremento (true por defecto)
    public $incrementing = true;

    // Tipos de la llave primaria
    protected $keyType = 'int';

    // Timestamps activados
    public $timestamps = true;

    protected $fillable = [
        'idCuenta',
        'monto',
        'fecha',
        'activo',
        'eliminado',
    ];

    protected $attributes = [
        'activo' => 1,
        'eliminado' => 0
    ];
}
