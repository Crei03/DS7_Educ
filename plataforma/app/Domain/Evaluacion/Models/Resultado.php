<?php

namespace App\Domain\Evaluacion\Models;

use App\Domain\Estudiante\Models\Estudiante;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resultado extends Model
{
    use HasFactory;

    protected $table = 'resultados';
    public $timestamps = false;

    protected $fillable = [
        'evaluacion_id',
        'estudiante_id',
        'puntaje',
        'fecha',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'puntaje' => 'decimal:2',
        ];
    }

    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_id');
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }
}
