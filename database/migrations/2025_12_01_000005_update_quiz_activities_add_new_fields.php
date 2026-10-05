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
        Schema::table('quiz_activities', function (Blueprint $table) {
            $table->text('description')->nullable()->after('id');
            $table->integer('duration_minutes')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_activities', function (Blueprint $table) {
            $table->dropColumn(['description', 'duration_minutes']);
        });
    }
};

