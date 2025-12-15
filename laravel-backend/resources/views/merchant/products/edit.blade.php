@extends('layouts.app')

@section('title', 'Éditer le produit - Merchant')

@section('content')
<style>
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

        <form action="{{ route('merchant.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="create-store-form">
            @csrf

            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-info-circle icon-inline"></i>
                    Informations
                </h2>
                <div class="form-group">
                    <label class="form-label">Nom</label>
                    <input type="text" name="name" class="form-input @error('name') error @enderror" value="{{ old('name', $product->name) }}" required>
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-align-left icon-inline"></i>
                        Description
                    </label>
                    <textarea name="description" 
                              id="product-description" 
                              rows="8" 
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
                <div class="form-group">
                    <label class="form-label">Prix fournisseur ($)</label>
                    <input type="number" step="0.01" name="supplier_price" class="form-input @error('supplier_price') error @enderror" value="{{ old('supplier_price', $product->supplier_price) }}" required>
                    @error('supplier_price') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Prix de vente ($)</label>
                    <input type="number" step="0.01" name="selling_price" class="form-input @error('selling_price') error @enderror" value="{{ old('selling_price', $product->selling_price) }}" required>
                    @error('selling_price') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-link icon-inline"></i>
                    Fournisseur & bannière produit
                </h2>
                <div class="form-group">
                    <label class="form-label">Lien fournisseur (URL)</label>
                    <input type="url"
                           name="supplier_link"
                           class="form-input @error('supplier_link') error @enderror"
                           value="{{ old('supplier_link', $product->supplier_link) }}"
                           placeholder="https://fournisseur.com/produit/123">
                    @error('supplier_link') <div class="form-error">{{ $message }}</div> @enderror
                    <p class="form-hint">Permet d’envoyer la commande directement au fournisseur (dropshipping).</p>
                    <div class="import-helper">
                        <button type="button" class="btn-secondary" onclick="alert('Import automatique non activé : nécessite une API ou un scraper. Nous pourrons le brancher plus tard.')">
                            <i class="fas fa-download icon-inline"></i> Importer depuis le lien (stub)
                        </button>
                        <p class="form-hint">Pour un import auto (images/prix/titre), il faut une API ou un scraper. Cette action est prête à être branchée plus tard.</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">ID produit fournisseur (optionnel)</label>
                    <input type="text"
                           name="supplier_product_id"
                           class="form-input @error('supplier_product_id') error @enderror"
                           value="{{ old('supplier_product_id', $product->supplier_product_id) }}"
                           placeholder="SKU / ID fournisseur">
                    @error('supplier_product_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Bannière produit (URL)</label>
                    <input type="text"
                           name="banner"
                           class="form-input @error('banner') error @enderror"
                           value="{{ old('banner', $product->banner) }}"
                           placeholder="https://exemple.com/image-banner.jpg">
                    @error('banner') <div class="form-error">{{ $message }}</div> @enderror
                    <p class="form-hint">Utilisée dans les thèmes qui mettent le produit en avant (hero).</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Lien du store fournisseur (optionnel)</label>
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
                    Images <span id="imageCounter" class="image-counter">(0/4)</span>
                </h2>
                <p class="form-hint" style="margin-bottom: 20px;">
                    <i class="fas fa-info-circle icon-inline"></i>
                    Vous pouvez utiliser les deux options : uploader des fichiers ET/OU ajouter des URLs. Maximum 4 images au total.
                </p>
                
                <!-- Images existantes -->
                @if($product->images && count($product->images) > 0)
                <div class="form-group">
                    <label class="form-label">Images actuelles</label>
                    <div class="existing-images-grid">
                        @foreach($product->images as $img)
                        <div class="existing-image-item">
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
                              placeholder="https://exemple.com/image1.jpg, https://exemple.com/image2.jpg (séparées par des virgules)">{{ old('images', implode(', ', $product->images ?? [])) }}</textarea>
                    <div class="form-hint">
                        <i class="fas fa-lightbulb icon-inline"></i>
                        Séparez les URLs par des virgules. Vous pouvez combiner avec les fichiers uploadés ci-dessus.
                    </div>
                    @error('images') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-toggle-on icon-inline"></i>
                    Statut
                </h2>
                <div class="form-group">
                    <select name="status" class="form-input @error('status') error @enderror" required>
                        <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                    @error('status') <div class="form-error">{{ $message }}</div> @enderror
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

if (imageUploadArea) {
    imageUploadArea.addEventListener('click', () => imageFilesInput.click());
    imageUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        imageUploadArea.classList.add('dragover');
    });
    imageUploadArea.addEventListener('dragleave', () => {
        imageUploadArea.classList.remove('dragover');
    });
    imageUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        imageUploadArea.classList.remove('dragover');
        if (e.dataTransfer.files.length > 0) {
            handleFiles(e.dataTransfer.files);
        }
    });
}

if (imageFilesInput) {
    imageFilesInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFiles(e.target.files);
        }
    });
}

function countImageUrls() {
    const urlsTextarea = document.getElementById('imageUrls');
    if (!urlsTextarea || !urlsTextarea.value.trim()) return 0;
    
    const urls = urlsTextarea.value.split(',').map(u => u.trim()).filter(u => u);
    return urls.length;
}

function handleFiles(files) {
    const totalImages = uploadedImages.length + countImageUrls();
    const remainingSlots = 4 - totalImages;
    
    if (remainingSlots <= 0) {
        alert('Vous avez déjà atteint la limite de 4 images (fichiers + URLs). Supprimez-en d\'abord.');
        return;
    }
    
    Array.from(files).slice(0, remainingSlots).forEach(file => {
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
                updatePreview();
                updateImageCounters();
            };
            reader.readAsDataURL(file);
        }
    });
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
    const fileCount = uploadedImages.length;
    const urlCount = countImageUrls();
    const totalCount = fileCount + urlCount;
    
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
        totalCounter.textContent = `(${totalCount}/4)`;
        // Changer la couleur selon le nombre
        if (totalCount >= 4) {
            totalCounter.style.color = '#ef4444';
        } else if (totalCount >= 3) {
            totalCounter.style.color = '#f59e0b';
        } else {
            totalCounter.style.color = '#10b981';
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

// Écouter les changements dans le textarea des URLs
const imageUrlsTextarea = document.getElementById('imageUrls');
if (imageUrlsTextarea) {
    imageUrlsTextarea.addEventListener('input', function() {
        updateImageCounters();
        
        // Valider que le total ne dépasse pas 4
        const totalCount = uploadedImages.length + countImageUrls();
        if (totalCount > 4) {
            this.style.borderColor = '#ef4444';
            const errorMsg = document.getElementById('urlCountError');
            if (!errorMsg) {
                const error = document.createElement('div');
                error.id = 'urlCountError';
                error.className = 'form-error';
                error.textContent = `Vous avez ${totalCount} images au total. Maximum 4 autorisées.`;
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

// Supprimer images existantes
document.querySelectorAll('.remove-image-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const url = this.dataset.url;
        const imagesTextarea = document.getElementById('imageUrls');
        if (imagesTextarea) {
            const urls = imagesTextarea.value.split(',').map(u => u.trim()).filter(u => u && u !== url);
            imagesTextarea.value = urls.join(', ');
            updateImageCounters();
        }
        this.closest('.existing-image-item').remove();
    });
});

// Initialiser les compteurs au chargement
document.addEventListener('DOMContentLoaded', function() {
    updateImageCounters();
});

// Validation avant soumission du formulaire
const form = document.querySelector('.create-store-form');
if (form) {
    form.addEventListener('submit', function(e) {
        const totalImages = uploadedImages.length + countImageUrls();
        
        // Vérifier que le total ne dépasse pas 4
        if (totalImages > 4) {
            e.preventDefault();
            alert(`Vous avez ${totalImages} images au total. Le maximum autorisé est de 4 images (fichiers + URLs). Veuillez en supprimer ${totalImages - 4}.`);
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
</script>
@endsection

