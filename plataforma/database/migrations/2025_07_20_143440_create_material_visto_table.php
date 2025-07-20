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
        Schema::create('material_visto', function (Blueprint $table) {
            $table->unsignedBigInteger('estudiante_id');
            $table->unsignedBigInteger('material_id');
            $table->timestamp('visto_en')->useCurrent();

            $table->primary(['estudiante_id', 'material_id']);
            $table->foreign('estudiante_id')->references('id')->on('estudiantes')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materiales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_visto');
    }
};
