# Endpoints API Implementados

## Resumen

Se implementaron los 3 endpoints solicitados para completar la funcionalidad del frontend de estudiantes:

## 1. GET /api/cursos/disponibles

**Descripción:** Obtiene la lista de cursos disponibles para matrícula  
**Autenticación:** Requerida (Bearer token)  
**Método:** GET

### Response ejemplo:

```json
{
  "success": true,
  "cursos": [
    {
      "id": 1,
      "titulo": "PHP Básico para Principiantes",
      "descripcion": "Curso completo de PHP desde los fundamentos hasta conceptos avanzados.",
      "profesor": {
        "id": 1,
        "nombre_completo": "Juan Carlos González Pérez"
      },
      "estudiantes_count": 0,
      "modulos_count": 2,
      "creado_en": "2025-07-20T14:00:00.000000Z"
    }
  ]
}
```

## 2. POST /api/cursos/{id}/matricular

**Descripción:** Matricula al estudiante autenticado en un curso específico  
**Autenticación:** Requerida (Bearer token)  
**Método:** POST

### Response ejemplo (éxito):

```json
{
  "success": true,
  "message": "Matrícula realizada exitosamente",
  "curso": {
    "id": 1,
    "titulo": "PHP Básico para Principiantes",
    "descripcion": "Curso completo de PHP desde los fundamentos hasta conceptos avanzados.",
    "profesor": {
      "nombre_completo": "Juan Carlos González Pérez"
    },
    "progreso": 0,
    "fecha_matricula": "2025-07-20T14:30:00.000000Z",
    "modulos_count": 2
  }
}
```

### Response ejemplo (ya matriculado):

```json
{
  "success": false,
  "message": "Ya estás matriculado en este curso"
}
```

## 3. POST /api/estudiante/materiales/{id}/marcar-visto

**Descripción:** Marca un material como visto por el estudiante autenticado  
**Autenticación:** Requerida (Bearer token)  
**Método:** POST

### Response ejemplo:

```json
{
  "success": true,
  "message": "Material marcado como visto",
  "visto_en": "2025-07-20T14:45:00.000000Z"
}
```

## 4. GET /api/estudiante/cursos/{id}/progreso

**Descripción:** Obtiene el progreso detallado de un curso para el estudiante autenticado  
**Autenticación:** Requerida (Bearer token)  
**Método:** GET

### Response ejemplo:

```json
{
  "id": 1,
  "titulo": "PHP Básico para Principiantes",
  "descripcion": "Curso completo de PHP desde los fundamentos hasta conceptos avanzados.",
  "progreso": 0,
  "profesor": {
    "nombre_completo": "Juan Carlos González Pérez"
  },
  "modulos": [
    {
      "id": 1,
      "titulo": "Introducción a PHP",
      "orden": 1,
      "progreso": 50,
      "materiales": [
        {
          "id": 1,
          "titulo": "¿Qué es PHP?",
          "tipo": "pdf",
          "url": "https://example.com/php-introduccion.pdf",
          "visto": true
        },
        {
          "id": 2,
          "titulo": "Instalación de PHP",
          "tipo": "link",
          "url": "https://php.net/downloads.php",
          "visto": false
        }
      ]
    }
  ],
  "ultima_actividad": "2025-07-20T14:30:00.000000Z"
}
```

## Implementación Técnica

### Base de Datos

- **Tabla `material_visto`**: Rastrea qué materiales ha visto cada estudiante
- **Tabla `matriculas`**: Relación muchos a muchos entre estudiantes y cursos con progreso

### Controladores

- **CursoController**: Métodos `disponibles()` y `matricular()`
- **MaterialController**: Método `marcarVisto()`
- **EstudianteController**: Método `progresoDetallado()`

### Autenticación

Todos los endpoints utilizan el middleware `auth:sanctum` y obtienen el estudiante a través del email del usuario autenticado.

### Validaciones

- Verificación de que el estudiante existe
- Validación de que el curso/material existe
- Prevención de matrículas duplicadas
- Verificación de permisos (solo estudiantes matriculados pueden ver progreso)

## Consideraciones

1. **Seguridad**: Solo estudiantes autenticados pueden acceder a sus datos
2. **Integridad**: Prevención de duplicados y validación de relaciones
3. **Performance**: Uso de eager loading para optimizar consultas
4. **Escalabilidad**: Estructura preparada para funcionalidades adicionales

Los endpoints están listos para ser consumidos por el frontend y están completamente integrados con la arquitectura existente del sistema.
