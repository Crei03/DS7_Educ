<?php

namespace App\Domain\Curso\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Profesor extends Model
{
    use HasFactory;
    use HasApiTokens;

    protected $table = 'profesores';
    public $timestamps = false;

    protected $fillable = [
        'nombrePrimario',
        'nombreSecundario',
        'apellidoPrimario',
        'apellidoSecundario',
        'cedula',
        'correo',
        'contrasena',
    ];

    protected $hidden = [
        'contrasena',
    ];

    protected function casts(): array
    {
        return [
            'creado_en' => 'datetime',
            'contrasena' => 'hashed',
        ];
    }

    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class, 'profesor_id');
    }

    public function fullName(): string
    {
        $nombre = $this->nombrePrimario;
        if ($this->nombreSecundario) {
            $nombre .= ' ' . $this->nombreSecundario;
        }

        $apellido = $this->apellidoPrimario;
        if ($this->apellidoSecundario) {
            $apellido .= ' ' . $this->apellidoSecundario;
        }

        return $nombre . ' ' . $apellido;
    }

    /**
     * Obtener todos los materiales de los cursos del profesor
     */
    public function materiales()
    {
        // Relación indirecta: profesor -> cursos -> modulos -> materiales
        return \App\Domain\ContenidoDigital\Models\Material::whereHas('modulo.curso', function ($q) {
            $q->where('profesor_id', $this->getKey());
        });
    }
    /**
     * Obtener todas las evaluaciones de los cursos del profesor
     */
    public function evaluaciones()
    {
        // Relación indirecta: profesor -> cursos -> evaluaciones
        return \App\Domain\Evaluacion\Models\Evaluacion::whereHas('curso', function ($q) {
            $q->where('profesor_id', $this->getKey());
        });
    }
}
