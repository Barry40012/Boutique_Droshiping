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
        // Nettoyer les doublons dans product_images
        // Garder seulement la première occurrence de chaque image_url par product_id
        if (config('database.default') === 'pgsql') {
            // Pour PostgreSQL : utiliser ROW_NUMBER() car MIN() ne fonctionne pas avec UUID
            DB::statement("
                DELETE FROM product_images
                WHERE id IN (
                    SELECT id
                    FROM (
                        SELECT id,
                               ROW_NUMBER() OVER (PARTITION BY product_id, image_url ORDER BY created_at ASC) as rn
                        FROM product_images
                    ) t
                    WHERE rn > 1
                )
            ");
        } else {
            // Pour MySQL/SQLite
            DB::statement("
                DELETE pi1 FROM product_images pi1
                INNER JOIN product_images pi2
                WHERE pi1.id > pi2.id
                AND pi1.product_id = pi2.product_id
                AND pi1.image_url = pi2.image_url
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cette migration ne peut pas être annulée
        // Les doublons supprimés ne peuvent pas être restaurés
    }
};

