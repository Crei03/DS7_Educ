<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Evaluacion\Models\Evaluacion;
use App\Domain\Evaluacion\Actions\CrearEvaluacionAction;
use App\Domain\Evaluacion\Actions\ProcesarEvaluacionAction;
use App\Domain\Evaluacion\Actions\ObtenerResultadosEvaluacionAction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EvaluacionController extends Controller
{
    public function __construct(
        private CrearEvaluacionAction $crearEvaluacionAction,
        private ProcesarEvaluacionAction $procesarEvaluacionAction,
        private ObtenerResultadosEvaluacionAction $obtenerResultadosAction
    ) {
    }

    public function index(): JsonResponse
    {
        $evaluaciones = Evaluacion::with(['curso', 'preguntas'])->paginate(15);
        return response()->json($evaluaciones);
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'titulo' => 'required|string|max:120',
            'curso_id' => 'required|exists:cursos,id',
            'duracion_min' => 'nullable|integer|min:1|max:300',
            'preguntas' => 'nullable|array',
            'preguntas.*.enunciado' => 'required_with:preguntas|string',
            'preguntas.*.opciones' => 'required_with:preguntas|array|min:2',
            'preguntas.*.opciones.*.texto' => 'required|string',
            'preguntas.*.opciones.*.es_correcta' => 'required|boolean',
        ]);

        $evaluacion = $this->crearEvaluacionAction->execute($datos);

        return response()->json($evaluacion, 201);
    }

    public function show(Evaluacion $evaluacion): JsonResponse
    {
        $evaluacion->load(['curso', 'preguntas.opciones']);
        return response()->json($evaluacion);
    }

    public function update(Request $request, Evaluacion $evaluacion): JsonResponse
    {
        $datos = $request->validate([
            'titulo' => 'sometimes|required|string|max:120',
            'duracion_min' => 'sometimes|nullable|integer|min:1|max:300',
        ]);

        $evaluacion->update($datos);

        return response()->json($evaluacion);
    }

    public function destroy(Evaluacion $evaluacion): JsonResponse
    {
        // Verificar si tiene resultados antes de eliminar
        if ($evaluacion->resultados()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar una evaluación que ya tiene resultados'
            ], 422);
        }

        $evaluacion->delete();
        return response()->json(['message' => 'Evaluación eliminada correctamente']);
    }

    public function resolverEvaluacion(Request $request, Evaluacion $evaluacion): JsonResponse
    {
        $request->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
            'respuestas' => 'required|array',
        ]);

        try {
            $resultado = $this->procesarEvaluacionAction->execute(
                $request->estudiante_id,
                $evaluacion->id,
                $request->respuestas
            );

            return response()->json($resultado, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function resultados(Evaluacion $evaluacion): JsonResponse
    {
        $resultados = $this->obtenerResultadosAction->execute($evaluacion->id);
        return response()->json($resultados);
    }
}
