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
        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Nom du fournisseur (AliExpress, CJ Dropshipping, etc.)
                $table->string('slug')->unique();
                $table->string('type')->default('aliexpress'); // aliexpress, cj_dropshipping, temu, custom
                $table->text('description')->nullable();
                $table->string('api_endpoint')->nullable(); // URL de l'API
                $table->text('api_key')->nullable(); // Clé API (encryptée)
                $table->text('api_secret')->nullable(); // Secret API (encrypté)
                $table->json('api_config')->nullable(); // Configuration supplémentaire
                $table->boolean('is_active')->default(true);
                $table->boolean('auto_fulfill')->default(true); // Automatisation activée
                $table->timestamps();
            });
        } else {
            // La table existe déjà, on ajoute seulement les colonnes manquantes
            Schema::table('suppliers', function (Blueprint $table) {
                if (!Schema::hasColumn('suppliers', 'slug')) {
                    $table->string('slug')->unique()->after('name');
                }
                if (!Schema::hasColumn('suppliers', 'type')) {
                    $table->string('type')->default('aliexpress')->after('slug');
                }
                if (!Schema::hasColumn('suppliers', 'description')) {
                    $table->text('description')->nullable()->after('type');
                }
                if (!Schema::hasColumn('suppliers', 'api_endpoint')) {
                    $table->string('api_endpoint')->nullable()->after('description');
                }
                if (!Schema::hasColumn('suppliers', 'api_key')) {
                    $table->text('api_key')->nullable()->after('api_endpoint');
                }
                if (!Schema::hasColumn('suppliers', 'api_secret')) {
                    $table->text('api_secret')->nullable()->after('api_key');
                }
                if (!Schema::hasColumn('suppliers', 'api_config')) {
                    $table->json('api_config')->nullable()->after('api_secret');
                }
                if (!Schema::hasColumn('suppliers', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('api_config');
                }
                if (!Schema::hasColumn('suppliers', 'auto_fulfill')) {
                    $table->boolean('auto_fulfill')->default(true)->after('is_active');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
