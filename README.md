# Plataforma Académica Laravel 11

![Laravel](https://img.shields.io/badge/Laravel-11-red?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange?style=flat-square&logo=mysql)

## 📋 Descripción del Proyecto

**Plataforma Académica** es una aplicación web desarrollada en Laravel 11 diseñada específicamente para academias panameñas que necesitan gestionar cursos, estudiantes, contenidos digitales y evaluaciones. La plataforma soporta grupos de hasta 1,000 estudiantes y provee una API REST completa con autenticación mediante Sanctum tokens.

### 🎯 Características Principales

- ✅ Gestión completa de cursos y módulos
- ✅ Administración de estudiantes y profesores
- ✅ Contenidos digitales (PDFs, enlaces, archivos ZIP)
- ✅ Sistema de evaluaciones con preguntas de opción múltiple
- ✅ API REST con autenticación Sanctum
- ✅ Interfaz web responsiva con Vue.js
- ✅ Datos de prueba con locale panameño (Faker es_PA)

## 📁 Estructura del Proyecto

### `/app` - Lógica de la Aplicación

La aplicación sigue los principios de **MVC (Model-View-Controller)** organizando el código por dominios funcionales:

#### `/app/Domain` - Modelos por Dominio (Models del patrón MVC)

```
app/Domain/
├── Curso/
│   ├── Models/
│   │   ├── Profesor.php      # Modelo de profesores
│   │   └── Curso.php         # Modelo de cursos
│   └── Actions/              # Lógica de negocio de cursos
├── Estudiante/
│   ├── Models/
│   │   └── Estudiante.php    # Modelo de estudiantes
│   └── Actions/              # Lógica de negocio de estudiantes
├── ContenidoDigital/
│   ├── Models/
│   │   ├── Modulo.php        # Modelo de módulos de curso
│   │   └── Material.php      # Modelo de materiales digitales
│   └── Actions/              # Lógica de contenidos digitales
└── Evaluacion/
    ├── Models/
    │   ├── Evaluacion.php    # Modelo de evaluaciones
    │   ├── Pregunta.php      # Modelo de preguntas
    │   ├── Opcion.php        # Modelo de opciones de respuesta
    │   └── Resultado.php     # Modelo de resultados
    └── Actions/              # Lógica de negocio de evaluaciones
```

**Propósito**: Los **Models** del patrón MVC organizados por dominios funcionales. Cada dominio agrupa los modelos relacionados con su área específica, manteniendo el código organizado y siguiendo las convenciones de Laravel.

#### `/app/Http` - Controladores (Controllers del patrón MVC)

```
app/Http/
├── Controllers/
│   └── Api/                  # Controladores de API REST
│       ├── AuthController.php
│       ├── CursoController.php
│       ├── EstudianteController.php
│       ├── MaterialController.php
│       └── EvaluacionController.php
├── Middleware/               # Middlewares personalizados
└── Requests/                 # Form Request para validación
```

**Propósito**: Los **Controllers** del patrón MVC que manejan las peticiones HTTP, validaciones y respuestas. Los controladores son delgados y delegan la lógica compleja a los Actions de cada dominio.

#### `/app/Models` - Modelos Base

```
app/Models/
└── User.php                  # Modelo base de usuarios (Laravel)
```

**Propósito**: Contiene el modelo User base de Laravel, usado para autenticación con Sanctum.

### `/config` - Configuración

```
config/
├── app.php                   # Configuración principal de la app
├── auth.php                  # Configuración de autenticación
├── database.php              # Configuración de base de datos
├── sanctum.php               # Configuración de Sanctum tokens
└── ...                       # Otras configuraciones de Laravel
```

**Propósito**: Archivos de configuración del framework y paquetes instalados.

### `/database` - Base de Datos

```
database/
├── migrations/               # Migraciones de base de datos
│   ├── *_create_profesores_table.php
│   ├── *_create_estudiantes_table.php
│   ├── *_create_cursos_table.php
│   ├── *_create_modulos_table.php
│   ├── *_create_materiales_table.php
│   ├── *_create_matriculas_table.php
│   ├── *_create_evaluaciones_table.php
│   ├── *_create_preguntas_table.php
│   ├── *_create_opciones_table.php
│   ├── *_create_resultados_table.php
│   └── *_create_material_visto_table.php
├── seeders/                  # Pobladores de datos
│   ├── DatabaseSeeder.php
│   └── TestDataSeeder.php    # Datos de prueba panameños
├── factories/                # Factories para testing
└── database.sqlite           # Base de datos SQLite (desarrollo)
```

**Propósito**: Definición y estructura de la base de datos. Las migraciones siguen exactamente el ERD especificado en el proyecto.

### `/resources` - Vistas (Views del patrón MVC)

```
resources/
├── css/                      # Estilos CSS
├── js/                       # JavaScript y Vue.js
│   ├── components/           # Componentes Vue
│   ├── stores/               # Stores de Pinia
│   └── views/                # Vistas de aplicación
```

**Propósito**: Las **Views** del patrón MVC. Frontend de la aplicación con Vue.js 3, Tailwind CSS y sistema de componentes reactivos.

### `/routes` - Definición de Rutas

```
routes/
├── api.php                   # Rutas de API REST
├── web.php                   # Rutas web del frontend
└── console.php               # Comandos de consola
```

**Propósito**: Define todas las rutas disponibles en la aplicación.

### `/public` - Archivos Públicos

```
public/
├── index.php                 # Punto de entrada de la aplicación
├── favicon.ico              # Icono del sitio
├── robots.txt               # Directivas para crawlers
└── build/                   # Archivos compilados (CSS/JS)
```

**Propósito**: Archivos estáticos accesibles públicamente y punto de entrada de Laravel.

### `/storage` - Almacenamiento

```
storage/
├── app/                     # Archivos de aplicación
├── framework/               # Archivos del framework (cache, sessions)
└── logs/                    # Logs de la aplicación
```

## 🚀 Instalación y Configuración

### Requisitos Previos

- PHP 8.2 o superior
- Composer
- MySQL 5.7+ o MariaDB
- Node.js y npm
- XAMPP (para desarrollo local)

### Pasos de Instalación

1. **Clonar el repositorio**

   ```bash
   git clone [repository-url]
   cd plataforma
   ```

2. **Instalar dependencias PHP**

   ```bash
   composer install
   ```

3. **Instalar dependencias Node.js**

   ```bash
   npm install
   ```

4. **Configurar archivo de entorno**

   ```bash
   cp .env.example .env
   ```

5. **Configurar base de datos en `.env`**

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=plataforma_academica
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Generar clave de aplicación**

   ```bash
   php artisan key:generate
   ```

7. **Ejecutar migraciones y seeders**

   ```bash
   php artisan migrate:fresh --seed
   ```

8. **Compilar assets del frontend**

   ```bash
   npm run build
   ```

9. **Iniciar servidor de desarrollo**
   ```bash
   php artisan serve
   ```

## 📡 API Endpoints

### Autenticación

- `POST /api/login` - Iniciar sesión
- `POST /api/logout` - Cerrar sesión
- `POST /api/register` - Registro de usuarios

### Estudiantes

- `GET /api/estudiante/cursos` - Cursos del estudiante
- `GET /api/estudiante/cursos/{id}/progreso` - Progreso detallado
- `GET /api/cursos/disponibles` - Cursos disponibles para matrícula
- `POST /api/cursos/{id}/matricular` - Matricular en curso

### Contenidos

- `POST /api/estudiante/materiales/{id}/marcar-visto` - Marcar material como visto

### Evaluaciones

- `GET /api/evaluaciones/{id}` - Obtener evaluación
- `POST /api/evaluaciones/{id}/responder` - Responder evaluación

> **Nota**: Todos los endpoints requieren autenticación mediante Sanctum tokens.

## 🗄️ Base de Datos

### Entidades Principales

- **profesores** - Información de profesores (cédula, nombre, email)
- **estudiantes** - Información de estudiantes (cédula, nombre, email)
- **cursos** - Cursos impartidos por profesores
- **modulos** - Módulos que componen cada curso
- **materiales** - Contenidos digitales (PDF, ZIP, enlaces)
- **matriculas** - Relación estudiante-curso con progreso
- **evaluaciones** - Evaluaciones por curso
- **preguntas** - Preguntas de opción múltiple
- **opciones** - Opciones de respuesta para cada pregunta
- **resultados** - Resultados de evaluaciones por estudiante
- **material_visto** - Seguimiento de materiales vistos por estudiante

## 🛠️ Desarrollo

### Arquitectura MVC

El proyecto sigue estrictamente el patrón **Model-View-Controller (MVC)**:

- **Models**: Ubicados en `/app/Domain/*/Models/` - Representan los datos y la lógica de negocio
- **Views**: Ubicadas en `/resources/views/` y `/resources/js/views/` - Interfaces de usuario (Blade y Vue.js)
- **Controllers**: Ubicados en `/app/Http/Controllers/` - Manejan las peticiones y coordinan Models y Views

### Estructura de Comandos Artisan

```bash
\
# Ejecutar migraciones
php artisan migrate

# Poblar base de datos con datos de prueba
php artisan db:seed
```

## Usuario de prueba

Usar con Postman
Para facilitar las pruebas, se ha creado un usuario de prueba con las siguientes credenciales:

- **Email**: `admin@academia.edu.pa`
- **Contraseña**: `Password123`

Este usuario tiene acceso completo a la plataforma y puede ser utilizado para realizar pruebas de funcionalidad.
