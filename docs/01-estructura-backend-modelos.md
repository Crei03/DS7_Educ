# Documentación - Estructura del Backend y Modelos

## Descripción

Se ha implementado la estructura completa del backend siguiendo fielmente el ERD (Modelo Entidad-Relación) especificado en las instrucciones del proyecto. La arquitectura utiliza Domain-Driven Design (DDD) organizando los modelos por dominios específicos.

## Estructura de Carpetas Creada

```
app/Domain/
├── Curso/
│   ├── Models/
│   │   ├── Profesor.php
│   │   └── Curso.php
│   └── Actions/
├── Estudiante/
│   ├── Models/
│   │   └── Estudiante.php
│   └── Actions/
├── ContenidoDigital/
│   ├── Models/
│   │   ├── Modulo.php
│   │   └── Material.php
│   └── Actions/
└── Evaluacion/
    ├── Models/
    │   ├── Evaluacion.php
    │   ├── Pregunta.php
    │   ├── Opcion.php
    │   └── Resultado.php
    └── Actions/
```

## Migraciones Implementadas

Se crearon 10 migraciones siguiendo exactamente el script SQL especificado:

1. **profesores** - Tabla de profesores con campos de nombre completo, cédula y credenciales
2. **estudiantes** - Tabla de estudiantes con estructura idéntica a profesores
3. **cursos** - Tabla de cursos con relación a profesores
4. **modulos** - Tabla de módulos pertenecientes a cursos
5. **materiales** - Tabla de materiales digitales (PDF, links, ZIP) por módulo
6. **matriculas** - Tabla pivote estudiante-curso con progreso
7. **evaluaciones** - Tabla de evaluaciones por curso
8. **preguntas** - Tabla de preguntas por evaluación
9. **opciones** - Tabla de opciones de respuesta por pregunta
10. **resultados** - Tabla de resultados de evaluaciones por estudiante

## Modelos Eloquent Creados

### Dominio Curso

- **Profesor**: Modelo para gestión de profesores con método `fullName()` y relación `cursos()`
- **Curso**: Modelo central con relaciones a `profesor()`, `estudiantes()`, `modulos()` y `evaluaciones()`

### Dominio Estudiante

- **Estudiante**: Modelo para gestión de estudiantes con relación many-to-many a `cursos()` vía tabla `matriculas`

### Dominio ContenidoDigital

- **Modulo**: Modelo para organización de contenidos por curso
- **Material**: Modelo para archivos digitales (PDF, links, ZIP) con enum de tipos

### Dominio Evaluacion

- **Evaluacion**: Modelo para exámenes con relaciones a `curso()`, `preguntas()` y `resultados()`
- **Pregunta**: Modelo para preguntas de evaluación con `opciones()` y método `opcionCorrecta()`
- **Opcion**: Modelo para opciones de respuesta con campo booleano `es_correcta`
- **Resultado**: Modelo para almacenar resultados de estudiantes en evaluaciones

## Relaciones Implementadas

Siguiendo el ERD especificado:

- **usuarios —< matriculas >— cursos —< modulos —< materiales**
- **cursos —< evaluaciones —< preguntas —< opciones**
- **evaluaciones —< resultados >— usuarios**

## Características Técnicas

- **Tipado estricto**: Todos los métodos incluyen tipos de retorno (`: void`, `: string`, etc.)
- **Convenciones PSR-12**: Código formateado según estándares
- **Campos calculados**: Métodos como `fullName()` para concatenar nombres completos
- **Casts apropiados**: Fechas como `datetime`, contraseñas como `hashed`, decimales para puntajes
- **Relaciones Eloquent**: Configuradas con claves foráneas correctas y tablas pivote

## Base de Datos

- **Motor**: MySQL (XAMPP puerto 3306)
- **Base de datos**: `plataforma_academica`
- **Codificación**: UTF-8 con soporte para caracteres especiales
- **Integridad referencial**: Todas las claves foráneas configuradas

## Consideraciones Especiales

### Seguridad

- Contraseñas hasheadas automáticamente con cast `'hashed'`
- Campos sensibles ocultos en serialización (`$hidden`)
- Validación de unicidad en cédulas y correos

### Rendimiento

- Índices únicos en campos de búsqueda frecuente (cédula, correo)
- Relaciones optimizadas para consultas eficientes
- Timestamps personalizados según requerimientos

### Localización

- Faker configurado con locale `es_PA` para datos panameños
- Estructura preparada para cédulas formato: `\d{1,2}-\d{4,5}-\d{5,6}`
