<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'api_endpoint',
        'api_key',
        'api_secret',
        'api_config',
        'is_active',
        'auto_fulfill',
    ];

    protected $casts = [
        'api_config' => 'array',
        'is_active' => 'boolean',
        'auto_fulfill' => 'boolean',
    ];

    // Relations
    public function wallets()
    {
        return $this->hasMany(SupplierWallet::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        if (config('database.default') === 'pgsql') {
            return $query->whereRaw('is_active::boolean = true');
        }
        return $query->where('is_active', true);
    }

    // Générer le slug automatiquement
    public static function boot()
    {
        parent::boot();

        static::creating(function ($supplier) {
            if (empty($supplier->slug)) {
                $supplier->slug = Str::slug($supplier->name);
            }
        });
    }
}
