<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'supplier_id',
        'balance',
        'reserved_balance',
        'total_deposited',
        'total_spent',
        'low_balance_threshold',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'reserved_balance' => 'decimal:2',
        'total_deposited' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'low_balance_threshold' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relations
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function transactions()
    {
        return $this->hasMany(SupplierTransaction::class);
    }

    // Méthodes utilitaires
    public function availableBalance()
    {
        return $this->balance - $this->reserved_balance;
    }

    public function hasSufficientBalance($amount)
    {
        return $this->availableBalance() >= $amount;
    }

    public function isLowBalance()
    {
        return $this->balance <= $this->low_balance_threshold;
    }

    // Déposer de l'argent
    public function deposit($amount, $description = null, $reference = null)
    {
        $balanceBefore = $this->balance;
        $this->balance += $amount;
        $this->total_deposited += $amount;
        $this->save();

        // Créer une transaction
        SupplierTransaction::create([
            'supplier_wallet_id' => $this->id,
            'store_id' => $this->store_id,
            'type' => 'deposit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'status' => 'completed',
            'description' => $description ?? 'Dépôt de fonds',
            'reference' => $reference,
        ]);

        return $this;
    }

    // Retirer de l'argent (pour payer le fournisseur)
    public function withdraw($amount, $orderId = null, $description = null, $reference = null)
    {
        if (!$this->hasSufficientBalance($amount)) {
            throw new \Exception('Solde insuffisant');
        }

        $balanceBefore = $this->balance;
        $this->balance -= $amount;
        $this->total_spent += $amount;
        
        // Libérer la balance réservée si c'est pour une commande
        if ($orderId && $this->reserved_balance >= $amount) {
            $this->reserved_balance -= $amount;
        }
        
        $this->save();

        // Créer une transaction
        SupplierTransaction::create([
            'supplier_wallet_id' => $this->id,
            'store_id' => $this->store_id,
            'order_id' => $orderId,
            'type' => 'payment',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'status' => 'completed',
            'description' => $description ?? 'Paiement fournisseur',
            'reference' => $reference,
        ]);

        return $this;
    }

    // Réserver de l'argent (pour une commande en cours)
    public function reserve($amount, $orderId = null)
    {
        if (!$this->hasSufficientBalance($amount)) {
            throw new \Exception('Solde insuffisant pour réserver');
        }

        $this->reserved_balance += $amount;
        $this->save();

        return $this;
    }

    // Libérer une réservation
    public function releaseReservation($amount)
    {
        $this->reserved_balance = max(0, $this->reserved_balance - $amount);
        $this->save();

        return $this;
    }
}
