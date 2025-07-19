<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profesores', function (Blueprint $table) {
            $table->id();
            $table->string('nombrePrimario', 60);
            $table->string('nombreSecundario', 60)->nullable();
            $table->string('apellidoPrimario', 60);
            $table->string('apellidoSecundario', 60)->nullable();
            $table->string('cedula', 20)->unique();
            $table->string('correo', 120)->unique();
            $table->string('contrasena');
            $table->timestamp('creado_en')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profesores');
    }
};
