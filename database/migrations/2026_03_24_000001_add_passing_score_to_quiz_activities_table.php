<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('quiz_activities', 'passing_score')) {
            return;
        }

        Schema::table('quiz_activities', function (Blueprint $table) {
            $table->decimal('passing_score', 5, 2)->default(70)->after('duration_minutes');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('quiz_activities', 'passing_score')) {
            return;
        }

        Schema::table('quiz_activities', function (Blueprint $table) {
            $table->dropColumn('passing_score');
        });
    }
};
