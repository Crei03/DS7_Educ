<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Curso\Models\Curso;
use App\Domain\Curso\Actions\CrearCursoAction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CursoController extends Controller
{
    public function __construct(
        private CrearCursoAction $crearCursoAction
    ) {
    }

    public function index(): JsonResponse
    {
        $cursos = Curso::with(['profesor', 'estudiantes'])->paginate(15);
        return response()->json($cursos);
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'profesor_id' => 'required|exists:profesores,id',
        ]);

        $curso = $this->crearCursoAction->execute($datos);

        return response()->json($curso, 201);
    }

    public function show(Curso $curso): JsonResponse
    {
        $curso->load(['profesor', 'estudiantes', 'modulos']);
        return response()->json($curso);
    }

    public function update(Request $request, Curso $curso): JsonResponse
    {
        $datos = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'descripcion' => 'sometimes|required|string',
            'profesor_id' => 'sometimes|required|exists:profesores,id',
        ]);

        $curso->update($datos);

        return response()->json($curso);
    }

    public function destroy(Curso $curso): JsonResponse
    {
        $curso->delete();
        return response()->json(['message' => 'Curso eliminado correctamente']);
    }

    public function estudiantes(Curso $curso): JsonResponse
    {
        $estudiantes = $curso->estudiantes()->withPivot(['fecha', 'progreso'])->get();
        return response()->json($estudiantes);
    }

    public function modulos(Curso $curso): JsonResponse
    {
        $modulos = $curso->modulos()->with('materiales')->get();
        return response()->json($modulos);
    }
}
