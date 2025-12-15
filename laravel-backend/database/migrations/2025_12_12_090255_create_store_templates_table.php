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
        Schema::create('store_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom du template (ex: "Classic", "Modern", "Premium")
            $table->string('slug')->unique(); // Slug unique
            $table->text('description')->nullable(); // Description du template
            $table->string('preview_image')->nullable(); // Image de prévisualisation
            $table->string('category')->default('free'); // free, pro, premium
            $table->json('layout_config')->nullable(); // Configuration du layout (JSON)
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0); // Ordre d'affichage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_templates');
    }
};
