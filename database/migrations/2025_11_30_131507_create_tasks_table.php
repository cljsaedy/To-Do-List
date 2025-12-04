<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // THIS IS THE ONLY SCHEMA::CREATE BLOCK YOU NEED
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('priority', ['Low', 'Medium', 'High'])->default('Medium');
            $table->date('deadline');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations (Good practice to include down() method).
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};