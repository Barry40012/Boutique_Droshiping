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
            $table->string('banner_button_bg_color', 7)->nullable()->after('banner_button_color');
            $table->string('banner_button_text_color', 7)->nullable()->after('banner_button_bg_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'banner_button_bg_color',
                'banner_button_text_color',
            ]);
        });
    }
};
