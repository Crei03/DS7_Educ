# Guía para GitHub Copilot – Proyecto “Plataforma Académica”

## 0. Instruccion para GitHub Copilot

Esta guía está diseñada para ayudar a GitHub Copilot a generar código de alta calidad y alineado con las mejores prácticas del proyecto "Plataforma Académica" en Laravel 11. Asegúrate de seguir las convenciones y patrones establecidos en esta guía para obtener los mejores resultados. Antes de generar codigo, analiza las tareas a desarrollar, comprende el contexto, estructura paso a paso lo que vas a hacer y preegunta al usuario si es necesario. Si tu sugerencias es correcta procede a generar el código, si no, pide más información o contexto.

## 1. Finalidad del proyecto

Crear una aplicación Laravel 11 que permita a academias gestionar cursos, estudiantes, contenidos digitales y evaluaciones; además, proveer una API REST con tokens de Sanctum. El público objetivo son academias panameñas con grupos de ≤ 1 000 estudiantes.

## 2. Acceso a información básica

Copilot puede inferir estructura a partir de:

- Modelos dentro de `app/Domain/*/Models`.
- Acciones dentro de `app/Domain/*/Actions`.
- Rutas en `routes/`.
- Migraciones y seeders.
- Archivos de configuración en `config/`.

## 3. Indicaciones clave para las sugerencias

1. **Usar Eloquent antes que SQL crudo**; si se requiere query compleja, envolverla en Scope.
2. Generar código PSR‑12 con tipado estricto (`: void`, `: string`).
3. Mantener los controladores delgados: si la sugerencia incluye lógica de negocio larga, muévela a un `Action`.
4. Sugerir importaciones absolutas (`use App\Domain\Curso\Models\Curso;`).
5. Para seeders, usar Faker es_PA y datos coherentes cédulas con formato: `\d{1,2}-\d{4,5}-\d{5,6}`, dominios `.pa`.
6. Seguir las guías `.github/instructions` para más instrucciones específicas.

## 4. Generar documentacion de lo que se hizo al finalizar una tarea o funcionalidad

Al finalizar una tarea o funcionalidad, genera documentación breve en el archivo correspondiente dentro de `docs/`. Usa el formato Markdown y sigue las convenciones del proyecto. Incluye:

- Descripción de la funcionalidad.
- Pasos de implementación.
- Consideraciones especiales (seguridad, rendimiento).
