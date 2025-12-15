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
        // Pour PostgreSQL, s'assurer que is_active est bien un boolean
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE store_templates ALTER COLUMN is_active TYPE boolean USING CASE WHEN is_active::text = \'1\' THEN true ELSE false END');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pas besoin de rollback pour cette correction
    }
};

