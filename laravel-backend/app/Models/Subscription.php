<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_id',
        'plan_id',
        'plan',
        'amount',
        'status',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'payment_gateway',
        'payment_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }
    
    // Alias pour éviter la confusion avec l'attribut 'plan' (string)
    public function planRelation()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    // Vérifier si l'abonnement est actif
    public function isActive()
    {
        return $this->status === 'active' 
            && $this->ends_at 
            && $this->ends_at->isFuture();
    }

    // Vérifier si l'abonnement est expiré
    public function isExpired()
    {
        return $this->ends_at && $this->ends_at->isPast();
    }
}
