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
        Schema::create('quiz_activities', function (Blueprint $table) {
            $table->id();
            $table->json('questions')->nullable();
            $table->integer('time_limit')->nullable(); // Time limit in minutes
            $table->integer('passing_score')->default(70); // Minimum score to pass (percentage)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_activities');
    }
};
