<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\ContenidoDigital\Models\Modulo;
use App\Domain\ContenidoDigital\Actions\CrearModuloAction;
use App\Domain\ContenidoDigital\Actions\ReordenarModulosAction;
use App\Domain\ContenidoDigital\Actions\ObtenerContenidoModuloAction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ModuloController extends Controller
{
    public function __construct(
        private CrearModuloAction $crearModuloAction,
        private ReordenarModulosAction $reordenarModulosAction,
        private ObtenerContenidoModuloAction $obtenerContenidoAction
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $query = Modulo::with(['curso', 'materiales']);

        // Filtrar por curso si se proporciona
        if ($request->has('curso_id')) {
            $query->where('curso_id', $request->curso_id);
        }

        $modulos = $query->orderBy('curso_id')
            ->orderBy('orden')
            ->paginate(15);

        return response()->json($modulos);
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'titulo' => 'required|string|max:120',
            'orden' => 'nullable|integer|min:1',
        ]);

        $modulo = $this->crearModuloAction->execute($datos);
        $modulo->load(['curso', 'materiales']);

        return response()->json($modulo, 201);
    }

    public function show(Modulo $modulo): JsonResponse
    {
        $contenido = $this->obtenerContenidoAction->execute($modulo->id);
        return response()->json($contenido);
    }

    public function update(Request $request, Modulo $modulo): JsonResponse
    {
        $datos = $request->validate([
            'titulo' => 'sometimes|required|string|max:120',
            'orden' => 'sometimes|required|integer|min:1',
        ]);

        $modulo->update($datos);
        $modulo->load(['curso', 'materiales']);

        return response()->json($modulo);
    }

    public function destroy(Modulo $modulo): JsonResponse
    {
        // Verificar si tiene materiales
        if ($modulo->materiales()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar un módulo que contiene materiales'
            ], 422);
        }

        $modulo->delete();
        return response()->json(['message' => 'Módulo eliminado correctamente']);
    }

    public function reordenar(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'modulos' => 'required|array|min:1',
            'modulos.*' => 'required|integer|exists:modulos,id',
        ]);

        $this->reordenarModulosAction->execute($datos['curso_id'], $datos['modulos']);

        return response()->json(['message' => 'Módulos reordenados correctamente']);
    }
}
