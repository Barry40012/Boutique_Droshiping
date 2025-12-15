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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->constrained('subscription_plans')->onDelete('set null');
            $table->string('plan')->default('monthly'); // monthly, yearly (legacy)
            $table->decimal('amount', 10, 2); // Montant de l'abonnement
            $table->string('status')->default('pending'); // pending, active, cancelled, expired
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('payment_gateway')->nullable(); // korapay, dpo, etc.
            $table->string('payment_id')->nullable(); // ID transaction paiement
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
