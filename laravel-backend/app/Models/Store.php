<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Store extends Model
{
    use HasFactory;

        protected $fillable = [
            'user_id',
            'template_id',
            'name',
            'slug',
            'logo',
            'banner',
            'primary_color',
            'secondary_color',
            'accent_color',
            'description',
            'category',
            'experience_level',
            'country',
            'currency',
            'domain',
            'is_active',
            'settings',
            // Réseaux sociaux
            'facebook_url',
            'instagram_url',
            'twitter_url',
            'linkedin_url',
            'youtube_url',
            // Typographie
            'heading_font',
            'body_font',
            // Boutons
            'button_text',
            'button_animation',
            'show_buy_button_on_card',
            // Organisation page
            'product_layout',
            'product_image_display',
            'products_per_row',
        ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    // S'assurer que is_active est toujours traité comme boolean dans les requêtes
    // NOTE: On ne met PAS is_active dans $attributes pour éviter que Laravel l'insère automatiquement
    // avec la valeur 1 (integer) au lieu de true (boolean) pour PostgreSQL
    // protected $attributes = [
    //     'is_active' => true,
    // ];
    
    // Override pour s'assurer que is_active est toujours boolean lors de la sauvegarde
    // Même approche que StoreTemplate pour PostgreSQL
    public function setAttribute($key, $value)
    {
        if ($key === 'is_active') {
            // Forcer le cast en boolean strict pour PostgreSQL
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($value === null) {
                $value = false;
            }
            // S'assurer que c'est un boolean PHP, pas un integer
            $value = (bool) $value;
        }
        return parent::setAttribute($key, $value);
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function template()
    {
        return $this->belongsTo(StoreTemplate::class, 'template_id');
    }

    public function supplierWallets()
    {
        return $this->hasMany(SupplierWallet::class);
    }

    // Générer le slug automatiquement
    public static function boot()
    {
        parent::boot();

        static::creating(function ($store) {
            // Générer le slug seulement si vide et si name est défini et est une chaîne
            // Vérifier à la fois dans les attributes et dans les propriétés
            $slug = $store->slug ?? $store->attributes['slug'] ?? null;
            if (empty($slug)) {
                $name = $store->name ?? $store->attributes['name'] ?? null;
                if ($name && is_string($name) && !empty(trim($name))) {
                    $store->slug = Str::slug($name);
                }
            }
            // NE PAS définir is_active ici - il sera ajouté après avec DB::raw pour PostgreSQL
            // Cela évite que Laravel convertisse true en 1 (integer)
            // Retirer is_active des attributes pour qu'il ne soit pas inséré
            if (isset($store->attributes['is_active'])) {
                unset($store->attributes['is_active']);
            }
        });
        
        static::updating(function ($store) {
            // Forcer is_active à être un boolean pour PostgreSQL lors des mises à jour aussi
            if (isset($store->is_active)) {
                $store->is_active = (bool) $store->is_active;
            }
        });
    }

    // URL publique de la boutique
    public function getPublicUrlAttribute()
    {
        if ($this->domain) {
            return 'https://' . $this->domain;
        }
        return url('/store/' . $this->slug);
    }
}
