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
            $table->string('language', 10)->default('fr')->after('footer_title_color'); // fr, en
            $table->text('why_choose_product')->nullable()->after('language'); // Section personnalisable "Pourquoi choisir ce produit"
            $table->json('product_banner_steps')->nullable()->after('why_choose_product'); // Étapes du produit pour la bannière
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['language', 'why_choose_product', 'product_banner_steps']);
        });
    }
};
