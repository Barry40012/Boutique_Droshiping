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
            // Personnalisation bannière
            $table->string('banner_title_color', 7)->nullable()->after('accent_color');
            $table->string('banner_title_animation', 20)->nullable()->default('none')->after('banner_title_color');
            $table->string('banner_button_text', 50)->nullable()->after('banner_title_animation');
            $table->string('banner_button_color', 7)->nullable()->after('banner_button_text');
            
            // Personnalisation header
            $table->string('header_bg_color', 7)->nullable()->after('banner_button_color');
            $table->string('header_text_color', 7)->nullable()->after('header_bg_color');
            $table->string('header_name_color', 7)->nullable()->after('header_text_color');
            $table->string('header_name_font', 255)->nullable()->after('header_name_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'banner_title_color',
                'banner_title_animation',
                'banner_button_text',
                'banner_button_color',
                'header_bg_color',
                'header_text_color',
                'header_name_color',
                'header_name_font',
            ]);
        });
    }
};
