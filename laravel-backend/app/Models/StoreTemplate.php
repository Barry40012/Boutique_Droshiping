<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'preview_image',
        'category',
        'layout_config',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'layout_config' => 'array',
        'is_active' => 'boolean',
    ];

    // S'assurer que is_active est toujours traité comme boolean
    protected $attributes = [
        'is_active' => true,
    ];

    // Override pour s'assurer que is_active est toujours boolean lors de la sauvegarde
    public function setAttribute($key, $value)
    {
        if ($key === 'is_active') {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($value === null) {
                $value = (bool) $value;
            }
        }
        return parent::setAttribute($key, $value);
    }

    // Relations
    public function stores()
    {
        return $this->hasMany(Store::class, 'template_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        // Forcer le cast boolean pour PostgreSQL
        // Utiliser ::boolean pour convertir explicitement
        if (config('database.default') === 'pgsql') {
            return $query->whereRaw('is_active::boolean = true');
        }
        return $query->where('is_active', true);
    }

    public function scopeFree($query)
    {
        return $query->where('category', 'free');
    }

    public function scopePro($query)
    {
        return $query->where('category', 'pro');
    }

    public function scopePremium($query)
    {
        return $query->where('category', 'premium');
    }
}
