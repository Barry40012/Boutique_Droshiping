<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierOrder extends Model
{
    use HasFactory;

    protected $table = 'supplier_orders';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'order_id',
        'supplier_id',
        'supplier_order_id',
        'amount_paid',
        'status',
        'tracking_number',
        'supplier_response',
    ];

    protected $casts = [
        'amount_paid' => 'float',
        'supplier_response' => 'array',
    ];
}

