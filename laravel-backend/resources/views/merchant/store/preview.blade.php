@extends('layouts.app')

@section('title', 'Aperçu de ma boutique - Dashboard Marchand')

@section('content')
<div class="store-preview-page">
    <div class="preview-header" data-aos="fade-down">
        <div>
            <a href="{{ route('merchant.dashboard') }}" class="back-link">
                <i class="fas fa-arrow-left icon-inline"></i>
                Retour au dashboard
            </a>
            <h1 class="preview-title">
                <i class="fas fa-eye icon-inline"></i>
                Aperçu de votre boutique
            </h1>
            <p class="preview-subtitle">Voici à quoi ressemble votre boutique pour vos clients</p>
        </div>
        <div class="preview-actions">
            <a href="{{ route('merchant.store.customize') }}" class="btn-secondary">
                <i class="fas fa-palette icon-inline"></i>
                Personnaliser
            </a>
            <a href="{{ $store->publicUrl }}" target="_blank" class="btn-primary">
                <i class="fas fa-external-link-alt icon-inline"></i>
                Voir la boutique publique
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" data-aos="fade-up">
            <i class="fas fa-check-circle icon-inline"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Aperçu de la boutique -->
    <div class="store-preview-container" data-aos="fade-up">
        <div class="preview-frame">
            <div class="store-front" style="--primary-color: {{ $store->primary_color ?? '#0ea5e9' }}; --secondary-color: {{ $store->secondary_color ?? '#a855f7' }}; --accent-color: {{ $store->accent_color ?? '#f97316' }};">
                <!-- Header de la boutique -->
                <header class="store-header">
                    <div class="store-header-content">
                        @if($store->logo)
                            <img src="{{ $store->logo }}" alt="{{ $store->name }}" class="store-logo">
                        @else
                            <div class="store-logo-placeholder">
                                <i class="fas fa-store"></i>
                            </div>
                        @endif
                        <div class="store-header-info">
                            <h1 class="store-name">{{ $store->name }}</h1>
                            @if($store->category)
                                <span class="store-category">{{ ucfirst($store->category) }}</span>
                            @endif
                        </div>
                    </div>
                </header>

                <!-- Bannière -->
                @if($store->banner)
                    <div class="store-banner" style="background: {{ $store->banner === 'banner-1' ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' : ($store->banner === 'banner-2' ? 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)' : ($store->banner === 'banner-3' ? 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)' : 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)')) }};">
                        <div class="banner-content">
                            <h2 class="banner-title">{{ $store->name }}</h2>
                            @if($store->description)
                                <p class="banner-description">{{ $store->description }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Description -->
                @if($store->description)
                    <section class="store-description">
                        <div class="container">
                            <p>{{ $store->description }}</p>
                        </div>
                    </section>
                @endif

                <!-- Produits -->
                <section class="store-products">
                    <div class="container">
                        <h2 class="section-title">
                            <i class="fas fa-box icon-inline"></i>
                            Nos produits
                        </h2>
                        @if($products->count() > 0)
                            <div class="products-grid">
                                @foreach($products as $product)
                                    <div class="product-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                                        @if($product->images && count($product->images) > 0)
                                            <div class="product-image">
                                                <img src="{{ $product->images[0] }}" alt="{{ $product->name }}">
                                            </div>
                                        @else
                                            <div class="product-image-placeholder">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                        <div class="product-info">
                                            <h3 class="product-name">{{ $product->name }}</h3>
                                            @if($product->description)
                                                <p class="product-description">{{ Str::limit($product->description, 80) }}</p>
                                            @endif
                                            <div class="product-price">${{ number_format($product->selling_price, 2) }}</div>
                                            <button class="btn-add-cart">
                                                <i class="fas fa-shopping-cart icon-inline"></i>
                                                Ajouter au panier
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($products->hasPages())
                                <div class="pagination-container">
                                    {{ $products->links() }}
                                </div>
                            @endif
                        @else
                            <div class="empty-products">
                                <i class="fas fa-box-open empty-icon"></i>
                                <h3>Aucun produit pour le moment</h3>
                                <p>Ajoutez des produits à votre boutique pour qu'ils apparaissent ici.</p>
                                <a href="{{ route('merchant.products.create') }}" class="btn-primary">
                                    <i class="fas fa-plus icon-inline"></i>
                                    Ajouter un produit
                                </a>
                            </div>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init();
</script>
@endsection

