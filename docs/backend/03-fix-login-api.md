# Solución al problema de login en API

## Descripción del problema

Al enviar un POST a `/api/auth/login`, la API devolvía la página HTML por defecto de Laravel en vez de una respuesta JSON. Esto impedía la autenticación desde Postman y otros clientes.

## Pasos realizados para solucionar

1. **Verificación de rutas:**

   - Se confirmó que la ruta `Route::post('login', [AuthController::class, 'login']);` está correctamente definida en `routes/api.php`.

2. **Revisión de configuración de rutas en Laravel 11:**

   - Se detectó que en `bootstrap/app.php` faltaba la línea para cargar las rutas API:
     ```php
     api: __DIR__.'/../routes/api.php',
     ```
   - Se agregó esta línea en la función `withRouting`.

3. **Controlador y namespace:**

   - Se verificó que el controlador `AuthController` está en el namespace correcto: `App\Http\Controllers\Api`.

4. **Respuesta JSON en el controlador:**

   - El método `login()` fue revisado para asegurar que siempre retorna `response()->json(...)` y nunca `view()` ni `redirect()`.

5. **Validación y autenticación:**

   - Se ajustó el método para intentar primero con el modelo `User` y luego con el modelo `Estudiante`, devolviendo un token Sanctum en ambos casos.
   - Se corrigió el uso de `Hash::check` para evitar problemas con el cast `hashed` en Laravel 11.

6. **Prueba final:**
   - Se realizó una petición POST con curl y se confirmó que la respuesta es JSON y contiene el token.

## Ejemplo de login exitoso

```json
{
  "user": { ... },
  "token": "TOKEN_SANCTUM",
  "tipo": "user"
}
```

## Consideraciones

- El login ahora funciona correctamente para usuarios y estudiantes.
- El token recibido puede usarse en Postman para acceder a endpoints protegidos.
- Se recomienda documentar este fix para futuras migraciones a Laravel 11.

---

**Autor:** GitHub Copilot
**Fecha:** 2025-07-18
