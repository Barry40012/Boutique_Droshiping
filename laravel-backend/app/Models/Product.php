<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'description',
        'supplier_price',
        'selling_price',
        'margin',
        'images',
        'supplier_id',
        'supplier_product_id',
        'status',
    ];

    protected $casts = [
        'images' => 'array',
        'supplier_price' => 'float',
        'selling_price' => 'float',
        'margin' => 'float',
    ];
}

