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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Nom de la boutique
            $table->string('slug')->unique(); // URL unique de la boutique (ex: mon-boutique)
            $table->string('logo')->nullable(); // URL du logo
            $table->string('primary_color')->default('#0ea5e9'); // Couleur principale
            $table->string('secondary_color')->default('#a855f7'); // Couleur secondaire
            $table->string('accent_color')->default('#f97316'); // Couleur accent
            $table->text('description')->nullable(); // Description de la boutique
            $table->string('domain')->nullable(); // Domaine personnalisé (optionnel)
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // Paramètres additionnels (JSON)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
