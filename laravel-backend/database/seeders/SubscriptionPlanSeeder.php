<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Version d\'essai pour découvrir la plateforme. Parfait pour débuter dans le dropshipping.',
                'price' => 0,
                'billing_period' => 'monthly',
                'max_stores' => 1,
                'max_products' => 1,
                'trial_days' => 14, // 14 jours d'essai
                'features' => [
                    '1 boutique',
                    '1 produit maximum',
                    '14 jours d\'essai',
                    'Support communautaire',
                    'Accès aux fonctionnalités de base'
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Pour les professionnels qui veulent développer leur activité de dropshipping.',
                'price' => 29.99,
                'billing_period' => 'monthly',
                'max_stores' => 5,
                'max_products' => 100,
                'trial_days' => 0,
                'features' => [
                    '5 boutiques',
                    '100 produits par boutique',
                    'Support prioritaire',
                    'Analytics avancés',
                    'Marketing intégré',
                    'Paiements automatisés'
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Solution complète pour les entreprises avec besoins illimités.',
                'price' => 99.99,
                'billing_period' => 'monthly',
                'max_stores' => -1, // Illimité
                'max_products' => -1, // Illimité
                'trial_days' => 0,
                'features' => [
                    'Boutiques illimitées',
                    'Produits illimités',
                    'Support dédié 24/7',
                    'API personnalisée',
                    'White label',
                    'Toutes les fonctionnalités'
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            // Utiliser DB::table pour forcer les types boolean
            \DB::table('subscription_plans')->updateOrInsert(
                ['slug' => $plan['slug']],
                [
                    'name' => $plan['name'],
                    'description' => $plan['description'],
                    'price' => $plan['price'],
                    'billing_period' => $plan['billing_period'],
                    'max_stores' => $plan['max_stores'],
                    'max_products' => $plan['max_products'],
                    'trial_days' => $plan['trial_days'],
                    'features' => json_encode($plan['features']),
                    'is_active' => DB::raw('true'), // Forcer boolean
                    'sort_order' => $plan['sort_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
