<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_wallet_id',
        'store_id',
        'order_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'status',
        'description',
        'reference',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
    ];

    // Relations
    public function wallet()
    {
        return $this->belongsTo(SupplierWallet::class, 'supplier_wallet_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes
    public function scopeDeposits($query)
    {
        return $query->where('type', 'deposit');
    }

    public function scopePayments($query)
    {
        return $query->where('type', 'payment');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
