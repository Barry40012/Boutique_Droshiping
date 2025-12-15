<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'AliExpress',
                'slug' => 'aliexpress',
                'type' => 'aliexpress',
                'description' => 'Plateforme de dropshipping AliExpress - Import automatique de produits',
                'is_active' => true,
                'auto_fulfill' => true,
            ],
            [
                'name' => 'CJ Dropshipping',
                'slug' => 'cj-dropshipping',
                'type' => 'cj_dropshipping',
                'description' => 'Service de dropshipping professionnel avec API complète',
                'is_active' => true,
                'auto_fulfill' => true,
            ],
            [
                'name' => 'Temu',
                'slug' => 'temu',
                'type' => 'temu',
                'description' => 'Plateforme e-commerce avec options de dropshipping',
                'is_active' => true,
                'auto_fulfill' => false,
            ],
            [
                'name' => 'Fournisseur Personnalisé',
                'slug' => 'custom',
                'type' => 'custom',
                'description' => 'Fournisseur personnalisé - Configuration manuelle',
                'is_active' => true,
                'auto_fulfill' => false,
            ],
        ];

        foreach ($suppliers as $supplier) {
            // Vérifier si la colonne api_type existe
            $hasApiType = Schema::hasColumn('suppliers', 'api_type');
            
            $data = [
                'name' => $supplier['name'],
                'type' => $supplier['type'],
                'description' => $supplier['description'],
                'is_active' => DB::raw('true'), // Forcer boolean
                'auto_fulfill' => DB::raw($supplier['auto_fulfill'] ? 'true' : 'false'), // Forcer boolean
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Si api_type existe, l'utiliser comme alias de type
            if ($hasApiType) {
                $data['api_type'] = $supplier['type'];
            }
            
            // Utiliser DB::table pour forcer les types boolean
            \DB::table('suppliers')->updateOrInsert(
                ['slug' => $supplier['slug']],
                $data
            );
        }
    }
}
