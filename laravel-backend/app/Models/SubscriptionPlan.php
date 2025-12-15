<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_period',
        'max_stores',
        'max_products',
        'trial_days',
        'features',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        if (config('database.default') === 'pgsql') {
            return $query->whereRaw('is_active::boolean = true');
        }
        return $query->where('is_active', true);
    }

    public function scopeFree($query)
    {
        return $query->where('slug', 'free');
    }

    // Relations
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }
}
