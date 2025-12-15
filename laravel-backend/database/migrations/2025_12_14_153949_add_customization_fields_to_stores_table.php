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
            // Réseaux sociaux
            $table->string('facebook_url')->nullable()->after('description');
            $table->string('instagram_url')->nullable()->after('facebook_url');
            $table->string('twitter_url')->nullable()->after('instagram_url');
            $table->string('linkedin_url')->nullable()->after('twitter_url');
            $table->string('youtube_url')->nullable()->after('linkedin_url');
            
            // Typographie
            $table->string('heading_font')->default('Inter')->after('accent_color');
            $table->string('body_font')->default('Inter')->after('heading_font');
            
            // Personnalisation des boutons
            $table->string('button_text')->default('Acheter maintenant')->after('body_font');
            $table->string('button_animation')->default('none')->after('button_text'); // none, bounce, pulse, shake, glow
            $table->boolean('show_buy_button_on_card')->default(true)->after('button_animation');
            
            // Organisation de la page
            $table->string('product_layout')->default('grid')->after('show_buy_button_on_card'); // grid, list, masonry
            $table->string('product_image_display')->default('carousel')->after('product_layout'); // carousel, gallery, single
            $table->integer('products_per_row')->default(3)->after('product_image_display');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_url',
                'instagram_url',
                'twitter_url',
                'linkedin_url',
                'youtube_url',
                'heading_font',
                'body_font',
                'button_text',
                'button_animation',
                'show_buy_button_on_card',
                'product_layout',
                'product_image_display',
                'products_per_row',
            ]);
        });
    }
};
