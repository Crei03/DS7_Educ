<?php
namespace App\Domain\Estudiante\Models;

use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    protected $table = 'matriculas';
    public $timestamps = false;
    protected $fillable = [
        'estudiante_id',
        'curso_id',
        'fecha',
        'progreso',
    ];
}
