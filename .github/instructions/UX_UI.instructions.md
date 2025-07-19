# Guía UI/UX – Plataforma Académica

## Principios generales

1. Minimalismo enfocado: máxima legibilidad, nada de adornos superfluos.
2. Jerarquía visual: tipografía + color → guían la atención (H1–H4, botones primarios/ secundarios).
3. Espacios en blanco generosos: padding ≥ 1.25 rem en contenedores.
4. Desempeño primero: imágenes comprimidas, carga diferida (lazy‑load) de vídeos/materiales.
5. Accesibilidad AA:
   - Contraste ≥ 4.5:1.
   - Navegación por teclado (TAB‑index coherente).

## Flujos clave

- **On‑boarding alumno**: dashboard inicial con progreso 0 % y cursos matriculados.
- **On‑boarding profesor**: wizard de “Crear tu primer curso” (pasos rápidos, guardado auto).
- **Evaluación**: temporizador visible, feedback instantáneo con color y animación sutil.

## Micro‑interacciones

- Hover en tarjetas de curso: elevación `translateY(-2px)` + sombra suave.
- Loader de contenido: icono spinner SVG animado (<x-lucide-loader class="animate-spin" />).

## Iconografía

Se utilizará el paquete **Blade Icons** con los sets **Heroicons** y **Lucide** para SVG (instalar vía Composer):

composer require blade-ui-kit/blade-heroicons
composer require blade-ui-kit/blade-lucide

Uso:
<x-heroicon-o-book-open class="w-5 h-5 text-primary" />
<x-lucide-graduation-cap class="w-5 h-5 text-secondary" />

Ventajas:

- Componentes Blade reutilizables.
- Personalización por CSS
- Gran comunidad y mantenimiento activo. :contentReference[oaicite:0]{index=0}

## Colores /_ Variables SCSS (derivadas de la paleta XML) _/

```scss
$blue-900: #0540f2;
$blue-800: #0554f2;
$blue-700: #056cf2;
$blue-400: #63a1f2;
$gris-100: #f2f2f2;

$primary: $blue-800;
$primary-dk: $blue-900;
$accent: $blue-400;
$bg: $gris-100;
$text-main: #1f2937;
```

## Header / Footer

/_ Header _/
.header {
@apply bg-white shadow-sm sticky top-0 z-40;
.nav-link { @apply text-sm font-medium py-3 px-4 hover:text-primary; }
}

/_ Footer _/
.footer {
@apply bg-primary text-white py-6;
a { @apply text-accent hover:underline; }
}

## Landing

.hero {
@apply flex flex-col md:flex-row items-center gap-8 py-20;
.headline { @apply text-4xl md:text-5xl font-extrabold; }
.cta { @apply mt-6; }
}

.feature {
@apply bg-white rounded-lg p-6 shadow hover:shadow-md transition;
}
