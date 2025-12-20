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
            // Couleur au survol des liens de navigation dans le header
            $table->string('header_nav_hover_color', 7)->nullable()->after('header_text_color');

            // Couleur de fond principale de la page de la boutique (en dehors de l'entête / footer)
            $table->string('page_bg_color', 7)->nullable()->after('footer_title_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('header_nav_hover_color');
            $table->dropColumn('page_bg_color');
        });
    }
};


