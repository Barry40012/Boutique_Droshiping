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
            // Personnalisation des sections produit et avis (fond + coins arrondis)
            $table->string('product_section_bg_color', 7)->nullable()->after('page_bg_color');
            $table->boolean('product_section_rounded')->nullable()->after('product_section_bg_color');

            $table->string('reviews_section_bg_color', 7)->nullable()->after('product_section_rounded');
            $table->boolean('reviews_section_rounded')->nullable()->after('reviews_section_bg_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('product_section_bg_color');
            $table->dropColumn('product_section_rounded');
            $table->dropColumn('reviews_section_bg_color');
            $table->dropColumn('reviews_section_rounded');
        });
    }
};


