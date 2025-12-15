<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StoreTemplate;
use Illuminate\Support\Facades\DB;

class StoreTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Classic',
                'slug' => 'classic',
                'description' => 'Template classique et simple, parfait pour débuter. Design épuré et professionnel.',
                'category' => 'free',
                'preview_image' => null,
                'layout_config' => [
                    'header_style' => 'simple',
                    'product_grid' => '3-columns',
                    'color_scheme' => 'light',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Modern',
                'slug' => 'modern',
                'description' => 'Template moderne avec animations et design contemporain. Idéal pour une boutique professionnelle.',
                'category' => 'free',
                'preview_image' => null,
                'layout_config' => [
                    'header_style' => 'modern',
                    'product_grid' => '4-columns',
                    'color_scheme' => 'gradient',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Template premium avec toutes les fonctionnalités avancées. Design luxueux et élégant.',
                'category' => 'free',
                'preview_image' => null,
                'layout_config' => [
                    'header_style' => 'premium',
                    'product_grid' => 'responsive',
                    'color_scheme' => 'custom',
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Minimal',
                'slug' => 'minimal',
                'description' => 'Template minimaliste avec beaucoup d\'espace blanc. Design épuré et moderne, parfait pour mettre en valeur vos produits.',
                'category' => 'free',
                'preview_image' => null,
                'layout_config' => [
                    'header_style' => 'minimal',
                    'product_grid' => '2-columns',
                    'color_scheme' => 'light',
                ],
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Vibrant',
                'slug' => 'vibrant',
                'description' => 'Template vibrant et dynamique avec hero percutant et mise en avant produit unique.',
                'category' => 'free',
                'preview_image' => null,
                'layout_config' => [
                    'header_style' => 'vibrant',
                    'product_grid' => '2-columns',
                    'color_scheme' => 'gradient',
                ],
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Elegant',
                'slug' => 'elegant',
                'description' => 'Template sobre et premium avec hero textuel et produit détaillé.',
                'category' => 'free',
                'preview_image' => null,
                'layout_config' => [
                    'header_style' => 'elegant',
                    'product_grid' => '2-columns',
                    'color_scheme' => 'dark',
                ],
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Aurora',
                'slug' => 'aurora',
                'description' => 'Template néon/animé avec hero percutant et mise en avant produit.',
                'category' => 'free',
                'preview_image' => null,
                'layout_config' => [
                    'header_style' => 'aurora',
                    'product_grid' => '2-columns',
                    'color_scheme' => 'neon',
                ],
                'is_active' => true,
                'sort_order' => 7,
            ],
        ];

        foreach ($templates as $template) {
            $existing = StoreTemplate::where('slug', $template['slug'])->first();
            
            if ($existing) {
                // Mettre à jour sans toucher à is_active
                $updateData = $template;
                unset($updateData['is_active']);
                $existing->update($updateData);
            } else {
                // Créer directement avec DB::table pour éviter le problème boolean avec PostgreSQL
                $createData = $template;
                $isActive = $createData['is_active'];
                unset($createData['is_active']);
                
                // Encoder layout_config en JSON
                if (isset($createData['layout_config'])) {
                    $createData['layout_config'] = json_encode($createData['layout_config']);
                }
                
                // Ajouter les timestamps
                $createData['created_at'] = now();
                $createData['updated_at'] = now();
                
                if (config('database.default') === 'pgsql') {
                    // Pour PostgreSQL, utiliser DB::raw pour is_active
                    $createData['is_active'] = DB::raw($isActive ? 'true' : 'false');
                    $id = DB::table('store_templates')->insertGetId($createData);
                } else {
                    // Pour MySQL, créer normalement
                    $createData['is_active'] = $isActive;
                    $id = DB::table('store_templates')->insertGetId($createData);
                }
            }
        }
    }
}
