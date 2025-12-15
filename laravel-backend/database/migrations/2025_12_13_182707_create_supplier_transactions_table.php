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
        Schema::create('supplier_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_wallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            // Orders utilise UUID, donc on utilise uuid au lieu de foreignId
            $table->uuid('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->string('type'); // deposit, withdrawal, payment, refund
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_before', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->string('status')->default('completed'); // pending, completed, failed, cancelled
            $table->text('description')->nullable();
            $table->string('reference')->nullable(); // Référence externe (ID transaction API)
            $table->json('metadata')->nullable(); // Données supplémentaires
            $table->timestamps();
            
            // Index pour recherche rapide
            $table->index(['supplier_wallet_id', 'type']);
            $table->index(['store_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_transactions');
    }
};
