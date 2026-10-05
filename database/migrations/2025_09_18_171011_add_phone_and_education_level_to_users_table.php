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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('cpf');
            $table->enum('education_level', [
                'fundamental',           // 1 - Ensino Fundamental
                'medio_incompleto',      // 2 - Ensino Médio Incompleto
                'medio_completo',        // 3 - Ensino Médio Completo
                'superior_incompleto',   // 4 - Ensino Superior Incompleto
                'superior_completo',     // 5 - Ensino Superior Completo
                'pos_graduacao'          // 6 - Pós-graduação
            ])->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'education_level']);
        });
    }
};