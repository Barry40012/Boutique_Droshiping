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
    $btnText = $store->button_text ?? 'Acheter maintenant';
    $btnAnim = $store->button_animation ?? 'none';
    $btnClass = $btnAnim !== 'none' ? ' btn-anim-'.$btnAnim : '';
@endphp
<div class="store-public-page modern-template">
    <!-- Header de la boutique -->
    <header class="store-header modern-header">
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
    <section class="store-banner modern-banner">
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
                <div class="products-grid modern-grid">
                    @foreach($products as $product)
                        <div class="product-card modern-card" data-aos="fade-up">
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
                                <a href="{{ route('products.show', $product->id) }}" class="btn-primary btn-product{{ $btnClass }}">
                                    <i class="fas fa-eye icon-inline"></i>
                                    {{ $btnText }}
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
                    @if($showHome)<li><a href="#products">Accueil</a></li>@endif
                    @if($showProduct)<li><a href="#products">Produits</a></li>@endif
                    @if($showAboutSection)<li><a href="#about">À propos</a></li>@endif
                    @if($showFaqSection)<li><a href="#faq">FAQ</a></li>@endif
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

.modern-template .store-header {
    background: linear-gradient(135deg, var(--primary, #6366f1), var(--secondary, #8b5cf6));
    color: white;
}

.store-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.store-brand {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.store-logo {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 8px;
}

.store-icon {
    font-size: 2rem;
    color: var(--primary, #6366f1);
}

.modern-template .store-icon {
    color: white;
}

.store-name {
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0;
    color: #1f2937;
}

.modern-template .store-name {
    color: white;
}

.store-nav {
    display: flex;
    gap: 2rem;
    align-items: center;
}

.nav-link {
    color: #4b5563;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
}

.nav-link:hover {
    color: var(--primary, #6366f1);
}

.modern-template .nav-link {
    color: white;
}

.modern-template .nav-link:hover {
    color: rgba(255,255,255,0.8);
}

.cart-link {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.cart-count {
    background: var(--primary, #6366f1);
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: bold;
}

.store-banner {
    background: linear-gradient(135deg, var(--primary, #6366f1), var(--secondary, #8b5cf6));
    color: white;
    padding: 4rem 0;
    text-align: center;
}

.banner-title {
    font-size: 3rem;
    margin: 0 0 1rem 0;
}

.banner-description {
    font-size: 1.25rem;
    opacity: 0.9;
}

.products-section {
    padding: 4rem 0;
}

.section-title {
    font-size: 2rem;
    text-align: center;
    margin-bottom: 3rem;
    color: #1f2937;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.modern-grid {
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
}

.product-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 12px rgba(0,0,0,0.15);
}

.modern-card {
    border: 2px solid transparent;
    transition: all 0.3s;
}

.modern-card:hover {
    border-color: var(--primary, #6366f1);
    transform: translateY(-4px);
}

.product-image {
    width: 100%;
    height: 250px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-placeholder {
    font-size: 4rem;
    color: #9ca3af;
}

/* Animations bouton */
@keyframes bounceBtn { 0%,20%,50%,80%,100%{transform:translateY(0);}40%{transform:translateY(-6px);}60%{transform:translateY(-3px);} }
@keyframes pulseBtn { 0%{box-shadow:0 0 0 0 rgba(99,102,241,0.3);}70%{box-shadow:0 0 0 10px rgba(99,102,241,0);}100%{box-shadow:0 0 0 0 rgba(99,102,241,0);} }
@keyframes shakeBtn { 0%,100%{transform:translateX(0);}20%{transform:translateX(-4px);}40%{transform:translateX(4px);}60%{transform:translateX(-3px);}80%{transform:translateX(3px);} }
@keyframes glowBtn { 0%{box-shadow:0 0 10px rgba(99,102,241,0.6);}50%{box-shadow:0 0 20px rgba(99,102,241,0.9);}100%{box-shadow:0 0 10px rgba(99,102,241,0.6);} }
.btn-anim-bounce { animation: bounceBtn 1.2s infinite; }
.btn-anim-pulse { animation: pulseBtn 1.6s infinite; }
.btn-anim-shake { animation: shakeBtn 0.9s infinite; }
.btn-anim-glow { animation: glowBtn 1.6s infinite; }

.product-info {
    padding: 1.5rem;
}

.product-name {
    font-size: 1.25rem;
    font-weight: bold;
    margin: 0 0 0.5rem 0;
    color: #1f2937;
}

.product-description {
    color: #6b7280;
    margin: 0 0 1rem 0;
    font-size: 0.9rem;
}

.product-price {
    margin-bottom: 1rem;
}

.price {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--primary, #6366f1);
}

.btn-product {
    width: 100%;
    text-align: center;
}

.empty-products {
    text-align: center;
    padding: 4rem 0;
}

.empty-icon {
    font-size: 4rem;
    color: #9ca3af;
    margin-bottom: 1rem;
}

.about-section, .faq-section {
    padding: 4rem 0;
    background: white;
}

.about-content {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
}

.faq-list {
    max-width: 800px;
    margin: 0 auto;
}

.faq-item {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f9fafb;
    border-radius: 8px;
}

.faq-question {
    font-size: 1.25rem;
    font-weight: bold;
    margin: 0 0 0.5rem 0;
    color: #1f2937;
}

.faq-answer {
    color: #6b7280;
    margin: 0;
}

.store-footer {
    background: #1f2937;
    color: white;
    padding: 3rem 0 1rem;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.footer-section h3, .footer-section h4 {
    margin: 0 0 1rem 0;
}

.footer-section ul {
    list-style: none;
    padding: 0;
}

.footer-section ul li {
    margin-bottom: 0.5rem;
}

.footer-section a {
    color: #d1d5db;
    text-decoration: none;
}

.footer-section a:hover {
    color: white;
}

.footer-bottom {
    text-align: center;
    padding-top: 2rem;
    border-top: 1px solid #374151;
    color: #9ca3af;
}

@media (max-width: 768px) {
    .store-header-content {
        flex-direction: column;
        gap: 1rem;
    }
    
    .store-nav {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .products-grid, .modern-grid {
        grid-template-columns: 1fr;
    }
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

