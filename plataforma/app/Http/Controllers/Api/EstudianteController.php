<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Estudiante\Models\Estudiante;
use App\Domain\Estudiante\Actions\CrearEstudianteAction;
use App\Domain\Estudiante\Actions\MatricularEstudianteAction;
use App\Http\Requests\CrearEstudianteRequest;
use App\Http\Requests\ActualizarEstudianteRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EstudianteController extends Controller
{
    public function __construct(
        private CrearEstudianteAction $crearEstudianteAction,
        private MatricularEstudianteAction $matricularEstudianteAction
    ) {
    }

    public function index(): JsonResponse
    {
        $estudiantes = Estudiante::with('cursos')->paginate(15);
        return response()->json($estudiantes);
    }

    public function store(CrearEstudianteRequest $request): JsonResponse
    {
        $estudiante = $this->crearEstudianteAction->execute($request->validated());

        return response()->json($estudiante, 201);
    }

    public function show(Estudiante $estudiante): JsonResponse
    {
        $estudiante->load('cursos');
        return response()->json($estudiante);
    }

    public function update(ActualizarEstudianteRequest $request, Estudiante $estudiante): JsonResponse
    {
        $estudiante->update($request->validated());

        return response()->json($estudiante);
    }

    public function destroy(Estudiante $estudiante): JsonResponse
    {
        $estudiante->delete();
        return response()->json(['message' => 'Estudiante eliminado correctamente']);
    }

    public function matricular(Request $request, Estudiante $estudiante): JsonResponse
    {
        $request->validate([
            'curso_id' => 'required|exists:cursos,id'
        ]);

        $this->matricularEstudianteAction->execute($estudiante->id, $request->curso_id);

        return response()->json(['message' => 'Estudiante matriculado correctamente']);
    }

    public function cursosMatriculados(Estudiante $estudiante): JsonResponse
    {
        $cursos = $estudiante->cursos()->withPivot(['fecha', 'progreso'])->get();
        return response()->json($cursos);
    }
}
