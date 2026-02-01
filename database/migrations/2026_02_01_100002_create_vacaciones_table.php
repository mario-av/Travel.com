<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the vacaciones table.
 * Stores vacation package information.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('vacaciones', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 200);
            $table->text('descripcion');
            $table->string('ubicacion', 150);
            $table->decimal('precio', 10, 2);
            $table->integer('duracion_dias');
            $table->integer('plazas_disponibles');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('destacado')->default(false);
            $table->boolean('activo')->default(true);
            $table->foreignId('tipo_id')->constrained('tipos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('vacaciones');
    }
};
