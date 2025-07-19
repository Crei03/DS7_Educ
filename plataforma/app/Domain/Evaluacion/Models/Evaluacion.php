<?php

namespace App\Domain\Evaluacion\Models;

use App\Domain\Curso\Models\Curso;
use App\Domain\Estudiante\Models\Estudiante;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluacion extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones';
    public $timestamps = false;

    protected $fillable = [
        'curso_id',
        'titulo',
        'duracion_min',
    ];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(Pregunta::class, 'evaluacion_id');
    }

    public function resultados(): HasMany
    {
        return $this->hasMany(Resultado::class, 'evaluacion_id');
    }
}
