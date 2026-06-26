<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Carbon\Carbon;
use App\Sucursale;
use App\Grupo;
use App\Examen;

class Ficha extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'idAlumno', 'semana', 'idGrupo', 'idSucursalImparticion', 
        'idSucursalInscripcion', 'idUsuario', 'activo', 'eliminado', 
        'created_at', 'update_at', 'idCalendario', 'folio', 'idNivel', 
        'intentos', 'observaciones', 'idUsuarioInformacion', 'fecha', 
        'idTipoPago', 'numeroRegistro'
    ];

    /**
     * MUTATORS & ACCESSORS
     */

    public function getFolioAttribute($value){
        $ficha = Ficha::where('folio', '=', $value)->get()[0];
        $sucursalImparticion = Sucursale::find($ficha->idSucursalImparticion);
        return $value.$sucursalImparticion->abreviatura;
    }

    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->format('d-m-Y H:i:s');
    }

    /**
     * RELACIONES DE ELOQUENT
     */

    /**
     * Una Ficha pertenece a un Grupo
     */
    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'idGrupo');
    }

    /**
     * Trae los exámenes asociados a esta ficha resolviendo las relaciones 
     * de Grupo -> AltaCurso -> ExamenPermisos mediante Eloquent Puro
     * * @return \Illuminate\Database\Eloquent\Collection
     */
    public function examenes()
    {
        $grupo = $this->grupo()->with('altaCurso')->first();

        if (!$grupo || !$grupo->altaCurso) {
            return collect([]);
        }

        $altaCurso = $grupo->altaCurso;

        // Cambiado a Examene (según el nombre de tu clase)
        return Examene::whereHas('permisos', function ($query) use ($altaCurso) {
            $query->where('idNivel', $altaCurso->idNivel)
                  ->where('idSubnivel', $altaCurso->idSubnivel)
                  ->where('idCategoria', $altaCurso->idCategoria);
        })
        ->where(function ($query) use ($altaCurso) {
            $query->where('inicio', '>=', $altaCurso->inicio)
                  ->orWhere('fin', '>=', $altaCurso->inicio);
        })
        ->orderBy('inicio', 'ASC')
        ->get();
    }
}