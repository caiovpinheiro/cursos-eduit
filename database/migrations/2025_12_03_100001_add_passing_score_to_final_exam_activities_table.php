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
        Schema::table('final_exam_activities', function (Blueprint $table) {
            $table->decimal('passing_score', 5, 2)->default(70.00)->after('duration_minutes')
                ->comment('Minimum score required to pass (0-100)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('final_exam_activities', function (Blueprint $table) {
            $table->dropColumn('passing_score');
        });
    }
};

