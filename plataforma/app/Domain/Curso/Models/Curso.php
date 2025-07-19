<?php

namespace App\Domain\Curso\Models;

use App\Domain\Estudiante\Models\Estudiante;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';
    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'descripcion',
        'profesor_id',
    ];

    protected function casts(): array
    {
        return [
            'creado_en' => 'datetime',
        ];
    }

    public function profesor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }

    public function estudiantes(): BelongsToMany
    {
        return $this->belongsToMany(
            Estudiante::class,
            'matriculas',
            'curso_id',
            'estudiante_id'
        )->withPivot(['fecha', 'progreso']);
    }

    public function modulos(): HasMany
    {
        return $this->hasMany(\App\Domain\ContenidoDigital\Models\Modulo::class, 'curso_id');
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(\App\Domain\Evaluacion\Models\Evaluacion::class, 'curso_id');
    }
}
