<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'order_id',
        'amount',
        'payment_method',
        'payment_gateway',
        'transaction_id',
        'status',
        'gateway_response',
    ];

    protected $casts = [
        'amount' => 'float',
        'gateway_response' => 'array',
    ];
}

