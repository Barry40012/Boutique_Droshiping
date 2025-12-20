@extends('layouts.app')

@section('title', 'Éditer le produit - Merchant')

@section('content')
<style>
.merchant-create-store-container {
    max-width: 100%;
    width: 100%;
    padding: 2rem;
}

.create-store-card {
    max-width: 100%;
    width: 100%;
}

.create-store-form {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.form-section {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.form-section-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.form-section-two-columns {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

@media (max-width: 768px) {
    .form-section-two-columns {
        grid-template-columns: 1fr;
    }
}

.pricing-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}

@media (max-width: 1024px) {
    .pricing-grid {
        grid-template-columns: 1fr;
    }
}

.margin-display {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.margin-percentage {
    font-size: 1.1rem;
    font-weight: 700;
    padding: 0.5rem 1rem;
    background: #f3f4f6;
    border-radius: 8px;
    min-width: 80px;
    text-align: center;
}

.image-counter {
    font-size: 0.9em;
    font-weight: 600;
    margin-left: 10px;
    color: #10b981;
}
.file-count-badge, .url-count-badge {
    font-size: 0.85em;
    font-weight: 500;
    color: #6b7280;
    margin-left: 5px;
}
.image-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 15px;
    margin-top: 15px;
}
.image-preview-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid #e5e7eb;
}
.image-preview-item img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    display: block;
}
.remove-preview-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(239, 68, 68, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: all 0.2s;
}
.remove-preview-btn:hover {
    background: #ef4444;
    transform: scale(1.1);
}

.existing-images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.existing-image-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid #e5e7eb;
}

.existing-image-item img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    display: block;
}

.remove-image-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(239, 68, 68, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: all 0.2s;
}

.remove-image-btn:hover {
    background: #ef4444;
    transform: scale(1.1);
}

#promotion_fields {
    margin-top: 1rem;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

#promotion_fields .form-group {
    margin-bottom: 1rem;
}

#promotion_fields .form-group:last-child {
    margin-bottom: 0;
}

.char-counter {
    font-size: 0.875rem;
    font-weight: 500;
    color: #6b7280;
    margin-left: 0.5rem;
    float: right;
}

.form-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
</style>
<div class="merchant-create-store-container">
    <div class="create-store-card" data-aos="fade-up">
        <div class="create-store-header">
            <h1 class="create-store-title">
                <i class="fas fa-edit icon-inline"></i>
                Éditer le produit
            </h1>
            <p class="create-store-subtitle">Modifiez les informations du produit</p>
        </div>

        @if(session('success'))
            <div class="alert-success" data-aos="fade-up">
                <i class="fas fa-check-circle icon-inline"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->has('error'))
            <div class="alert-error" data-aos="fade-up">
                <i class="fas fa-exclamation-circle icon-inline"></i>
                {{ $errors->first('error') }}
            </div>
        @endif

        @if($errors->any() && !$errors->has('error'))
            <div class="alert-error" data-aos="fade-up">
                <i class="fas fa-exclamation-circle icon-inline"></i>
                <strong>Erreurs détectées :</strong>
                <ul style="margin-top: 10px; margin-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

            @php
                $variants = $product->variants ?? [];
                $variantSizes = old('variant_sizes', isset($variants['sizes']) ? implode(', ', $variants['sizes']) : '');
                $variantColors = old('variant_colors', isset($variants['colors']) ? implode(', ', $variants['colors']) : '');
                $variantLengths = old('variant_lengths', isset($variants['lengths']) ? implode(', ', $variants['lengths']) : '');
                $hasPromotion = old('has_promotion', $product->has_promotion ?? false);
                // Calculer le pourcentage de réduction si promotion active
                $promoPercentage = null;
                if ($product->has_promotion && $product->promo_price && $product->selling_price) {
                    $discount = $product->selling_price - $product->promo_price;
                    $promoPercentage = round(($discount / $product->selling_price) * 100, 1);
                }
                $promoPercentage = old('promo_percentage', $promoPercentage);
                $promoDurationDays = old('promo_duration_days', $product->promo_start_date && $product->promo_end_date ? $product->promo_start_date->diffInDays($product->promo_end_date) : 7);
                // ✅ Récupérer les images depuis la table product_images (APPROCHE PRO)
                $existingImages = [];
                try {
                    // Charger la relation si elle n'est pas déjà chargée
                    if (!$product->relationLoaded('productImages')) {
                        $product->load('productImages');
                    }
                    
                    // Debug: vérifier combien d'images sont dans la relation
                    \Log::info('Product Edit - productImages relation check', [
                        'product_id' => $product->id ?? 'N/A',
                        'productImages_count' => $product->productImages->count(),
                        'productImages_raw' => $product->productImages->toArray(),
                        'productImages_urls' => $product->productImages->pluck('image_url')->toArray()
                    ]);
                    
                    // Récupérer les images depuis la relation
                    $existingImages = $product->productImages
                        ->sortBy('sort_order')
                        ->pluck('image_url')
                        ->filter(function($url) {
                            return !empty($url) && is_string($url);
                        })
                        ->values()
                        ->toArray();
                    
                    // Si aucune image dans product_images, essayer de récupérer depuis l'ancienne colonne (migration)
                    if (empty($existingImages)) {
                        // Fallback: utiliser l'accessor qui peut encore lire l'ancienne colonne
                        $legacyImages = $product->getAttributes()['images'] ?? null;
                        if ($legacyImages && $legacyImages !== '{}') {
                            // Parser le format PostgreSQL TEXT[] si nécessaire
                            if (is_string($legacyImages) && preg_match('/^\{.*\}$/', $legacyImages)) {
                                $content = trim($legacyImages, '{}');
                                if (!empty($content)) {
                                    $items = explode(',', $content);
                                    $existingImages = array_map(function($item) {
                                        $item = trim($item);
                                        $item = trim($item, '"');
                                        $item = str_replace('\\"', '"', $item);
                                        $item = str_replace('\\\\', '\\', $item);
                                        return $item;
                                    }, $items);
                                    $existingImages = array_values(array_filter($existingImages, function($img) {
                                        return !empty($img) && is_string($img);
                                    }));
                                }
                            } elseif (is_array($legacyImages)) {
                                $existingImages = array_values(array_filter($legacyImages, function($img) {
                                    return !empty($img) && is_string($img);
                                }));
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Erreur récupération images produit (edit): ' . $e->getMessage());
                    $existingImages = [];
                }
                
                // Pour le textarea, on utilise old() si présent, sinon les images existantes
                $existingImagesText = old('images', !empty($existingImages) ? implode(', ', $existingImages) : '');
                $existingImagesCount = count($existingImages);
                
                \Log::info('Product Edit - Images récupérées', [
                    'product_id' => $product->id ?? 'N/A',
                    'existing_images_count' => $existingImagesCount,
                    'existing_images' => $existingImages,
                    'relation_loaded' => $product->relationLoaded('productImages'),
                    'product_images_count' => $product->relationLoaded('productImages') ? $product->productImages->count() : 0,
                    'textarea_value_length' => strlen($existingImagesText)
                ]);
            @endphp

        <form action="{{ route('merchant.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="create-store-form" id="productEditForm">
            @csrf
            
            {{-- Champ hidden pour envoyer les images existantes au backend --}}
            <input type="hidden" name="existing_images" id="existingImagesInput" value="{{ json_encode($existingImages) }}" data-initial="{{ json_encode($existingImages) }}">

            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-info-circle icon-inline"></i>
                    Informations
                </h2>
                <div class="form-section-two-columns">
                    <div class="form-group">
                        <label class="form-label">Nom</label>
                        <label class="form-label">
                            Nom du produit
                            <span class="char-counter" id="nameCharCounter">{{ strlen(old('name', $product->name)) }}/100</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="product_name"
                               class="form-input @error('name') error @enderror" 
                               value="{{ old('name', $product->name) }}" 
                               maxlength="100"
                               required
                               oninput="updateCharCounter('product_name', 'nameCharCounter', 100)">
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                        <div class="form-hint" style="margin-top: 0.25rem; font-size: 0.875rem; color: #6b7280;">
                            <i class="fas fa-info-circle icon-inline"></i>
                            Maximum 100 caractères
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-toggle-on icon-inline"></i>
                            Statut
                        </label>
                        <select name="status" class="form-input @error('status') error @enderror" required>
                            <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Inactif</option>
                        </select>
                        @error('status') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-align-left icon-inline"></i>
                        Description
                    </label>
                    <textarea name="description" 
                              id="product-description" 
                              rows="6" 
                              class="form-textarea @error('description') error @enderror" 
                              placeholder="Décrivez votre produit en détail...">{{ old('description', $product->description) }}</textarea>
                    <div class="form-hint">
                        <i class="fas fa-info-circle icon-inline"></i>
                        Décrivez les caractéristiques, avantages et détails de votre produit. Plus la description est complète, mieux c'est !
                    </div>
                    @error('description') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-dollar-sign icon-inline"></i>
                    Tarification
                </h2>
                <div class="pricing-grid">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-truck icon-inline"></i>
                            Prix fournisseur ($)
                        </label>
                        <input type="number" 
                               step="0.01" 
                               id="supplier_price" 
                               name="supplier_price" 
                               class="form-input @error('supplier_price') error @enderror" 
                               value="{{ old('supplier_price', $product->supplier_price) }}" 
                               oninput="calculateMargin()"
                               required>
                        @error('supplier_price') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-dollar-sign icon-inline"></i>
                            Prix de vente ($)
                        </label>
                        <input type="number" 
                               step="0.01" 
                               id="selling_price" 
                               name="selling_price" 
                               class="form-input @error('selling_price') error @enderror" 
                               value="{{ old('selling_price', $product->selling_price) }}" 
                               oninput="calculateMargin()"
                               required>
                        @error('selling_price') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-chart-line icon-inline"></i>
                            Marge ($)
                        </label>
                        <div class="margin-display">
                            <input type="text" 
                                   id="margin_display" 
                                   class="form-input" 
                                   value="$0.00" 
                                   readonly 
                                   style="background: #f3f4f6; font-weight: 600;">
                            <div class="margin-percentage" id="margin_percentage">0%</div>
                        </div>
                        <div class="form-hint">
                            <i class="fas fa-calculator icon-inline"></i>
                            Marge = Prix de vente - Prix fournisseur
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-link icon-inline"></i>
                    Fournisseur & bannière produit
                </h2>
                <div class="form-group">
                    <label class="form-label">
                        Lien fournisseur (URL)
                    </label>
                    <input type="url"
                           name="supplier_link"
                           class="form-input @error('supplier_link') error @enderror"
                           value="{{ old('supplier_link', $product->supplier_link) }}"
                           placeholder="https://fournisseur.com/produit/123">
                    @error('supplier_link') <div class="form-error">{{ $message }}</div> @enderror
                    <p class="form-hint">Permet d'envoyer la commande directement au fournisseur (dropshipping).</p>
                    <div class="import-helper">
                        <button type="button" class="btn-secondary" onclick="alert('Import automatique non activé : nécessite une API ou un scraper. Nous pourrons le brancher plus tard.')">
                            <i class="fas fa-download icon-inline"></i> Importer depuis le lien (stub)
                        </button>
                        <p class="form-hint">Pour un import auto (images/prix/titre), il faut une API ou un scraper. Cette action est prête à être branchée plus tard.</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        ID produit fournisseur (optionnel)
                    </label>
                    <input type="text"
                           name="supplier_product_id"
                           class="form-input @error('supplier_product_id') error @enderror"
                           value="{{ old('supplier_product_id', $product->supplier_product_id) }}"
                           placeholder="SKU / ID fournisseur">
                    @error('supplier_product_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">
                        Bannière produit (URL ou image uploadée)
                    </label>
                    <input type="text"
                           name="banner"
                           class="form-input @error('banner') error @enderror"
                           value="{{ old('banner', $product->banner) }}"
                           placeholder="https://exemple.com/image-banner.jpg">
                    @error('banner') <div class="form-error">{{ $message }}</div> @enderror
                    <p class="form-hint">Utilisée dans les thèmes qui mettent le produit en avant (hero).</p>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        Lien du store fournisseur (optionnel)
                    </label>
                    <input type="url"
                           name="supplier_store_link"
                           class="form-input @error('supplier_store_link') error @enderror"
                           value="{{ old('supplier_store_link', $product->supplier_store_link) }}"
                           placeholder="https://fournisseur.com/boutique">
                    @error('supplier_store_link') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-image icon-inline"></i>
                    Images <span id="imageCounter" class="image-counter">(0/6)</span>
                </h2>
                <p class="form-hint" style="margin-bottom: 20px;">
                    <i class="fas fa-info-circle icon-inline"></i>
                    Vous pouvez utiliser les deux options : uploader des fichiers ET/OU ajouter des URLs. Maximum 6 images au total.
                </p>
                
                <!-- Images existantes -->
                @if(!empty($existingImages) && count($existingImages) > 0)
                <div class="form-group">
                    <label class="form-label">Images actuelles</label>
                    <div class="existing-images-grid" id="existingImagesGrid">
                        @foreach($existingImages as $img)
                        <div class="existing-image-item" data-url="{{ $img }}">
                            <img src="{{ $img }}" alt="Image produit">
                            <button type="button" class="remove-image-btn" data-url="{{ $img }}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Upload de nouveaux fichiers -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-upload icon-inline"></i>
                        Uploader des fichiers images
                        <span id="fileCount" class="file-count-badge">(0 fichier(s))</span>
                    </label>
                    <div class="image-upload-area" id="imageUploadArea">
                        <input type="file" 
                               name="image_files[]" 
                               id="imageFiles" 
                               multiple 
                               accept="image/*"
                               class="image-upload-input">
                        <div class="upload-placeholder">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Glissez-déposez des images ou cliquez pour sélectionner</p>
                            <span class="upload-hint">JPG, PNG, GIF, WEBP (max 5MB par fichier)</span>
                        </div>
                    </div>
                    <div class="image-preview-grid" id="imagePreviewGrid"></div>
                    @error('image_files')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                    @error('image_files.*')
                        <div class="form-error">
                            <strong>Erreur d'image :</strong> {{ $message }}
                            <br><small>Assurez-vous de sélectionner uniquement des fichiers images valides (JPG, PNG, GIF, WEBP) de moins de 5MB.</small>
                        </div>
                    @enderror
                </div>

                <!-- URLs images (alternative) -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-link icon-inline"></i>
                        Ou ajouter des URLs d'images
                        <span id="urlCount" class="url-count-badge">(0 URL(s))</span>
                    </label>
                    <textarea name="images" 
                              id="imageUrls"
                              rows="3" 
                              class="form-textarea @error('images') error @enderror" 
                              placeholder="https://exemple.com/image1.jpg, https://exemple.com/image2.jpg (séparées par des virgules)">{{ $existingImagesText }}</textarea>
                    @if(config('app.debug'))
                        <small style="color: #666; font-size: 0.8rem; margin-top: 0.5rem; display: block;">
                            Debug: {{ count($existingImages) }} image(s) existante(s) | Textarea value length: {{ strlen($existingImagesText) }}
                        </small>
                    @endif
                    <div class="form-hint">
                        <i class="fas fa-lightbulb icon-inline"></i>
                        Séparez les URLs par des virgules. Vous pouvez combiner avec les fichiers uploadés ci-dessus.
                    </div>
                    @error('images') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-palette icon-inline"></i>
                    Variantes du produit (optionnel)
                </h2>
                <p class="form-hint" style="margin-bottom: 20px;">
                    <i class="fas fa-info-circle icon-inline"></i>
                    Ajoutez les variantes disponibles pour ce produit (taille, couleur, longueur, etc.). Laissez vide si le produit n'a pas de variantes.
                </p>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-ruler icon-inline"></i>
                        Tailles disponibles (séparées par des virgules)
                    </label>
                    <input type="text"
                           name="variant_sizes"
                           id="variant_sizes"
                           class="form-input @error('variant_sizes') error @enderror"
                           value="{{ $variantSizes }}"
                           placeholder="Ex: S, M, L, XL ou 38, 39, 40, 41">
                    @error('variant_sizes') <div class="form-error">{{ $message }}</div> @enderror
                    <p class="form-hint">Laissez vide si le produit n'a pas de tailles</p>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-paint-brush icon-inline"></i>
                        Couleurs disponibles (séparées par des virgules)
                    </label>
                    <input type="text"
                           name="variant_colors"
                           id="variant_colors"
                           class="form-input @error('variant_colors') error @enderror"
                           value="{{ $variantColors }}"
                           placeholder="Ex: Rouge, Bleu, Vert, Noir">
                    @error('variant_colors') <div class="form-error">{{ $message }}</div> @enderror
                    <p class="form-hint">Laissez vide si le produit n'a qu'une seule couleur</p>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-ruler-vertical icon-inline"></i>
                        Longueurs/Mesures disponibles (séparées par des virgules)
                    </label>
                    <input type="text"
                           name="variant_lengths"
                           id="variant_lengths"
                           class="form-input @error('variant_lengths') error @enderror"
                           value="{{ $variantLengths }}"
                           placeholder="Ex: 1 mètre, 2 mètres, 3 mètres">
                    @error('variant_lengths') <div class="form-error">{{ $message }}</div> @enderror
                    <p class="form-hint">Laissez vide si le produit n'a pas de variantes de longueur</p>
                </div>
            </div>

            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-tag icon-inline"></i>
                    Promotion (optionnel)
                </h2>
                <div class="form-group">
                    <label class="form-label" style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox"
                               name="has_promotion"
                               id="has_promotion"
                               value="1"
                               {{ $hasPromotion ? 'checked' : '' }}
                               onchange="togglePromotionFields()">
                        <span>Activer une promotion pour ce produit</span>
                    </label>
                </div>

                <div id="promotion_fields" style="display: {{ $hasPromotion ? 'block' : 'none' }};">
                    <div class="form-section-two-columns">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-percent icon-inline"></i>
                                Pourcentage de réduction (%)
                            </label>
                            <input type="number"
                                   step="0.1"
                                   min="1"
                                   max="99"
                                   name="promo_percentage"
                                   id="promo_percentage"
                                   class="form-input @error('promo_percentage') error @enderror"
                                   value="{{ $promoPercentage }}"
                                   placeholder="Ex: 10"
                                   oninput="calculatePromoPrice()">
                            @error('promo_percentage') <div class="form-error">{{ $message }}</div> @enderror
                            <p class="form-hint">Le pourcentage de réduction (ex: 10 pour 10% de réduction)</p>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-calendar-alt icon-inline"></i>
                                Durée de la promotion (en jours)
                            </label>
                            <input type="number"
                                   min="1"
                                   name="promo_duration_days"
                                   id="promo_duration_days"
                                   class="form-input @error('promo_duration_days') error @enderror"
                                   value="{{ $promoDurationDays }}"
                                   placeholder="Ex: 7">
                            @error('promo_duration_days') <div class="form-error">{{ $message }}</div> @enderror
                            <p class="form-hint">La promotion commencera dès l'activation et durera le nombre de jours indiqué</p>
                        </div>
                    </div>
                    
                    <div class="promo-preview" id="promoPreview" style="margin-top: 1rem; padding: 1rem; background: #f0fdf4; border-radius: 8px; display: {{ $promoPercentage ? 'block' : 'none' }}; border: 1px solid #86efac;">
                        <small style="color: #166534; font-size: 0.95rem;">
                            <i class="fas fa-info-circle"></i>
                            <strong>Aperçu de la promotion :</strong> Prix original: <span id="originalPriceDisplay">${{ number_format($product->selling_price, 2) }}</span> → 
                            Prix promo: <strong id="promoPriceDisplay">${{ $promoPercentage ? number_format($product->selling_price - ($product->selling_price * $promoPercentage / 100), 2) : '0.00' }}</strong> 
                            (<span id="savingsDisplay">${{ $promoPercentage ? number_format($product->selling_price * $promoPercentage / 100, 2) : '0.00' }}</span> d'économie)
                        </small>
                    </div>
                    
                    <!-- Champ caché pour le prix promo calculé -->
                    <input type="hidden" name="promo_price" id="promo_price_calculated" value="{{ $product->promo_price ?? '' }}">
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('merchant.products') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left icon-inline"></i> Retour
                </a>
                <button type="submit" class="btn-primary btn-large">
                    <i class="fas fa-save icon-inline"></i>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init();

// Gestion upload images
const imageUploadArea = document.getElementById('imageUploadArea');
const imageFilesInput = document.getElementById('imageFiles');
const imagePreviewGrid = document.getElementById('imagePreviewGrid');
let uploadedImages = [];
let removedExistingImages = [];

if (imageUploadArea) {
    imageUploadArea.addEventListener('click', () => {
        const totalImages = countTotalImages();
        // Bloquer si on a déjà 6 images ou plus
        if (totalImages >= 6) {
            alert('❌ Maximum de 6 images atteint ! Supprimez des images existantes avant d\'en ajouter de nouvelles.');
            return;
        }
        if (imageFilesInput) imageFilesInput.click();
    });
    imageUploadArea.addEventListener('dragover', (e) => {
        const totalImages = countTotalImages();
        // Bloquer si on a déjà 6 images ou plus
        if (totalImages >= 6) {
            e.preventDefault();
            return;
        }
        e.preventDefault();
        imageUploadArea.classList.add('dragover');
    });
    imageUploadArea.addEventListener('dragleave', () => {
        imageUploadArea.classList.remove('dragover');
    });
    imageUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        imageUploadArea.classList.remove('dragover');
        const totalImages = countTotalImages();
        // Bloquer si on a déjà 6 images ou plus
        if (totalImages >= 6) {
            alert('❌ Maximum de 6 images atteint ! Supprimez des images existantes avant d\'en ajouter de nouvelles.');
            return;
        }
        if (e.dataTransfer.files.length > 0) {
            handleFiles(e.dataTransfer.files);
        }
    });
}

if (imageFilesInput) {
    imageFilesInput.addEventListener('change', (e) => {
        const totalImages = countTotalImages();
        // Bloquer si on a déjà 6 images ou plus
        if (totalImages >= 6) {
            alert('❌ Maximum de 6 images atteint ! Supprimez des images existantes avant d\'en ajouter de nouvelles.');
            e.target.value = ''; // Réinitialiser l'input
            return;
        }
        if (e.target.files.length > 0) {
            handleFiles(e.target.files);
        }
    });
}

function handleFiles(files) {
    // Compter seulement les fichiers uploadés + URLs (les existantes sont dans les URLs)
    const currentTotal = countTotalImages();
    
    console.log('handleFiles called:', {
        currentTotal: currentTotal,
        filesCount: files.length,
        uploadedImagesCount: uploadedImages.length,
        urlCount: countImageUrls()
    });
    
    // Permettre jusqu'à 6 images inclus (bloquer seulement si on dépasse 6)
    // Les validations dans les event listeners (click, drop, change) vérifient déjà totalImages >= 6
    // Ici, on calcule combien d'images on peut encore ajouter
    if (currentTotal >= 6) {
        alert('❌ Maximum de 6 images atteint ! Supprimez des images existantes avant d\'en ajouter de nouvelles.');
        return;
    }
    
    // Calculer combien d'images on peut encore ajouter (jusqu'à 6 inclus)
    const maxToAdd = 6 - currentTotal;
    
    console.log('handleFiles: maxToAdd =', maxToAdd, 'files.length =', files.length);
    
    if (maxToAdd <= 0) {
        alert('❌ Maximum de 6 images atteint ! Supprimez des images existantes avant d\'en ajouter de nouvelles.');
        return;
    }
    
    if (files.length > maxToAdd) {
        alert(`⚠️ Attention : Vous ne pouvez ajouter que ${maxToAdd} image${maxToAdd > 1 ? 's' : ''} supplémentaire${maxToAdd > 1 ? 's' : ''}. Seulement ${maxToAdd} image${maxToAdd > 1 ? 's' : ''} sera${maxToAdd > 1 ? 'ont' : ''} ajoutée${maxToAdd > 1 ? 's' : ''}.`);
    }
    
    Array.from(files).slice(0, maxToAdd).forEach(file => {
        if (file.type.startsWith('image/')) {
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert(`Le fichier "${file.name}" n'est pas un format d'image valide.`);
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert(`Le fichier "${file.name}" est trop volumineux (max 5MB).`);
                return;
            }
            
            const reader = new FileReader();
            reader.onload = (e) => {
                uploadedImages.push({
                    file: file,
                    preview: e.target.result
                });
                console.log('Image added to uploadedImages. New count:', uploadedImages.length);
                updatePreview();
                // Forcer la mise à jour du compteur après un court délai pour s'assurer que tout est synchronisé
                setTimeout(() => {
                    updateImageCounters();
                    const totalCounter = document.getElementById('imageCounter');
                    if (totalCounter) {
                        const expectedTotal = countTotalImages();
                        console.log('Counter after update:', totalCounter.textContent, 'Expected:', `(${expectedTotal}/6)`);
                        if (totalCounter.textContent !== `(${expectedTotal}/6)`) {
                            console.warn('Counter mismatch! Forcing update...');
                            totalCounter.textContent = `(${expectedTotal}/6)`;
                        }
                    }
                }, 100);
            };
            reader.readAsDataURL(file);
        }
    });
}

function countImageUrls() {
    const urlsTextarea = document.getElementById('imageUrls');
    if (!urlsTextarea || !urlsTextarea.value.trim()) {
        return 0;
    }
    
    const urls = urlsTextarea.value.split(',').map(u => u.trim()).filter(u => u);
    return urls.length;
}

/**
 * ✅ COMPTE TOTAL CORRECT : Images existantes + Nouvelles URLs + Fichiers uploadés
 * C'est LA fonction à utiliser partout pour compter les images
 */
function countTotalImages() {
    // 1. Compter les images existantes NON supprimées (visibles dans la grille)
    // ✅ DÉDUPLIQUER : Compter seulement les URLs uniques
    const existingGrid = document.getElementById('existingImagesGrid');
    let existingCount = 0;
    if (existingGrid) {
        const visibleItems = Array.from(existingGrid.querySelectorAll('.existing-image-item'))
            .filter(item => item.style.display !== 'none');
        // Dédupliquer les URLs pour éviter de compter les doublons
        const uniqueUrls = [...new Set(visibleItems.map(item => item.dataset.url))];
        existingCount = uniqueUrls.length;
    }
    
    // 2. Compter les nouveaux fichiers uploadés
    const uploadedCount = uploadedImages.length;
    
    // 3. Compter les NOUVELLES URLs ajoutées (qui ne sont pas dans les existantes)
    const urlsTextarea = document.getElementById('imageUrls');
    let newUrlsCount = 0;
    if (urlsTextarea && urlsTextarea.value.trim()) {
        const existingImagesInput = document.getElementById('existingImagesInput');
        const initialExistingImages = existingImagesInput 
            ? JSON.parse(existingImagesInput.getAttribute('data-initial') || existingImagesInput.value || '[]')
            : [];
        
        // URLs actuelles dans le textarea (dédupliquées)
        const currentUrls = [...new Set(urlsTextarea.value.split(',').map(u => u.trim()).filter(u => u))];
        
        // Compter seulement les URLs qui ne sont PAS dans les existantes initiales
        newUrlsCount = currentUrls.filter(url => !initialExistingImages.includes(url)).length;
    }
    
    const total = existingCount + uploadedCount + newUrlsCount;
    
    console.log('countTotalImages:', {
        existing: existingCount,
        uploaded: uploadedCount,
        newUrls: newUrlsCount,
        total: total
    });
    
    return total;
}

function countExistingImages() {
    const existingGrid = document.getElementById('existingImagesGrid');
    if (!existingGrid) return 0;
    // Compter uniquement les images qui ne sont pas masquées (non supprimées)
    const visibleItems = Array.from(existingGrid.querySelectorAll('.existing-image-item')).filter(item => {
        return item.style.display !== 'none' && !removedExistingImages.includes(item.dataset.url);
    });
    return visibleItems.length;
}

function updatePreview() {
    if (!imagePreviewGrid) return;
    imagePreviewGrid.innerHTML = '';
    uploadedImages.forEach((img, index) => {
        const div = document.createElement('div');
        div.className = 'image-preview-item';
        div.innerHTML = `
            <img src="${img.preview}" alt="Preview">
            <button type="button" class="remove-preview-btn" data-index="${index}">
                <i class="fas fa-times"></i>
            </button>
        `;
        imagePreviewGrid.appendChild(div);
    });
    
    if (imageFilesInput) {
        const dt = new DataTransfer();
        uploadedImages.forEach(img => dt.items.add(img.file));
        imageFilesInput.files = dt.files;
    }
}

function updateImageCounters() {
    // ✅ Utiliser la fonction de comptage unique et correcte
    const totalCount = countTotalImages();
    
    // Compter séparément pour l'affichage des badges
    const fileCount = uploadedImages.length;
    const urlCount = countImageUrls();
    
    console.log('updateImageCounters:', {
        fileCount: fileCount,
        urlCount: urlCount,
        totalCount: totalCount,
        uploadedImages: uploadedImages.length
    });
    
    // Mettre à jour les badges
    const fileCountBadge = document.getElementById('fileCount');
    const urlCountBadge = document.getElementById('urlCount');
    const totalCounter = document.getElementById('imageCounter');
    
    if (fileCountBadge) {
        fileCountBadge.textContent = `(${fileCount} fichier${fileCount > 1 ? 's' : ''})`;
    }
    if (urlCountBadge) {
        urlCountBadge.textContent = `(${urlCount} URL${urlCount > 1 ? 's' : ''})`;
    }
    if (totalCounter) {
        // Afficher le total (fichiers + URLs du textarea qui inclut les existantes)
        const oldText = totalCounter.textContent;
        totalCounter.textContent = `(${totalCount}/6)`;
        console.log('Counter updated:', oldText, '->', totalCounter.textContent, '| totalCount:', totalCount);
        
        // Changer la couleur selon le nombre
        // Rouge si on a atteint ou dépassé 6, orange si entre 3 et 5, vert si moins de 3
        if (totalCount > 6) {
            totalCounter.style.color = '#ef4444'; // Dépasse le maximum
        } else if (totalCount === 6) {
            totalCounter.style.color = '#f59e0b'; // Maximum atteint mais OK
        } else if (totalCount >= 3) {
            totalCounter.style.color = '#f59e0b'; // Orange
        } else {
            totalCounter.style.color = '#10b981'; // Vert
        }
    } else {
        console.error('totalCounter element not found!');
    }
    
    // Désactiver/activer l'upload et le textarea selon le nombre d'images
    // Désactiver quand on a atteint ou dépassé 6 images
    const isMaxReached = totalCount >= 6;
    
    if (imageUploadArea) {
        if (isMaxReached) {
            imageUploadArea.style.opacity = '0.5';
            imageUploadArea.style.pointerEvents = 'none';
            imageUploadArea.style.cursor = 'not-allowed';
            imageUploadArea.title = 'Maximum de 6 images atteint. Supprimez des images pour en ajouter.';
        } else {
            imageUploadArea.style.opacity = '1';
            imageUploadArea.style.pointerEvents = 'auto';
            imageUploadArea.style.cursor = 'pointer';
            imageUploadArea.title = '';
        }
    }
    
                if (imageFilesInput) {
                    // Utiliser readOnly au lieu de disabled pour que le champ soit toujours envoyé
                    imageFilesInput.disabled = isMaxReached; // OK pour les input file
                }
    
                if (imageUrlsTextarea) {
                    // NE JAMAIS désactiver le textarea car les champs disabled ne sont pas envoyés dans le formulaire
                    // Utiliser readonly à la place pour l'affichage visuel
                    if (isMaxReached) {
                        imageUrlsTextarea.readOnly = true;
                        imageUrlsTextarea.style.opacity = '0.5';
                        imageUrlsTextarea.style.cursor = 'not-allowed';
                        imageUrlsTextarea.title = 'Maximum de 6 images atteint. Supprimez des images pour en ajouter.';
                    } else {
                        imageUrlsTextarea.readOnly = false;
                        imageUrlsTextarea.style.opacity = '1';
                        imageUrlsTextarea.style.cursor = 'text';
                        imageUrlsTextarea.title = '';
                    }
                }
    
    // Afficher/masquer le message d'avertissement
    let maxReachedMsg = document.getElementById('maxImagesReachedMsg');
    if (isMaxReached) {
        if (!maxReachedMsg) {
            maxReachedMsg = document.createElement('div');
            maxReachedMsg.id = 'maxImagesReachedMsg';
            maxReachedMsg.className = 'form-error';
            maxReachedMsg.style.marginTop = '10px';
            maxReachedMsg.style.padding = '10px';
            maxReachedMsg.style.background = '#fef2f2';
            maxReachedMsg.style.border = '1px solid #fecaca';
            maxReachedMsg.style.borderRadius = '8px';
            maxReachedMsg.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <strong>Maximum atteint :</strong> Vous avez atteint la limite de 6 images. Supprimez des images existantes pour en ajouter de nouvelles.';
            const imagesSection = document.querySelector('.form-section:has(#imageCounter)');
            if (imagesSection) {
                imagesSection.appendChild(maxReachedMsg);
            }
        }
    } else {
        if (maxReachedMsg) {
            maxReachedMsg.remove();
        }
    }
}

if (imagePreviewGrid) {
    imagePreviewGrid.addEventListener('click', (e) => {
        if (e.target.closest('.remove-preview-btn')) {
            const index = parseInt(e.target.closest('.remove-preview-btn').dataset.index);
            uploadedImages.splice(index, 1);
            updatePreview();
            updateImageCounters();
        }
    });
}

// Supprimer images existantes
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.remove-image-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.dataset.url;
            removedExistingImages.push(url);
            const item = this.closest('.existing-image-item');
            if (item) {
                item.style.display = 'none';
            }
            
            // Retirer de la textarea
            const imagesTextarea = document.getElementById('imageUrls');
            if (imagesTextarea) {
                const currentUrls = imagesTextarea.value.split(',').map(u => u.trim()).filter(u => u && u !== url);
                // Si toutes les images sont supprimées, vider complètement le textarea
                imagesTextarea.value = currentUrls.length > 0 ? currentUrls.join(', ') : '';
                console.log('Image removed from textarea. Remaining URLs:', currentUrls.length, 'URLs:', currentUrls);
            }
            updateImageCounters();
        });
    });
});

// Écouter les changements dans le textarea des URLs
const imageUrlsTextarea = document.getElementById('imageUrls');
if (imageUrlsTextarea) {
    imageUrlsTextarea.addEventListener('input', function() {
        updateImageCounters();
        
        // Valider que le total ne dépasse pas 6 (les existantes sont dans les URLs)
        const totalCount = countTotalImages();
        if (totalCount > 6) {
            this.style.borderColor = '#ef4444';
            const errorMsg = document.getElementById('urlCountError');
            if (!errorMsg) {
                const error = document.createElement('div');
                error.id = 'urlCountError';
                error.className = 'form-error';
                error.textContent = `Vous avez ${totalCount} images au total. Maximum 6 autorisées.`;
                this.parentNode.insertBefore(error, this.nextSibling);
            }
        } else {
            this.style.borderColor = '';
            const errorMsg = document.getElementById('urlCountError');
            if (errorMsg) {
                errorMsg.remove();
            }
        }
    });
}

// Calcul automatique de la marge
function calculateMargin() {
    const supplierPrice = parseFloat(document.getElementById('supplier_price')?.value || 0);
    const sellingPrice = parseFloat(document.getElementById('selling_price')?.value || 0);
    const marginDisplay = document.getElementById('margin_display');
    const marginPercentage = document.getElementById('margin_percentage');
    
    if (marginDisplay && marginPercentage) {
        const margin = sellingPrice - supplierPrice;
        const percentage = supplierPrice > 0 ? ((margin / supplierPrice) * 100).toFixed(1) : 0;
        
        marginDisplay.value = '$' + margin.toFixed(2);
        marginPercentage.textContent = percentage + '%';
        
        // Changer la couleur selon la marge
        if (margin < 0) {
            marginDisplay.style.color = '#ef4444';
            marginPercentage.style.color = '#ef4444';
        } else if (margin < sellingPrice * 0.1) {
            marginDisplay.style.color = '#f59e0b';
            marginPercentage.style.color = '#f59e0b';
        } else {
            marginDisplay.style.color = '#10b981';
            marginPercentage.style.color = '#10b981';
        }
    }
}

// Validation avant soumission du formulaire
const form = document.querySelector('.create-store-form');
if (form) {
    form.addEventListener('submit', function(e) {
        // ✅ Mettre à jour le champ hidden avec TOUTES les images (textarea + fichiers uploadés)
        const existingImagesInput = document.getElementById('existingImagesInput');
        const textarea = document.getElementById('imageUrls');
        
        if (existingImagesInput && textarea) {
            // ✅ CORRECTION: Récupérer les images existantes depuis les éléments visibles (pas le textarea)
            // Les images existantes sont celles qui sont visibles dans la grille
            const existingGrid = document.getElementById('existingImagesGrid');
            const existingImages = [];
            
            if (existingGrid) {
                const visibleItems = Array.from(existingGrid.querySelectorAll('.existing-image-item'))
                    .filter(item => item.style.display !== 'none');
                existingImages.push(...visibleItems.map(item => item.dataset.url));
            }
            
            // ✅ DÉDUPLIQUER : Supprimer les doublons avant d'envoyer
            const uniqueImages = [...new Set(existingImages)];
            
            // Mettre à jour le champ hidden avec les images existantes visibles (sans doublons)
            existingImagesInput.value = JSON.stringify(uniqueImages);
            
            console.log('=== FORM SUBMIT DEBUG ===');
            console.log('Existing images (from visible grid):', existingImages.length, existingImages);
            console.log('Unique images (deduplicated):', uniqueImages.length, uniqueImages);
            if (existingImages.length !== uniqueImages.length) {
                console.warn('⚠️ DOUBLONS DÉTECTÉS ET SUPPRIMÉS:', existingImages.length - uniqueImages.length, 'doublon(s)');
            }
            console.log('New files uploaded:', uploadedImages.length);
            console.log('Total images to save:', countTotalImages());
        }
        
        // ✅ Utiliser la fonction de comptage unique et correcte
        const totalImages = countTotalImages();
        
        // Vérifier que le total ne dépasse pas 6
        if (totalImages > 6) {
            e.preventDefault();
            alert(`Vous avez ${totalImages} images au total. Le maximum autorisé est de 6 images (fichiers + URLs). Veuillez en supprimer ${totalImages - 6}.`);
            return false;
        }
        
        // Filtrer les fichiers vides ou invalides avant soumission
        const fileInput = document.getElementById('imageFiles');
        if (fileInput && fileInput.files.length > 0) {
            const validFiles = Array.from(fileInput.files).filter(file => {
                // Vérifier que c'est une image valide
                if (!file.type.startsWith('image/')) {
                    return false;
                }
                // Vérifier le type MIME
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    return false;
                }
                // Vérifier la taille (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    alert(`Le fichier "${file.name}" est trop volumineux (max 5MB).`);
                    return false;
                }
                return true;
            });
            
            // Mettre à jour l'input avec seulement les fichiers valides
            if (validFiles.length !== fileInput.files.length) {
                const dt = new DataTransfer();
                validFiles.forEach(file => dt.items.add(file));
                fileInput.files = dt.files;
            }
        }
    });
}

// Initialiser les compteurs au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser removedExistingImages avec un tableau vide
    removedExistingImages = [];
    
    // Mettre à jour le compteur initial (compte automatiquement les images existantes)
    updateImageCounters();
    calculateMargin();
});

function togglePromotionFields() {
    const checkbox = document.getElementById('has_promotion');
    const fields = document.getElementById('promotion_fields');
    if (checkbox && fields) {
        fields.style.display = checkbox.checked ? 'block' : 'none';
        if (!checkbox.checked) {
            // Réinitialiser les champs si la promotion est désactivée
            const promoPercentage = document.getElementById('promo_percentage');
            const promoDuration = document.getElementById('promo_duration_days');
            if (promoPercentage) promoPercentage.value = '';
            if (promoDuration) promoDuration.value = '7';
            const promoPreview = document.getElementById('promoPreview');
            if (promoPreview) promoPreview.style.display = 'none';
        } else {
            calculatePromoPrice();
        }
    }
}

function calculatePromoPrice() {
    const sellingPrice = parseFloat(document.getElementById('selling_price')?.value || 0);
    const promoPercentage = parseFloat(document.getElementById('promo_percentage')?.value || 0);
    const promoPreview = document.getElementById('promoPreview');
    const originalPriceDisplay = document.getElementById('originalPriceDisplay');
    const promoPriceDisplay = document.getElementById('promoPriceDisplay');
    const savingsDisplay = document.getElementById('savingsDisplay');
    const promoPriceCalculated = document.getElementById('promo_price_calculated');
    
    if (sellingPrice > 0 && promoPercentage > 0 && promoPercentage <= 99) {
        const discount = (sellingPrice * promoPercentage) / 100;
        const promoPrice = sellingPrice - discount;
        
        if (promoPriceCalculated) {
            promoPriceCalculated.value = promoPrice.toFixed(2);
        }
        
        if (promoPreview && originalPriceDisplay && promoPriceDisplay && savingsDisplay) {
            originalPriceDisplay.textContent = '$' + sellingPrice.toFixed(2);
            promoPriceDisplay.textContent = '$' + promoPrice.toFixed(2);
            savingsDisplay.textContent = '$' + discount.toFixed(2);
            promoPreview.style.display = 'block';
        }
    } else {
        if (promoPreview) promoPreview.style.display = 'none';
        if (promoPriceCalculated) promoPriceCalculated.value = '';
    }
}

// Écouter les changements du prix de vente pour recalculer la promo
document.addEventListener('DOMContentLoaded', function() {
    const sellingPriceInput = document.getElementById('selling_price');
    if (sellingPriceInput) {
        sellingPriceInput.addEventListener('input', function() {
            if (document.getElementById('has_promotion')?.checked) {
                calculatePromoPrice();
            }
        });
    }
});

// Fonction pour le compteur de caractères
function updateCharCounter(inputId, counterId, maxLength) {
    const input = document.getElementById(inputId);
    const counter = document.getElementById(counterId);
    if (input && counter) {
        const currentLength = input.value.length;
        counter.textContent = currentLength + '/' + maxLength;
        
        // Changer la couleur si on approche de la limite
        if (currentLength > maxLength * 0.9) {
            counter.style.color = '#ef4444';
        } else if (currentLength > maxLength * 0.75) {
            counter.style.color = '#f59e0b';
        } else {
            counter.style.color = '#6b7280';
        }
    }
}

// Initialiser le compteur au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('product_name');
    if (nameInput) {
        updateCharCounter('product_name', 'nameCharCounter', 100);
    }
});
</script>
@endsection
