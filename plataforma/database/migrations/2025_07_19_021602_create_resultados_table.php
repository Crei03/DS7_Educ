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
        Schema::create('resultados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluacion_id')->nullable();
            $table->unsignedBigInteger('estudiante_id')->nullable();
            $table->decimal('puntaje', 5, 2)->nullable();
            $table->timestamp('fecha')->useCurrent();

            $table->foreign('evaluacion_id')->references('id')->on('evaluaciones');
            $table->foreign('estudiante_id')->references('id')->on('estudiantes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultados');
    }
};
