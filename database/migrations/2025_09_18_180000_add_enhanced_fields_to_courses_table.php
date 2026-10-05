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
        Schema::table('courses', function (Blueprint $table) {
            // Imagem de capa
            $table->string('cover_image_url')->nullable()->after('description');
            
            // Descrições
            $table->string('short_description')->nullable()->after('cover_image_url');
            $table->longText('long_description')->nullable()->after('short_description');
            
            // Informações do curso
            $table->integer('modules_count')->default(0)->after('workload');
            $table->enum('difficulty_level', ['iniciante', 'intermediario', 'avancado'])->default('iniciante')->after('modules_count');
            $table->string('category')->nullable()->after('difficulty_level');
            
            // Preços e descontos
            $table->decimal('price', 10, 2)->default(0.00)->after('category');
            $table->decimal('promotional_price', 10, 2)->nullable()->after('price');
            $table->integer('discount_percentage')->nullable()->after('promotional_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'cover_image_url',
                'short_description',
                'long_description',
                'modules_count',
                'difficulty_level',
                'category',
                'price',
                'promotional_price',
                'discount_percentage',
            ]);
        });
    }
};
