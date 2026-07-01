<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Traspaso extends Model
{
    // Nombre de la tabla SIEMPRE en minúsculas
    protected $table = 'traspasos';

    // Llave primaria
    protected $primaryKey = 'id';

    // Auto-incremento (true por defecto)
    public $incrementing = true;

    // Tipos de la llave primaria
    protected $keyType = 'int';

    // Timestamps activados
    public $timestamps = true;

    protected $fillable = [
        'idIngreso',
        'idEgreso',
        'idFormaPago',
        'monto',
        'idCuenta',
        'activo',
        'eliminado',
    ];

    protected $attributes = [
        'activo' => 1,
        'eliminado' => 0
    ];
}
