<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'api_type',
        'api_key',
        'api_secret',
        'wallet_balance',
        'status',
    ];

    protected $casts = [
        'wallet_balance' => 'float',
    ];
}

