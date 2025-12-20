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
        Schema::table('stores', function (Blueprint $table) {
            // Couleur des textes de bannière (messages défilants)
            $table->string('banner_text_color', 7)->nullable()->after('banner_title_animation');
            // Type d'animation pour les textes de bannière (scroll, typewriter, etc.)
            $table->string('banner_text_animation', 32)->nullable()->after('banner_text_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('banner_text_color');
            $table->dropColumn('banner_text_animation');
        });
    }
};


