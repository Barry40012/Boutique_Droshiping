<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'store_id',
        'customer_id',
        'total_amount',
        'supplier_cost',
        'margin',
        'status',
        'payment_status',
        'payment_id',
        'payment_gateway',
        'shipping_address',
        'tracking_number',
    ];

    // Relations
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    protected $casts = [
        'total_amount' => 'float',
        'supplier_cost' => 'float',
        'margin' => 'float',
        'shipping_address' => 'array',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function supplierTransactions()
    {
        return $this->hasMany(SupplierTransaction::class);
    }
}

