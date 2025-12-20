<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'store_id',
        'name',
        'description',
        'supplier_price',
        'selling_price',
        'margin',
        'images',
        'banner',
        'supplier_id',
        'supplier_product_id',
        'supplier_link',
        'supplier_store_link',
        'status',
        'variants',
        'has_promotion',
        'promo_price',
        'promo_start_date',
        'promo_end_date',
    ];

    protected $casts = [
        // 'images' => 'array', // Désactivé car on utilise un accessor personnalisé pour PostgreSQL TEXT[]
        'supplier_price' => 'float',
        'selling_price' => 'float',
        'margin' => 'float',
        'variants' => 'array',
        'has_promotion' => 'boolean',
        'promo_price' => 'decimal:2',
        'promo_start_date' => 'datetime',
        'promo_end_date' => 'datetime',
    ];

    // Relations
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function reviews()
    {
        // Pour PostgreSQL, utiliser whereRaw pour comparer correctement le boolean
        if (config('database.default') === 'pgsql') {
            return $this->hasMany(ProductReview::class)
                ->whereRaw('is_approved::boolean = true')
                ->orderBy('created_at', 'desc');
        } else {
            return $this->hasMany(ProductReview::class)
                ->where('is_approved', true)
                ->orderBy('created_at', 'desc');
        }
    }

    /**
     * Relation avec ProductImage (table séparée - APPROCHE PRO)
     */
    public function productImages()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order', 'asc');
    }

    /**
     * Accessor pour compatibilité: retourner les images depuis product_images
     * (remplace l'ancien accessor qui utilisait la colonne images TEXT[])
     */
    public function getImagesAttribute($value)
    {
        // ✅ APPROCHE PRO: Utiliser la table product_images
        // Si la relation est déjà chargée, l'utiliser (plus performant)
        if ($this->relationLoaded('productImages')) {
            return $this->productImages->pluck('image_url')->filter()->values()->toArray();
        }
        
        // Sinon, charger depuis la relation (requête DB)
        return $this->productImages()->pluck('image_url')->filter()->values()->toArray();
    }

    /**
     * Préparer les attributs avant l'insertion/mise à jour
     * Note: Les images sont maintenant gérées via la table product_images (approche PRO)
     * On garde juste la conversion de has_promotion pour PostgreSQL
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($product) {
            // Forcer has_promotion à être un boolean pour PostgreSQL
            if (array_key_exists('has_promotion', $product->getAttributes())) {
                $hasPromotionValue = $product->getAttributes()['has_promotion'];
                // Convertir en boolean strict
                if (is_string($hasPromotionValue)) {
                    $hasPromotionValue = in_array(strtolower($hasPromotionValue), ['1', 'true', 'on', 'yes']);
                } elseif (is_int($hasPromotionValue)) {
                    $hasPromotionValue = (bool) $hasPromotionValue;
                } elseif (!is_bool($hasPromotionValue)) {
                    $hasPromotionValue = (bool) $hasPromotionValue;
                }
                $product->setAttribute('has_promotion', $hasPromotionValue);
            }
        });
    }
}

