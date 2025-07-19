# Guía de Roles y Permisos

El proyecto distingue tres roles base:

| Rol            | Descripción breve                       |
| -------------- | --------------------------------------- |
| **profesor**   | Crea y administra sus propios cursos.   |
| **estudiante** | Consume cursos y presenta evaluaciones. |

> **Paquete recomendado**: [spatie/laravel-permission](https://github.com/spatie/laravel-permission)

## 1. Instalación rápida

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate

use Spatie\Permission\Models\Role;

Role::firstOrCreate(['name' => 'profesor']);
Role::firstOrCreate(['name' => 'estudiante']);

```

## 2. Tabla de permisos

| Permiso                        | admin | profesor | estudiante |
| ------------------------------ | :---: | :------: | :--------: |
| crear cursos                   |  ✅   |    ✅    |     ❌     |
| editar / borrar cursos propios |  ✅   |    ✅    |     ❌     |
| matricularse en curso          |  ❌   |    ❌    |     ✅     |
| ver progreso de estudiantes    |  ✅   |    ✅    |     ❌     |
| exportar progreso JSON         |  ✅   |    ❌    |     ❌     |
| administrar usuarios           |  ✅   |    ❌    |     ❌     |

## 3. Endpoints públicos principales

| Método | Ruta                               | Descripción                      |
| ------ | ---------------------------------- | -------------------------------- |
| GET    | /api/v1/cursos                     | Listar cursos públicos           |
| GET    | /api/v1/cursos/                    | Detalle de curso                 |
| POST   | /api/v1/matriculas                 | Matricular (requiere token)      |
| GET    | /api/v1/usuario/progreso           | Progreso del usuario autenticado |
| GET    | /api/v1/evaluaciones/{id}/intentos | Intentos y nota                  |

## 4. Rate limiting

Rate Limiting
Público sin token: 60 solicitudes/minuto.

Profesor y admin autenticados: 120 solicitudes/minuto.

Configuración en app/Providers/RouteServiceProvider.php
