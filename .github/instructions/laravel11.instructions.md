# Guía de Buenas Prácticas – Proyecto “Plataforma Académica” (Laravel 11)

## 1. Propósito del proyecto

Desarrollar una plataforma web para academias pequeñas que gestione cursos, estudiantes, contenidos digitales, evaluaciones y una API REST con autenticación. El foco es **seguridad, mantenibilidad y escalabilidad**.

---

## 2. Arquitectura recomendada

- **MVC**
  - `app/Models` → Modelos Eloquent.
  - `app/Http/Controllers` → controladores para orquestar la lógica.
  - `app/Http/Requests` → validación y autorización.
  - `resources/views` (Blade) para la UI interna.
  - `routes/web.php` y `routes/api.php` para definir rutas.

---

## 3. Kits y herramientas oficiales de Laravel

| Kit / Paquete            | Uso en el proyecto             | Por qué es útil                          |
| ------------------------ | ------------------------------ | ---------------------------------------- |
| **Jetstream (Livewire)** | Registro, login, 2FA, perfil.  | Agiliza la autenticación sin reinventar. |
| **Sanctum**              | API tokens, autenticación SPA. | Ligero y fácil de integrar.              |

---

## 4. Seguridad de acceso y solicitud de información

1. **Protección de datos**
   - No exponer campos sensibles vía API (`hidden` en Modelos).
   - Enmascarar cédulas y correos en logs.
2. **Autenticación y autorización**
   - Jetstream (sesiones) + Sanctum (API).
   - `Gate::allows('admin')` para endpoints críticos.
3. **Validación estricta**
   - Todas las entradas pasan por **Form Request**; regla `regex` para cédula panameña con formato: `\d{1,2}-\d{4,5}-\d{5,6}`.
4. **Prevención de ataques**
   - CSRF token (`@csrf`) en cada formulario.
   - Prevención SQL Injection ↔ Eloquent/Query Builder.
5. **Rate Limiting**
   - `/api/*` → 30 req/min; profesores autenticados hasta 60 req/min.
6. **Gestión de claves**
   - `.env` jamás en Git.
   - `APP_KEY` y tokens sólo vía 1Password/Vault.

---

## 5. Convenciones de código

- `declare(strict_types=1);` al principio de cada archivo PHP.
- Controladores ≤ 120 líneas; lógica de negocio en **Actions** (`app/Domain/*/Actions`).
- Rutas nombradas (`->name('cursos.matricular')`).
- DTOs o **Eloquent API Resources** para todas las respuestas JSON.
