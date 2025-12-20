<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'product_images';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'product_id',
        'image_path',
        'image_url',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Relation avec Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Accessor pour obtenir l'URL complète de l'image
     */
    public function getFullUrlAttribute()
    {
        if ($this->image_url) {
            return $this->image_url;
        }
        
        if ($this->image_path) {
            return \Storage::url($this->image_path);
        }
        
        return null;
    }
}

