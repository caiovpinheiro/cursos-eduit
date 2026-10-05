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
        Schema::table('video_activities', function (Blueprint $table) {
            $table->text('description')->nullable()->after('id');
            $table->longText('transcript')->nullable()->after('description');
            $table->renameColumn('url', 'link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_activities', function (Blueprint $table) {
            $table->dropColumn(['description', 'transcript']);
            $table->renameColumn('link', 'url');
        });
    }
};

