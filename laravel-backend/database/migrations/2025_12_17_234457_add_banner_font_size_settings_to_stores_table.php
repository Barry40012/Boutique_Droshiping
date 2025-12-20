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
            // Police des textes de bannière (messages défilants)
            $table->string('banner_text_font', 100)->nullable()->after('banner_text_animation');
            // Taille des textes de bannière (messages défilants)
            $table->string('banner_text_size', 20)->nullable()->after('banner_text_font');
            // Taille du nom de la boutique sur la bannière
            $table->string('banner_title_size', 20)->nullable()->after('banner_title_animation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('banner_text_font');
            $table->dropColumn('banner_text_size');
            $table->dropColumn('banner_title_size');
        });
    }
};
