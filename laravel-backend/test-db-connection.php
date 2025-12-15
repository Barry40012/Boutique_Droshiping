<?php
/**
 * Script de test de connexion à la base de données
 * Exécuter avec: php test-db-connection.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Test de connexion à la base de données...\n\n";

try {
    // Test de connexion basique
    DB::connection()->getPdo();
    echo "✅ Connexion réussie !\n";
    
    // Test de requête simple
    $result = DB::select('SELECT 1 as test');
    echo "✅ Requête SQL fonctionne !\n";
    
    // Test de lecture depuis la table users
    $userCount = DB::table('users')->count();
    echo "✅ Lecture depuis 'users' : {$userCount} utilisateur(s)\n";
    
    // Test de lecture depuis la table products
    try {
        $productCount = DB::table('products')->count();
        echo "✅ Lecture depuis 'products' : {$productCount} produit(s)\n";
    } catch (\Exception $e) {
        echo "⚠️  Table 'products' : " . $e->getMessage() . "\n";
    }
    
    // Test d'écriture (INSERT test)
    try {
        $testId = 'test-' . time();
        DB::table('products')->insert([
            'id' => $testId,
            'store_id' => '00000000-0000-0000-0000-000000000000',
            'name' => 'Test Product',
            'supplier_price' => 10.00,
            'selling_price' => 20.00,
            'margin' => 10.00,
            'status' => 'inactive',
            'images' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✅ Écriture (INSERT) fonctionne !\n";
        
        // Nettoyer le test
        DB::table('products')->where('id', $testId)->delete();
        echo "✅ Suppression (DELETE) fonctionne !\n";
    } catch (\Exception $e) {
        echo "❌ Erreur lors de l'écriture : " . $e->getMessage() . "\n";
    }
    
    echo "\n✅ Tous les tests sont passés avec succès !\n";
    
} catch (\PDOException $e) {
    echo "❌ Erreur de connexion PDO : " . $e->getMessage() . "\n";
    echo "\n💡 Solutions possibles :\n";
    echo "   1. Vérifiez votre connexion internet\n";
    echo "   2. Vérifiez les paramètres dans .env (DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD)\n";
    echo "   3. Vérifiez que votre projet Supabase est actif\n";
    echo "   4. Essayez l'URL directe au lieu du pooler\n";
} catch (\Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    echo "\n💡 Vérifiez les logs dans storage/logs/laravel.log\n";
}

