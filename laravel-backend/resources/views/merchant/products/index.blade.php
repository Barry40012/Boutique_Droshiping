@extends('layouts.app')

@section('title', 'Produits - Merchant')

@section('content')
<div class="merchant-dashboard-container">
    <div class="merchant-dashboard-header" data-aos="fade-down">
        <div>
            <h1 class="merchant-dashboard-title">Produits</h1>
            <p class="merchant-dashboard-subtitle">Gérez votre catalogue</p>
        </div>
        <div class="merchant-store-info">
            <div class="store-badge">
                <i class="fas fa-store icon-inline"></i>
                <span>{{ $store->name }}</span>
            </div>
            <a href="{{ route('merchant.products.create') }}" class="btn-primary">
                <i class="fas fa-plus icon-inline"></i> Nouveau produit
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success" data-aos="fade-up">
            <i class="fas fa-check-circle icon-inline"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-error" data-aos="fade-up">
            <i class="fas fa-exclamation-circle icon-inline"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Filtres et recherche -->
    <div class="products-filters" data-aos="fade-up">
        <form method="GET" action="{{ route('merchant.products') }}" class="filters-form">
            <div class="filter-group">
                <label class="filter-label">
                    <i class="fas fa-search icon-inline"></i>
                    Recherche
                </label>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Nom ou description..."
                       class="filter-input">
            </div>
            <div class="filter-group">
                <label class="filter-label">
                    <i class="fas fa-filter icon-inline"></i>
                    Statut
                </label>
                <select name="status" class="filter-select">
                    <option value="">Tous</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actifs</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactifs</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-search icon-inline"></i> Filtrer
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('merchant.products') }}" class="btn-secondary">
                        <i class="fas fa-times icon-inline"></i> Réinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="products-grid" data-aos="fade-up" data-aos-delay="50">
        @forelse($products as $product)
            <div class="product-card">
                <div class="product-card-img">
                    @php 
                        // Récupérer les images du produit
                        // L'accessor du modèle Product convertit automatiquement PostgreSQL TEXT[] en array
                        $productImages = $product->images ?? [];
                        
                        // S'assurer que c'est un tableau
                        if (!is_array($productImages)) {
                            $productImages = [];
                        }
                        
                        // Filtrer les valeurs vides
                        $productImages = array_filter($productImages, function($img) {
                            return !empty($img) && is_string($img);
                        });
                        
                        // Utiliser la première image, sinon la bannière, sinon placeholder
                        $firstImage = !empty($productImages) ? reset($productImages) : ($product->banner ?? null);
                    @endphp
                    @if($firstImage)
                        <img src="{{ $firstImage }}" alt="{{ $product->name }}" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'product-img-placeholder\'><i class=\'fas fa-image\'></i></div>';">
                    @else
                        <div class="product-img-placeholder">
                            <i class="fas fa-image"></i>
                            <span>Aucune image</span>
                        </div>
                    @endif
                    <div class="product-image-count">
                        @if(count($productImages) > 0)
                            <i class="fas fa-images"></i> {{ count($productImages) }}
                        @elseif($product->banner)
                            <i class="fas fa-image"></i> 1
                        @endif
                    </div>
                </div>
                <div class="product-card-body">
                    <div class="product-card-top">
                        <h3 class="product-card-title" title="{{ $product->name }}">{{ Str::limit($product->name, 50) }}</h3>
                        <span class="status-badge {{ $product->status === 'active' ? 'status-paid' : 'status-pending' }}">
                            {{ $product->status === 'active' ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                    <p class="product-card-desc" title="{{ $product->description }}">{{ Str::limit($product->description ?? 'Aucune description', 80) }}</p>
                    <div class="product-card-prices">
                        <div class="price-item">
                            <div class="price-label">Prix vente</div>
                            <div class="price-main">${{ number_format($product->selling_price, 2) }}</div>
                        </div>
                        <div class="price-item">
                            <div class="price-label">Prix fournisseur</div>
                            <div class="price-sub">${{ number_format($product->supplier_price, 2) }}</div>
                        </div>
                        <div class="price-item">
                            <div class="price-label">Marge</div>
                            <div class="price-badge">
                                <i class="fas fa-chart-line icon-inline"></i>
                                ${{ number_format($product->margin ?? 0, 2) }}
                            </div>
                        </div>
                    </div>
                    <div class="product-card-actions">
                        <a href="{{ route('store.product.show', ['slug' => $store->slug, 'id' => $product->id]) }}" target="_blank" class="btn-primary btn-sm" title="Voir sur la boutique">
                            <i class="fas fa-eye icon-inline"></i> Voir
                        </a>
                        <a href="{{ route('merchant.products.edit', $product->id) }}" class="btn-secondary btn-sm">
                            <i class="fas fa-edit icon-inline"></i> Éditer
                        </a>
                        <form action="{{ route('merchant.products.delete', $product->id) }}" method="POST" onsubmit="return confirm('Supprimer ce produit ?')" class="inline-form">
                            @csrf
                            <button type="submit" class="btn-danger btn-sm">
                                <i class="fas fa-trash icon-inline"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="product-empty" data-aos="fade-up">
                <i class="fas fa-box-open empty-icon"></i>
                <h3 class="empty-title">Aucun produit</h3>
                <p class="empty-text">Ajoutez votre premier produit pour démarrer votre boutique.</p>
                <a href="{{ route('merchant.products.create') }}" class="btn-primary">
                    <i class="fas fa-plus icon-inline"></i> Ajouter un produit
                </a>
            </div>
        @endforelse
    </div>

    <div class="pagination-wrapper" data-aos="fade-up" data-aos-delay="100">
        {{ $products->links() }}
    </div>
</div>

<style>
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 24px;
    margin-top: 30px;
}

.product-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.product-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}

.product-card-img {
    position: relative;
    width: 100%;
    height: 220px;
    background: #f8f9fa;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-card-img img {
    transform: scale(1.05);
}

.product-img-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 48px;
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    gap: 10px;
}

.product-img-placeholder span {
    font-size: 12px;
    color: #6b7280;
}

.product-image-count {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0,0,0,0.6);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.product-card-body {
    padding: 20px;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.product-card-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 12px;
}

.product-card-title {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
    flex: 1;
    line-height: 1.4;
}

.product-card-desc {
    color: #6b7280;
    font-size: 14px;
    line-height: 1.5;
    margin: 0 0 16px 0;
    flex: 1;
}

.product-card-prices {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 16px;
    padding: 12px;
    background: #f9fafb;
    border-radius: 8px;
}

.price-item {
    text-align: center;
}

.price-label {
    font-size: 11px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.price-main {
    font-size: 18px;
    font-weight: 700;
    color: #059669;
}

.price-sub {
    font-size: 14px;
    font-weight: 500;
    color: #6b7280;
}

.price-badge {
    font-size: 14px;
    font-weight: 600;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}

.product-card-actions {
    display: flex;
    gap: 8px;
    margin-top: auto;
    padding-top: 16px;
    border-top: 1px solid #e5e7eb;
}

.product-card-actions .btn-sm {
    padding: 8px 12px;
    font-size: 13px;
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.product-card-actions .inline-form {
    margin: 0;
    flex: 0 0 auto;
}

.product-card-actions .btn-danger.btn-sm {
    padding: 8px;
    min-width: 40px;
}

.status-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    white-space: nowrap;
    flex-shrink: 0;
}

.status-paid {
    background: #d1fae5;
    color: #065f46;
}

.status-pending {
    background: #fee2e2;
    color: #991b1b;
}

@media (max-width: 768px) {
    .products-grid {
        grid-template-columns: 1fr;
    }
    
    .product-card-prices {
        grid-template-columns: 1fr;
        gap: 8px;
    }
    
    .product-card-actions {
        flex-wrap: wrap;
    }
    
    .product-card-actions .btn-sm {
        flex: 1 1 calc(50% - 4px);
    }
}
</style>
@endsection

