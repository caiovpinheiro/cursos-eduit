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
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('activity_id')->constrained()->onDelete('cascade');
            $table->boolean('completed')->default(true);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Um usuário não pode ter a mesma atividade concluída mais de uma vez
            $table->unique(['user_id', 'activity_id']);
            
            // Índices para performance
            $table->index(['user_id', 'completed']);
            $table->index(['activity_id', 'completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};

