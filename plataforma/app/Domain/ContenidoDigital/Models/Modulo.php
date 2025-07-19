<?php

namespace App\Domain\ContenidoDigital\Models;

use App\Domain\Curso\Models\Curso;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Modulo extends Model
{
    use HasFactory;

    protected $table = 'modulos';
    public $timestamps = false;

    protected $fillable = [
        'curso_id',
        'titulo',
        'orden',
    ];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function materiales(): HasMany
    {
        return $this->hasMany(Material::class, 'modulo_id');
    }
}
