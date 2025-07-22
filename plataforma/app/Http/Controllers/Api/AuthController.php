<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Domain\Estudiante\Models\Estudiante;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Primero intentar autenticación como User con Auth::attempt
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('user-token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
                'tipo' => 'user'
            ]);
        }

        // Luego intentar como Estudiante (no usa Auth::attempt porque no es Authenticatable por defecto)
        $estudiante = Estudiante::where('correo', $request->email)->first();
        if ($estudiante && Hash::check($request->password, $estudiante->contrasena)) {
            $token = $estudiante->createToken('estudiante-token')->plainTextToken;

            return response()->json([
                'user' => $estudiante,
                'token' => $token,
                'tipo' => 'estudiante'
            ]);
        }

        // Luego intentar como Profesor (no usa Auth::attempt porque no es Authenticatable por defecto)
        $profesor = \App\Domain\Curso\Models\Profesor::where('correo', $request->email)->first();
        if ($profesor && Hash::check($request->password, $profesor->contrasena)) {
            $token = $profesor->createToken('profesor-token')->plainTextToken;

            return response()->json([
                'user' => $profesor,
                'token' => $token,
                'tipo' => 'profesor'
            ]);
        }

        // Si no se encontró en ninguno
        throw ValidationException::withMessages([
            'email' => ['Las credenciales proporcionadas son incorrectas.'],
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('user-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'tipo' => 'user'
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    }
}
