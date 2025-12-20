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
            $table->string('footer_bg_color', 7)->nullable()->after('header_name_font');
            $table->string('footer_text_color', 7)->nullable()->after('footer_bg_color');
            $table->string('footer_link_color', 7)->nullable()->after('footer_text_color');
            $table->string('footer_title_color', 7)->nullable()->after('footer_link_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'footer_bg_color',
                'footer_text_color',
                'footer_link_color',
                'footer_title_color',
            ]);
        });
    }
};
