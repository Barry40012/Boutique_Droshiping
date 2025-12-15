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

    protected $casts = [
        // 'images' => 'array', // Désactivé car on utilise un accessor personnalisé pour PostgreSQL TEXT[]
        'supplier_price' => 'float',
        'selling_price' => 'float',
        'margin' => 'float',
    ];

    /**
     * Accessor pour convertir PostgreSQL TEXT[] en tableau PHP
     */
    public function getImagesAttribute($value)
    {
        // Récupérer la valeur brute depuis les attributs
        // Laravel peut passer la valeur directement ou elle peut être dans $this->attributes
        $rawValue = $value;
        
        // Si $value est null, essayer de récupérer depuis les attributs
        if ($rawValue === null && array_key_exists('images', $this->attributes)) {
            $rawValue = $this->attributes['images'];
        }
        
        // Si toujours null ou vide, retourner un tableau vide
        if ($rawValue === null || $rawValue === '' || $rawValue === '{}') {
            return [];
        }
        
        // Si c'est déjà un tableau, le retourner tel quel (filtré)
        if (is_array($rawValue)) {
            return array_values(array_filter($rawValue, function($img) {
                return !empty($img) && is_string($img);
            }));
        }
        
        // Si c'est une chaîne, essayer de la parser
        if (is_string($rawValue)) {
            // Format PostgreSQL TEXT[]: {val1,val2} ou {"val1","val2"}
            if (preg_match('/^\{.*\}$/', $rawValue)) {
                // Enlever les accolades
                $content = trim($rawValue, '{}');
                if (empty($content)) {
                    return [];
                }
                
                // Séparer par virgule (en faisant attention aux virgules dans les URLs)
                // Utiliser une regex pour séparer correctement
                preg_match_all('/"((?:[^"\\\\]|\\\\.)*)"|([^,]+)/', $content, $matches);
                $items = [];
                foreach ($matches[0] as $match) {
                    if (!empty(trim($match))) {
                        $items[] = trim($match, '"');
                    }
                }
                
                // Si la méthode regex n'a pas fonctionné, utiliser explode simple
                if (empty($items)) {
                    $items = explode(',', $content);
                    $items = array_map(function($item) {
                        $item = trim($item);
                        $item = trim($item, '"');
                        $item = str_replace('\\"', '"', $item);
                        $item = str_replace('\\\\', '\\', $item);
                        return $item;
                    }, $items);
                }
                
                // Filtrer les valeurs vides
                return array_values(array_filter($items, function($img) {
                    return !empty($img) && is_string($img);
                }));
            }
            
            // Si c'est du JSON (fallback)
            if (($decoded = json_decode($rawValue, true)) !== null && is_array($decoded)) {
                return array_values(array_filter($decoded, function($img) {
                    return !empty($img) && is_string($img);
                }));
            }
        }
        
        return [];
    }

    /**
     * Préparer les attributs avant l'insertion/mise à jour
     * Pour PostgreSQL TEXT[], on doit convertir le tableau PHP en format PostgreSQL
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($product) {
            // Convertir le tableau PHP en format PostgreSQL TEXT[]
            // Format PostgreSQL: {} pour vide, {val1,val2} pour tableau avec valeurs
            
            // Récupérer la valeur depuis les attributs si elle existe
            $imagesValue = $product->getAttributes()['images'] ?? $product->images ?? null;
            
            // Debug
            \Log::info('Product saving - Images conversion', [
                'product_id' => $product->id ?? 'new',
                'images_type' => gettype($imagesValue),
                'images_value' => is_array($imagesValue) ? $imagesValue : (is_string($imagesValue) ? substr($imagesValue, 0, 200) : $imagesValue),
                'is_array' => is_array($imagesValue),
                'is_empty' => empty($imagesValue)
            ]);
            
            if (is_array($imagesValue)) {
                if (empty($imagesValue)) {
                    $product->setAttribute('images', '{}');
                } else {
                    // Échapper les valeurs et créer le format PostgreSQL
                    $escaped = array_map(function($val) {
                        // Échapper les guillemets et backslashes pour PostgreSQL
                        $val = str_replace('\\', '\\\\', $val);
                        $val = str_replace('"', '\\"', $val);
                        return '"' . $val . '"';
                    }, $imagesValue);
                    $product->setAttribute('images', '{' . implode(',', $escaped) . '}');
                }
            } elseif (empty($imagesValue) || $imagesValue === '[]' || $imagesValue === 'null' || $imagesValue === null) {
                $product->setAttribute('images', '{}');
            } elseif (is_string($imagesValue) && strpos($imagesValue, '{') !== 0) {
                // Si c'est une chaîne qui n'est pas déjà au format PostgreSQL, la convertir
                // (ne devrait pas arriver, mais au cas où)
                $product->setAttribute('images', $imagesValue);
            }
        });
    }
}

