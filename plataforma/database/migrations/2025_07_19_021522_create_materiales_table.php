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
        Schema::create('materiales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('modulo_id')->nullable();
            $table->enum('tipo', ['pdf', 'link', 'zip']);
            $table->string('url');
            $table->string('titulo', 120)->nullable();

            $table->foreign('modulo_id')->references('id')->on('modulos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materiales');
    }
};
