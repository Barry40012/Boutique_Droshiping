@extends('layouts.app')

@section('title', $store->name . ' - Boutique en ligne')

@section('content')
@php
    $settings = $store->settings ?? [];
    $showHome = $settings['nav_home'] ?? true;
    $showProduct = $settings['nav_product'] ?? true;
    $showAboutNav = $settings['nav_about'] ?? true;
    $showFaqNav = $settings['nav_faq'] ?? true;
    $showCartNav = $settings['nav_cart'] ?? true;
    $showAboutSection = $settings['section_about'] ?? true;
    $showFaqSection = $settings['section_faq'] ?? true;
    $footerEmail = $settings['footer_email'] ?? ($store->user->email ?? 'contact@example.com');
    $faqList = $settings['faq'] ?? [];
@endphp
<div class="store-public-page premium-template">
    <!-- Header de la boutique -->
    <header class="store-header premium-header">
        <div class="container">
            <div class="store-header-content">
                <div class="store-brand">
                    @if($store->logo)
                        <img src="{{ $store->logo }}" alt="{{ $store->name }}" class="store-logo">
                    @else
                        <i class="fas fa-store store-icon"></i>
                    @endif
                    <h1 class="store-name">{{ $store->name }}</h1>
                </div>
                <nav class="store-nav">
                    @if($showHome)<a href="#products" class="nav-link">Accueil</a>@endif
                    @if($showProduct)<a href="#products" class="nav-link">Produits</a>@endif
                    @if($showAboutNav)<a href="#about" class="nav-link">À propos</a>@endif
                    @if($showFaqNav)<a href="#faq" class="nav-link">FAQ</a>@endif
                    @if($showCartNav)
                    <a href="{{ route('cart') }}" class="nav-link cart-link">
                        <i class="fas fa-shopping-cart"></i>
                        Panier
                        <span class="cart-count" id="cartCount">{{ session('cart') ? count(session('cart')) : 0 }}</span>
                    </a>
                    @endif
                </nav>
            </div>
        </div>
    </header>

    <!-- Bannière de la boutique -->
    @if($store->banner)
    <section class="store-banner premium-banner">
        <div class="banner-content">
            <h2 class="banner-title">{{ $store->name }}</h2>
            @if($store->description)
                <p class="banner-description">{{ $store->description }}</p>
            @endif
        </div>
    </section>
    @endif

    <!-- Section Produits -->
    <section id="products" class="products-section">
        <div class="container">
            @if(isset($product) && $product)
                {{-- Affichage d'un produit unique --}}
                @php
                    $productImages = is_array($product->images) ? $product->images : (is_string($product->images) ? json_decode($product->images, true) : []);
                @endphp
                
                <div class="product-single-display">
                    <div class="product-single-images">
                        @if(!empty($productImages) && is_array($productImages))
                            <div class="product-main-image-single">
                                <img src="{{ $productImages[0] }}" alt="{{ $product->name }}" id="mainProductImage">
                            </div>
                            @if(count($productImages) > 1)
                                <div class="product-thumbnails-single">
                                    @foreach($productImages as $index => $image)
                                        <div class="thumbnail-item-single {{ $index === 0 ? 'active' : '' }}" onclick="changeMainImage('{{ $image }}', this)">
                                            <img src="{{ $image }}" alt="Image {{ $index + 1 }}">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="product-placeholder-single">
                                <i class="fas fa-image"></i>
                                <p>Aucune image disponible</p>
                            </div>
                        @endif
                    </div>
                    
                    <div class="product-single-info">
                        <h1 class="product-single-title">{{ $product->name }}</h1>
                        <div class="product-single-price">
                            <span class="price-main">{{ number_format($product->selling_price, 2) }} {{ $store->currency ?? 'USD' }}</span>
                        </div>
                        
                        @if($product->description)
                            <div class="product-single-description">
                                <h3>Description</h3>
                                <div class="description-content">
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                            </div>
                        @endif
                        
                        @if($product->status === 'active')
                            <div class="product-single-actions">
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="buy-now-form" onsubmit="event.preventDefault(); addToCartAndCheckout(this);">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-buy-now btn-primary-large{{ $btnClass }}">
                                        <i class="fas fa-bolt icon-inline"></i>
                                        {{ $btnText }}
                                    </button>
                                </form>
                                
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="add-to-cart-form">
                                    @csrf
                                    <div class="quantity-selector">
                                        <label for="quantity">Quantité:</label>
                                        <input type="number" name="quantity" id="quantity" value="1" min="1" class="quantity-input">
                                    </div>
                                    <button type="submit" class="btn-add-cart btn-secondary-large">
                                        <i class="fas fa-shopping-cart icon-inline"></i>
                                        Ajouter au panier
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="product-inactive-notice">
                                <i class="fas fa-info-circle"></i> Ce produit n'est actuellement pas disponible à la vente.
                            </div>
                        @endif
                    </div>
                </div>
            @elseif($products->count() > 0)
                <h2 class="section-title">Nos Produits</h2>
                <div class="products-grid premium-grid">
                    @foreach($products as $product)
                        <div class="product-card premium-card" data-aos="fade-up">
                            <div class="product-image">
                                @php
                                    $productImages = is_array($product->images) ? $product->images : (is_string($product->images) ? json_decode($product->images, true) : []);
                                    $firstImage = !empty($productImages) && is_array($productImages) ? $productImages[0] : null;
                                @endphp
                                @if($firstImage)
                                    <img src="{{ $firstImage }}" alt="{{ $product->name }}">
                                @else
                                    <div class="product-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="product-info">
                                <h3 class="product-name">{{ $product->name }}</h3>
                                <p class="product-description">{{ Str::limit($product->description, 100) }}</p>
                                <div class="product-price">
                                    <span class="price">{{ number_format($product->selling_price, 2) }} {{ $store->currency ?? 'USD' }}</span>
                                </div>
                                <a href="{{ route('products.show', $product->id) }}" class="btn-primary btn-product">
                                    <i class="fas fa-eye icon-inline"></i>
                                    Voir le produit
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $products->links() }}
                </div>
            @else
                <div class="empty-products">
                    <i class="fas fa-box-open empty-icon"></i>
                    <p>Aucun produit disponible pour le moment.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Section À propos -->
    @if($showAboutSection)
    <section id="about" class="about-section">
        <div class="container">
            <h2 class="section-title">À propos de {{ $store->name }}</h2>
            @if($store->description)
                <div class="about-content">
                    <p>{{ $store->description }}</p>
                </div>
            @endif
        </div>
    </section>
    @endif

    <!-- Section FAQ -->
    @if($showFaqSection)
    <section id="faq" class="faq-section">
        <div class="container">
            <h2 class="section-title">Questions fréquentes</h2>
            <div class="faq-list">
                @php
                    $defaults = [
                        ['q' => 'Quels sont les modes de paiement acceptés ?', 'a' => 'Carte bancaire, mobile money et autres méthodes sécurisées.'],
                        ['q' => 'Quels sont les délais de livraison ?', 'a' => 'Selon votre localisation, en général 7 à 21 jours ouvrés.'],
                        ['q' => 'Puis-je retourner un produit ?', 'a' => 'Oui, 14 jours pour retourner un produit non utilisé.'],
                    ];
                    $faqs = [];
                    for($i=1;$i<=3;$i++){
                        $faqs[] = [
                            'q' => $faqList[$i]['q'] ?? $defaults[$i-1]['q'],
                            'a' => $faqList[$i]['a'] ?? $defaults[$i-1]['a'],
                        ];
                    }
                @endphp
                @foreach($faqs as $faq)
                    <div class="faq-item">
                        <h3 class="faq-question">{{ $faq['q'] }}</h3>
                        <p class="faq-answer">{{ $faq['a'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Footer -->
    <footer class="store-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>{{ $store->name }}</h3>
                    <p>{{ $store->description ?? 'Votre boutique de confiance' }}</p>
                </div>
                <div class="footer-section">
                    <h4>Liens rapides</h4>
                    <ul>
                        <li><a href="#products">Produits</a></li>
                        <li><a href="#about">À propos</a></li>
                        <li><a href="#faq">FAQ</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <p>Email: {{ $footerEmail }}</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} {{ $store->name }}. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</div>

<style>
.store-public-page {
    min-height: 100vh;
    background: #f9fafb;
}

/* Masquer le header de la plateforme sur les pages de boutique */
body:has(.store-public-page) .topbar {
    display: none !important;
}

body:has(.store-public-page) .main {
    padding-top: 0 !important;
}

.store-header {
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 1rem 0;
    position: sticky;
    top: 0;
    z-index: 1000;
    margin-top: 0;
}

.premium-template .store-header {
    background: linear-gradient(135deg, #1a1a2e, #16213e);
    color: white;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.premium-template .nav-link {
    color: white;
    font-weight: 600;
}

.premium-template .nav-link:hover {
    color: #fbbf24;
}

.premium-grid {
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 2.5rem;
}

.premium-card {
    background: linear-gradient(135deg, white, #f9fafb);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    border: 1px solid #e5e7eb;
}

.premium-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 32px rgba(0,0,0,0.2);
}
/* Styles pour l'affichage d'un produit unique */
.product-single-display {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-top: 30px;
}

.product-single-images {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.product-main-image-single {
    width: 100%;
    aspect-ratio: 1;
    border-radius: 12px;
    overflow: hidden;
    background: #f9fafb;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-main-image-single img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.3s;
}

.product-main-image-single img:hover {
    transform: scale(1.05);
}

.product-thumbnails-single {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.thumbnail-item-single {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.3s;
}

.thumbnail-item-single.active {
    border-color: {{ $store->primary_color ?? '#2563eb' }};
}

.thumbnail-item-single img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-placeholder-single {
    width: 100%;
    aspect-ratio: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f9fafb;
    border-radius: 12px;
    color: #9ca3af;
}

.product-placeholder-single i {
    font-size: 64px;
    margin-bottom: 15px;
}

.product-single-info {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.product-single-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.product-single-price {
    font-size: 2rem;
    font-weight: 700;
    color: {{ $store->primary_color ?? '#2563eb' }};
}

.product-single-description {
    margin-top: 20px;
}

.product-single-description h3 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 15px;
    color: #111827;
}

.description-content {
    line-height: 1.8;
    color: #4b5563;
    font-size: 1.1rem;
}

.product-single-actions {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-top: 30px;
}

.buy-now-form,
.add-to-cart-form {
    width: 100%;
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.quantity-selector label {
    font-weight: 500;
    color: #374151;
}

.quantity-input {
    width: 80px;
    padding: 10px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 16px;
}

.btn-buy-now,
.btn-add-cart {
    width: 100%;
    padding: 15px 30px;
    font-size: 18px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s;
    border: none;
}

.btn-buy-now {
    background: {{ $store->primary_color ?? '#2563eb' }};
    color: white;
}

.btn-buy-now:hover {
    opacity: 0.9;
    transform: translateY(-2px);
}

.btn-add-cart {
    border: 2px solid {{ $store->primary_color ?? '#2563eb' }};
    background: white;
    color: {{ $store->primary_color ?? '#2563eb' }};
}

.btn-add-cart:hover {
    background: {{ $store->primary_color ?? '#2563eb' }};
    color: white;
}

.product-inactive-notice {
    padding: 15px;
    background: #fef3c7;
    color: #92400e;
    border-radius: 8px;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

@media (max-width: 768px) {
    .product-single-display {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Changer l'image principale
function changeMainImage(imageSrc, element) {
    document.getElementById('mainProductImage').src = imageSrc;
    document.querySelectorAll('.thumbnail-item-single').forEach(item => {
        item.classList.remove('active');
    });
    element.classList.add('active');
}

// Ajouter au panier et rediriger vers checkout
function addToCartAndCheckout(form) {
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.ok) {
            window.location.href = '{{ route("checkout") }}';
        } else {
            alert('Erreur lors de l\'ajout au panier');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de l\'ajout au panier');
    });
}
</script>
@endsection

