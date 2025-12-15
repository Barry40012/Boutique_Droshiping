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
            if (!Schema::hasColumn('products', 'banner')) {
                $table->string('banner')->nullable()->after('images');
            }
            if (!Schema::hasColumn('products', 'supplier_link')) {
                $table->string('supplier_link')->nullable()->after('supplier_product_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'banner')) {
                $table->dropColumn('banner');
            }
            if (Schema::hasColumn('products', 'supplier_link')) {
                $table->dropColumn('supplier_link');
            }
        });
    }
};

