<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('supplier_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            
            // Vérifier le type de l'ID de suppliers (peut être uuid ou bigint)
            $supplierIdType = DB::selectOne("SELECT data_type FROM information_schema.columns WHERE table_name = 'suppliers' AND column_name = 'id'");
            
            if ($supplierIdType && $supplierIdType->data_type === 'uuid') {
                $table->uuid('supplier_id');
                $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            } else {
                $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            }
            
            $table->decimal('balance', 10, 2)->default(0); // Balance disponible
            $table->decimal('reserved_balance', 10, 2)->default(0); // Balance réservée pour commandes en cours
            $table->decimal('total_deposited', 10, 2)->default(0); // Total déposé
            $table->decimal('total_spent', 10, 2)->default(0); // Total dépensé
            $table->decimal('low_balance_threshold', 10, 2)->default(50); // Seuil d'alerte
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Un wallet unique par store + supplier
            $table->unique(['store_id', 'supplier_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_wallets');
    }
};
