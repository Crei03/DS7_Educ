<?php

namespace App\Domain\Evaluacion\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Opcion extends Model
{
    use HasFactory;

    protected $table = 'opciones';
    public $timestamps = false;

    protected $fillable = [
        'pregunta_id',
        'texto',
        'es_correcta',
    ];

    protected function casts(): array
    {
        return [
            'es_correcta' => 'boolean',
        ];
    }

    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(Pregunta::class, 'pregunta_id');
    }
}
