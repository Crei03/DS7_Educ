# Base de Datos y Estructura de Programación Orientada a Objetos (POO)

## Modelo entidad-relación (ERD)

- usuarios —< matriculas >— cursos —< modulos —< materiales
- cursos —< evaluaciones —< preguntas —< opciones
- evaluaciones —< resultados >— usuarios

## Convenciones

- Base de datos en snake_case.
- Clases PHP en StudlyCase.

## Seeders

- Utilizar Faker con localización es_PA.
- Cédulas con formato: `\d{1,2}-\d{4,5}-\d{5,6}`
- Cursos de ejemplo: “PHP Básico”, “Finanzas Personales”.

## Script SQL de la base de datos

```sql
-- Tabla de usuarios
CREATE TABLE profesores (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombrePrimario    VARCHAR(60) NOT NULL,
    nombreSecundario  VARCHAR(60),
    apellidoPrimario  VARCHAR(60) NOT NULL,
    apellidoSecundario VARCHAR(60),
    cedula            VARCHAR(20) UNIQUE NOT NULL,
    correo            VARCHAR(120) UNIQUE NOT NULL,
    contrasena        VARCHAR(255) NOT NULL,
    creado_en         TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE estudiantes (
    id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombrePrimario    VARCHAR(60) NOT NULL,
    nombreSecundario  VARCHAR(60),
    apellidoPrimario  VARCHAR(60) NOT NULL,
    apellidoSecundario VARCHAR(60),
    cedula            VARCHAR(20) UNIQUE NOT NULL,
    correo            VARCHAR(120) UNIQUE NOT NULL,
    contrasena        VARCHAR(255) NOT NULL,
    creado_en         TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de cursos
CREATE TABLE cursos (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titulo          VARCHAR(120) NOT NULL,
  descripcion     TEXT,
  profesor_id     BIGINT UNSIGNED,
  creado_en       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (profesor_id) REFERENCES usuarios(id)
);

-- Tabla de módulos por curso
CREATE TABLE modulos (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  curso_id        BIGINT UNSIGNED NOT NULL,
  titulo          VARCHAR(120),
  orden           INT DEFAULT 1,
  FOREIGN KEY (curso_id) REFERENCES cursos(id)
);

-- Tabla de materiales
CREATE TABLE materiales (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  modulo_id       BIGINT UNSIGNED,
  tipo            ENUM('pdf','link','zip') NOT NULL,
  url             VARCHAR(255) NOT NULL,
  titulo          VARCHAR(120),
  FOREIGN KEY (modulo_id) REFERENCES modulos(id)
);

-- Tabla de matrículas
CREATE TABLE matriculas (
  estudiante_id   BIGINT UNSIGNED,
  curso_id        BIGINT UNSIGNED,
  fecha           DATE DEFAULT CURRENT_DATE,
  progreso        DECIMAL(5,2) DEFAULT 0,
  PRIMARY KEY (estudiante_id, curso_id),
  FOREIGN KEY (estudiante_id) REFERENCES usuarios(id),
  FOREIGN KEY (curso_id) REFERENCES cursos(id)
);

-- Tabla de evaluaciones
CREATE TABLE evaluaciones (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  curso_id        BIGINT UNSIGNED,
  titulo          VARCHAR(120),
  duracion_min    SMALLINT,
  FOREIGN KEY (curso_id) REFERENCES cursos(id)
);

-- Tabla de preguntas
CREATE TABLE preguntas (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  evaluacion_id   BIGINT UNSIGNED,
  enunciado       TEXT,
  FOREIGN KEY (evaluacion_id) REFERENCES evaluaciones(id)
);

-- Tabla de opciones de respuesta
CREATE TABLE opciones (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pregunta_id     BIGINT UNSIGNED,
  texto           VARCHAR(255),
  es_correcta     BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (pregunta_id) REFERENCES preguntas(id)
);

-- Tabla de resultados
CREATE TABLE resultados (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  evaluacion_id   BIGINT UNSIGNED,
  estudiante_id   BIGINT UNSIGNED,
  puntaje         DECIMAL(5,2),
  fecha           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (evaluacion_id) REFERENCES evaluaciones(id),
  FOREIGN KEY (estudiante_id) REFERENCES usuarios(id)
);
```

## Estructura de Programación Orientada a Objetos (POO)

```php

class User
{
    // Atributos (propiedades)
    private int $id;
    private string $nombre;
    private string $apellido;
    private string $email;
    private string $rol;       // estudiante | profesor

    // Métodos públicos
    public function enroll(Course $course): void;
    public function isProfessor(): bool;
    public function fullName(): string;

    // Métodos privados
    private function hashPassword(string $plain): string;
}

class Course
{
    private int $id;
    private string $titulo;
    private User $profesor;

    public function addModule(Module $module): void;
    public function students(): Collection;      // lista de User
    public function averageScore(): float;

    private function generateSlug(): void;
}

class Module
{
    private int $id;
    private string $titulo;
    private int $orden;
    private Course $course;

    public function addMaterial(Material $mat): void;
}

class Material
{
    private int $id;
    private string $tipo; // pdf | link | zip
    private string $url;
    private Module $module;
}

class Assessment
{
    private int $id;
    private string $titulo;
    private Course $course;
    private Collection $preguntas;

    public function grade(User $student, array $respuestas): Result;
}

class Question
{
    private int $id;
    private string $enunciado;
    private Collection $opciones;

    public function correctOption(): Option;
}

class Option
{
    private int $id;
    private string $texto;
    private bool $esCorrecta;
}

class Result
{
    private int $id;
    private User $student;
    private Assessment $assessment;
    private float $puntaje;
}
```
