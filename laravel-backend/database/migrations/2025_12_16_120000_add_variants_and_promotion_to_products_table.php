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
        Schema::table('products', function (Blueprint $table) {
            // Variantes (stockées en JSONB pour PostgreSQL)
            if (!Schema::hasColumn('products', 'variants')) {
                $table->jsonb('variants')->nullable()->after('description');
            }
            
            // Promotion
            if (!Schema::hasColumn('products', 'has_promotion')) {
                $table->boolean('has_promotion')->default(false)->after('selling_price');
            }
            if (!Schema::hasColumn('products', 'promo_price')) {
                $table->decimal('promo_price', 10, 2)->nullable()->after('has_promotion');
            }
            if (!Schema::hasColumn('products', 'promo_start_date')) {
                $table->timestamp('promo_start_date')->nullable()->after('promo_price');
            }
            if (!Schema::hasColumn('products', 'promo_end_date')) {
                $table->timestamp('promo_end_date')->nullable()->after('promo_start_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'variants')) {
                $table->dropColumn('variants');
            }
            if (Schema::hasColumn('products', 'has_promotion')) {
                $table->dropColumn('has_promotion');
            }
            if (Schema::hasColumn('products', 'promo_price')) {
                $table->dropColumn('promo_price');
            }
            if (Schema::hasColumn('products', 'promo_start_date')) {
                $table->dropColumn('promo_start_date');
            }
            if (Schema::hasColumn('products', 'promo_end_date')) {
                $table->dropColumn('promo_end_date');
            }
        });
    }
};

