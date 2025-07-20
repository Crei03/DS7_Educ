<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\Evaluacion\Models\Evaluacion;
use App\Domain\Evaluacion\Models\Resultado;
use App\Domain\Curso\Models\Curso;
use App\Domain\Evaluacion\Actions\CrearEvaluacionAction;
use App\Domain\Evaluacion\Actions\ProcesarEvaluacionAction;
use App\Domain\Evaluacion\Actions\ObtenerResultadosEvaluacionAction;
use App\Http\Requests\ResolverEvaluacionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
        // Debug temporal - verificar qué evaluación está llegando
        \Log::info('Evaluacion ID recibida:', ['id' => $evaluacion->id]);

        $evaluacion->load(['curso', 'preguntas.opciones']);

        // Debug temporal
        \Log::info('Evaluacion data después del load:', $evaluacion->toArray());

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

    /**
     * Obtener evaluaciones de un curso específico para estudiante
     */
    public function evaluacionesPorCurso(Curso $curso): JsonResponse
    {
        $estudiante = Auth::user();

        // Verificar que el estudiante esté matriculado en el curso
        $matricula = $estudiante->cursos()->where('cursos.id', $curso->id)->first();
        if (!$matricula) {
            return response()->json([
                'error' => 'No estás matriculado en este curso'
            ], 403);
        }

        $evaluaciones = $curso->evaluaciones()->with('preguntas')->get();

        // Agregar información de resultados del estudiante
        $evaluacionesConResultados = $evaluaciones->map(function ($evaluacion) use ($estudiante) {
            $resultado = Resultado::where('evaluacion_id', $evaluacion->id)
                ->where('estudiante_id', $estudiante->id)
                ->latest('fecha')
                ->first();

            $evaluacion->resultado_estudiante = $resultado ? [
                'puntaje' => $resultado->puntaje,
                'fecha' => $resultado->fecha,
                'completada' => true
            ] : ['completada' => false];

            $evaluacion->total_preguntas = $evaluacion->preguntas->count();

            return $evaluacion;
        });

        return response()->json($evaluacionesConResultados);
    }

    /**
     * Resolver evaluación por parte de un estudiante
     */
    public function resolverEvaluacionEstudiante(Request $request, Evaluacion $evaluacion): JsonResponse
    {
        $estudiante = Auth::user();

        // Verificar que el estudiante esté matriculado en el curso
        if (!$estudiante->cursosMatriculados()->where('curso_id', $evaluacion->curso_id)->exists()) {
            return response()->json([
                'error' => 'No estás matriculado en este curso'
            ], 403);
        }

        // Verificar que no haya resuelto ya la evaluación
        $resultadoExistente = Resultado::where('evaluacion_id', $evaluacion->id)
            ->where('estudiante_id', $estudiante->id)
            ->first();

        if ($resultadoExistente) {
            return response()->json([
                'error' => 'Ya has completado esta evaluación'
            ], 409);
        }

        $request->validate([
            'respuestas' => 'required|array',
            'respuestas.*' => 'required|integer|exists:opciones,id'
        ]);

        try {
            $resultado = $this->procesarEvaluacionAction->execute(
                $estudiante->id,
                $evaluacion->id,
                $request->respuestas
            );

            return response()->json([
                'mensaje' => 'Evaluación completada exitosamente',
                'resultado' => [
                    'puntaje' => $resultado->puntaje,
                    'total_preguntas' => $evaluacion->preguntas->count(),
                    'preguntas_correctas' => intval(($resultado->puntaje / 100) * $evaluacion->preguntas->count()),
                    'fecha' => $resultado->fecha
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al procesar la evaluación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener historial de evaluaciones del estudiante
     */
    public function historialEstudiante(): JsonResponse
    {
        $estudiante = Auth::user();

        $resultados = Resultado::where('estudiante_id', $estudiante->id)
            ->with(['evaluacion.curso'])
            ->orderBy('fecha', 'desc')
            ->get();

        $historial = $resultados->map(function ($resultado) {
            return [
                'evaluacion_id' => $resultado->evaluacion->id,
                'evaluacion_titulo' => $resultado->evaluacion->titulo,
                'curso_titulo' => $resultado->evaluacion->curso->titulo,
                'puntaje' => $resultado->puntaje,
                'fecha' => $resultado->fecha,
                'duracion_min' => $resultado->evaluacion->duracion_min
            ];
        });

        return response()->json($historial);
    }
}
