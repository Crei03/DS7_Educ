<?php

namespace App\Domain\Estudiante\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

class Estudiante extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'estudiantes';
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

    public function cursos(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Domain\Curso\Models\Curso::class,
            'matriculas',
            'estudiante_id',
            'curso_id'
        )->withPivot(['fecha', 'progreso']);
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
}
