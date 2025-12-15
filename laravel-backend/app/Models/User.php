<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relations
    public function store()
    {
        return $this->hasOne(Store::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // Vérifier si l'utilisateur a un abonnement actif (payant)
    public function hasActiveSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->whereHas('plan', function($q) {
                $q->where('slug', '!=', 'free');
            })
            ->exists();
    }

    // Obtenir l'abonnement actif
    public function activeSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->first();
    }

    // Vérifier si l'utilisateur est sur le plan Free
    public function isOnFreePlan()
    {
        $subscription = $this->activeSubscription();
        if ($subscription) {
            $plan = $subscription->planRelation;
            return $plan && is_object($plan) && isset($plan->slug) && $plan->slug === 'free';
        }
        return false;
    }

    // Obtenir le plan actuel (Free par défaut)
    public function currentPlan()
    {
        $subscription = $this->activeSubscription();
        // Utiliser la relation planRelation() pour éviter la confusion avec l'attribut 'plan' (string)
        if ($subscription) {
            $plan = $subscription->planRelation;
            // Vérifier que c'est bien un objet SubscriptionPlan
            if ($plan && is_object($plan) && $plan instanceof \App\Models\SubscriptionPlan) {
                return $plan;
            }
        }
        
        // Retourner le plan Free par défaut
        if (config('database.default') === 'pgsql') {
            return \App\Models\SubscriptionPlan::whereRaw('slug = ? AND is_active::boolean = true', ['free'])->first();
        }
        return \App\Models\SubscriptionPlan::where('slug', 'free')->where('is_active', true)->first();
    }
}
