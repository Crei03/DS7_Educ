<?php

namespace App\Domain\ContenidoDigital\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory;

    protected $table = 'materiales';
    public $timestamps = false;

    protected $fillable = [
        'modulo_id',
        'tipo',
        'url',
        'titulo',
    ];

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class, 'modulo_id');
    }
}
