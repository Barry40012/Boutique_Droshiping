<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierWallet;
use App\Models\SupplierTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AutomationService
{
    /**
     * Traiter automatiquement une commande après paiement
     */
    public function processOrder(Order $order): bool
    {
        try {
            DB::beginTransaction();

            // Pour chaque produit de la commande
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                
                if (!$product || !$product->supplier_id) {
                    Log::warning("Produit sans fournisseur: {$item->product_id}");
                    continue;
                }

                // Récupérer le fournisseur avec gestion PostgreSQL
                if (config('database.default') === 'pgsql') {
                    $supplier = Supplier::whereRaw('id = ? AND is_active::boolean = true', [$product->supplier_id])->first();
                } else {
                    $supplier = Supplier::where('id', $product->supplier_id)->where('is_active', true)->first();
                }
                
                if (!$supplier || !$supplier->auto_fulfill) {
                    Log::warning("Fournisseur non configuré pour automatisation: {$product->supplier_id}");
                    continue;
                }

                // Récupérer ou créer le wallet pour ce fournisseur
                $wallet = SupplierWallet::firstOrCreate(
                    [
                        'store_id' => $order->store_id,
                        'supplier_id' => $supplier->id,
                    ],
                    [
                        'balance' => 0,
                        'reserved_balance' => 0,
                        'total_deposited' => 0,
                        'total_spent' => 0,
                        'low_balance_threshold' => 50,
                        'is_active' => true,
                    ]
                );

                // Calculer le coût fournisseur pour cet item
                $supplierCost = $product->supplier_price * $item->quantity;

                // Vérifier si le wallet a suffisamment de balance
                if (!$wallet->hasSufficientBalance($supplierCost)) {
                    Log::error("Solde insuffisant dans le wallet pour la commande {$order->id}. Solde disponible: {$wallet->availableBalance()}, Requis: {$supplierCost}");
                    
                    // Optionnel: Envoyer une notification au merchant
                    // NotificationService::notifyLowBalance($wallet);
                    
                    DB::rollBack();
                    return false;
                }

                // Réserver l'argent
                $wallet->reserve($supplierCost, $order->id);

                // Envoyer la commande au fournisseur via API
                $fulfillmentResult = $this->fulfillOrder($supplier, $product, $order, $item);

                if ($fulfillmentResult['success']) {
                    // Débiter le wallet
                    $wallet->withdraw(
                        $supplierCost,
                        $order->id,
                        "Paiement automatique pour commande {$order->id} - Produit: {$product->name}",
                        $fulfillmentResult['reference'] ?? null
                    );

                    // Mettre à jour le statut de la commande
                    $order->update([
                        'status' => 'processing',
                    ]);

                    Log::info("Commande {$order->id} traitée automatiquement avec succès");
                } else {
                    // Libérer la réservation en cas d'échec
                    $wallet->releaseReservation($supplierCost);
                    
                    Log::error("Échec du traitement automatique pour la commande {$order->id}: " . ($fulfillmentResult['error'] ?? 'Erreur inconnue'));
                    
                    DB::rollBack();
                    return false;
                }
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors du traitement automatique de la commande {$order->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoyer la commande au fournisseur via API
     */
    protected function fulfillOrder(Supplier $supplier, Product $product, Order $order, OrderItem $item): array
    {
        try {
            switch ($supplier->type) {
                case 'aliexpress':
                    return $this->fulfillAliExpress($supplier, $product, $order, $item);
                
                case 'cj_dropshipping':
                    return $this->fulfillCJDropshipping($supplier, $product, $order, $item);
                
                case 'temu':
                    return $this->fulfillTemu($supplier, $product, $order, $item);
                
                case 'custom':
                    return $this->fulfillCustom($supplier, $product, $order, $item);
                
                default:
                    return [
                        'success' => false,
                        'error' => 'Type de fournisseur non supporté'
                    ];
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de commande au fournisseur: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Fulfillment AliExpress (à implémenter avec l'API réelle)
     */
    protected function fulfillAliExpress(Supplier $supplier, Product $product, Order $order, OrderItem $item): array
    {
        // TODO: Implémenter l'appel API AliExpress
        // Pour l'instant, on simule un succès
        
        Log::info("Simulation fulfillment AliExpress pour produit {$product->supplier_product_id}");
        
        return [
            'success' => true,
            'reference' => 'ALX-' . time(),
            'tracking_number' => null, // Sera mis à jour plus tard
        ];
    }

    /**
     * Fulfillment CJ Dropshipping (à implémenter avec l'API réelle)
     */
    protected function fulfillCJDropshipping(Supplier $supplier, Product $product, Order $order, OrderItem $item): array
    {
        // TODO: Implémenter l'appel API CJ Dropshipping
        
        Log::info("Simulation fulfillment CJ Dropshipping pour produit {$product->supplier_product_id}");
        
        return [
            'success' => true,
            'reference' => 'CJ-' . time(),
            'tracking_number' => null,
        ];
    }

    /**
     * Fulfillment Temu (à implémenter avec l'API réelle)
     */
    protected function fulfillTemu(Supplier $supplier, Product $product, Order $order, OrderItem $item): array
    {
        // TODO: Implémenter l'appel API Temu
        
        return [
            'success' => false,
            'error' => 'Temu API non encore implémentée'
        ];
    }

    /**
     * Fulfillment personnalisé (webhook ou email)
     */
    protected function fulfillCustom(Supplier $supplier, Product $product, Order $order, OrderItem $item): array
    {
        // Pour les fournisseurs personnalisés, on peut envoyer un email ou webhook
        // TODO: Implémenter l'envoi d'email/webhook
        
        Log::info("Simulation fulfillment personnalisé pour produit {$product->supplier_product_id}");
        
        return [
            'success' => true,
            'reference' => 'CUSTOM-' . time(),
            'tracking_number' => null,
        ];
    }

    /**
     * Recharger le wallet d'un fournisseur
     */
    public function depositToWallet(SupplierWallet $wallet, float $amount, string $description = null, string $reference = null): bool
    {
        try {
            $wallet->deposit($amount, $description, $reference);
            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors du dépôt dans le wallet: " . $e->getMessage());
            return false;
        }
    }
}

