<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the vacations table.
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
        Schema::create('vacations', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->text('description');
            $table->string('location', 150);
            $table->decimal('price', 10, 2);
            $table->integer('duration_days');
            $table->integer('available_slots');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('featured')->default(false);
            $table->boolean('active')->default(true);
            $table->boolean('approved')->default(true); // Default true for legacy, false for new advanced users
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
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
        Schema::dropIfExists('vacations');
    }
};
