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
        Schema::table('article_activities', function (Blueprint $table) {
            $table->text('description')->nullable()->after('id');
            $table->renameColumn('content', 'content_richtext');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('article_activities', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->renameColumn('content_richtext', 'content');
        });
    }
};

