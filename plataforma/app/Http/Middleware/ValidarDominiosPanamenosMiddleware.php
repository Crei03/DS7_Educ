<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidarDominiosPanamenosMiddleware
{
    /**
     * Middleware para validar que los correos tengan dominio panameño
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo aplicar validación a rutas específicas
        $rutasValidar = [
            'api/estudiantes',
            'api/auth/register'
        ];

        $rutaActual = $request->path();
        $debeValidar = false;

        foreach ($rutasValidar as $ruta) {
            if (str_starts_with($rutaActual, $ruta)) {
                $debeValidar = true;
                break;
            }
        }

        if ($debeValidar && $request->has('correo')) {
            $correo = $request->input('correo');

            if (!str_ends_with($correo, '.pa')) {
                return response()->json([
                    'message' => 'Solo se permiten correos con dominio panameño (.pa)',
                    'errors' => [
                        'correo' => ['El correo debe tener dominio panameño (.pa)']
                    ]
                ], 422);
            }
        }

        return $next($request);
    }
}
