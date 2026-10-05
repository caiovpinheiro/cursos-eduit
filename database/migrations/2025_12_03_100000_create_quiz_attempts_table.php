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
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('questionable'); // questionable_id, questionable_type (QuizActivity ou FinalExamActivity)
            $table->integer('attempt_number')->default(1);
            $table->json('answers'); // [{question_id: 1, selected_option: 'a'}, ...]
            $table->integer('total_questions');
            $table->integer('correct_answers');
            $table->decimal('score', 5, 2); // Porcentagem (0-100.00)
            $table->boolean('passed')->default(false); // Se passou (apenas relevante para final_exam)
            $table->timestamp('submitted_at');
            $table->timestamps();
            
            // Índices para performance
            $table->index(['user_id', 'questionable_type', 'questionable_id']);
            $table->index(['user_id', 'attempt_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};

