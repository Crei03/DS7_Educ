<?php

namespace App\Domain\Evaluacion\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pregunta extends Model
{
    use HasFactory;

    protected $table = 'preguntas';
    public $timestamps = false;

    protected $fillable = [
        'evaluacion_id',
        'enunciado',
    ];

    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_id');
    }

    public function opciones(): HasMany
    {
        return $this->hasMany(Opcion::class, 'pregunta_id');
    }

    public function opcionCorrecta(): ?Opcion
    {
        return $this->opciones()->where('es_correcta', true)->first();
    }
}
