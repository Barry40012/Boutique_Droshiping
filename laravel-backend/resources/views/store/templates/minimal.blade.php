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
<div class="store-public-page minimal-template">
    <!-- Header de la boutique -->
    <header class="store-header minimal-header">
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
                    @if($showHome)<a href="#product" class="nav-link">Accueil</a>@endif
                    @if($showProduct)<a href="#product" class="nav-link">Produit</a>@endif
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

    @php $productBanner = ($products->count() > 0) ? ($products->first()->banner ?? null) : null; @endphp
    <!-- Bannière avec image de fond -->
    <section class="store-banner minimal-banner" 
        @if($productBanner && strpos($productBanner,'http')===0)
            style="background-image: linear-gradient(135deg, rgba(0,0,0,0.45), rgba(0,0,0,0.45)), url('{{ $productBanner }}');"
        @elseif($store->banner && strpos($store->banner, 'http') === 0)
            style="background-image: url('{{ $store->banner }}');"
        @elseif($store->banner)
            style="background: {{ $store->banner }};"
        @endif
    >
        <div class="banner-overlay"></div>
        <div class="banner-content">
            <h2 class="banner-title">{{ $store->name }}</h2>
            <div class="banner-scrolling-text">
                <div class="scrolling-wrapper">
                    <span class="scrolling-item active">{{ $store->description ?? 'Bienvenue dans notre boutique' }}</span>
                    @if($store->description)
                        <span class="scrolling-item">{{ $store->description }}</span>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Section Produit Unique -->
    <section id="product" class="product-section minimal-product">
        <div class="container" style="max-width: 1100px;">
            @if($products->count() > 0)
                @php
                    $product = $products->first(); // Un seul produit
                    // Utiliser directement l'accessor du modèle qui gère PostgreSQL TEXT[]
                    $productImages = $product->images ?? [];
                    // S'assurer que c'est un tableau et filtrer les valeurs vides
                    if (!is_array($productImages)) {
                        $productImages = [];
                    }
                    // Filtrer les valeurs vides ou null
                    $productImages = array_filter($productImages, function($img) {
                        return !empty($img) && is_string($img);
                    });
                    // Réindexer le tableau après filtrage
                    $productImages = array_values($productImages);
                @endphp
                
                <div class="product-display">
                    <!-- Images du produit avec navigation -->
                    <div class="product-images-wrapper">
                        <div class="product-main-image">
                            @if(!empty($productImages) && is_array($productImages) && count($productImages) > 0)
                                <div class="image-slider" id="imageSlider">
                                    @foreach($productImages as $index => $image)
                                        @if(!empty($image))
                                            <div class="slide {{ $index === 0 ? 'active' : '' }}">
                                                <img src="{{ $image }}" alt="{{ $product->name }}" class="product-image" onclick="openImageModal('{{ $image }}')" onerror="console.error('Erreur chargement image:', '{{ $image }}'); this.style.display='none';">
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                
                                @if(count($productImages) > 1)
                                    <button class="slider-nav prev" onclick="changeImage(-1)">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <button class="slider-nav next" onclick="changeImage(1)">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                    
                                    <div class="image-indicators">
                                        @foreach($productImages as $index => $image)
                                            <span class="indicator {{ $index === 0 ? 'active' : '' }}" onclick="goToImage({{ $index }})"></span>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <div class="product-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p>Aucune image disponible</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Informations du produit -->
                    <div class="product-info-wrapper">
                        <h1 class="product-title">{{ $product->name }}</h1>
                        
                        <div class="product-price">
                            <span class="price-amount">{{ number_format($product->selling_price, 2) }}</span>
                            <span class="price-currency">{{ $store->currency ?? 'USD' }}</span>
                        </div>

                        <div class="product-description">
                            <h3>Description</h3>
                            <p>{{ $product->description ?? 'Aucune description disponible.' }}</p>
                        </div>

                        @if($product->status === 'active')
                            <!-- Boutons d'action personnalisables -->
                            <div class="product-actions">
                                <!-- Bouton Acheter maintenant (personnalisable) -->
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="buy-now-form" onsubmit="event.preventDefault(); addToCartAndCheckout(this);">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-buy btn-primary-large{{ $btnClass }}">
                                        <i class="fas fa-bolt icon-inline"></i>
                                        {{ $btnText }}
                                    </button>
                                </form>
                                
                                <!-- Bouton Ajouter au panier -->
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
                            <div class="product-actions">
                                <div class="product-inactive-notice">
                                    <i class="fas fa-info-circle"></i> Ce produit n'est actuellement pas disponible à la vente.
                                </div>
                            </div>
                        @endif
                    </div>
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
    <section id="about" class="about-section minimal-about">
        <div class="container">
            <h2 class="section-title">À propos de {{ $store->name }}</h2>
            @if($store->description)
                <div class="about-content">
                    <p>{{ $store->description }}</p>
                </div>
            @endif
            
            <!-- Réseaux sociaux -->
            @if($store->facebook_url || $store->instagram_url || $store->twitter_url || $store->youtube_url)
                <div class="social-links">
                    @if($store->facebook_url)
                        <a href="{{ $store->facebook_url }}" target="_blank" class="social-link">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    @endif
                    @if($store->instagram_url)
                        <a href="{{ $store->instagram_url }}" target="_blank" class="social-link">
                            <i class="fab fa-instagram"></i>
                        </a>
                    @endif
                    @if($store->twitter_url)
                        <a href="{{ $store->twitter_url }}" target="_blank" class="social-link">
                            <i class="fab fa-twitter"></i>
                        </a>
                    @endif
                    @if($store->youtube_url)
                        <a href="{{ $store->youtube_url }}" target="_blank" class="social-link">
                            <i class="fab fa-youtube"></i>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </section>
    @endif

    <!-- Section FAQ -->
    @if($showFaqSection)
    <section id="faq" class="faq-section minimal-faq">
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
    <footer class="store-footer minimal-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>{{ $store->name }}</h3>
                    <p>{{ $store->description ?? 'Votre boutique de confiance' }}</p>
                </div>
                <div class="footer-section">
                    <h4>Liens rapides</h4>
                    <ul>
                        @if($showHome)<li><a href="#product">Accueil</a></li>@endif
                        @if($showProduct)<li><a href="#product">Produit</a></li>@endif
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

<!-- Modal pour zoomer l'image -->
<div id="imageModal" class="image-modal" onclick="closeImageModal()">
    <span class="modal-close">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<style>
.store-public-page {
    min-height: 100vh;
    background: #ffffff;
}

/* Masquer le header de la plateforme */
body:has(.store-public-page) .topbar {
    display: none !important;
}

body:has(.store-public-page) .main {
    padding-top: 0 !important;
}

/* Header Minimal */
.minimal-header {
    background: white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 1.5rem 0;
    position: sticky;
    top: 0;
    z-index: 1000;
    margin-top: 0;
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
    object-fit: contain;
    border-radius: 8px;
}

.store-icon {
    font-size: 2rem;
    color: #0ea5e9;
}

.store-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.store-nav {
    display: flex;
    gap: 2rem;
    align-items: center;
}

.nav-link {
    color: #6b7280;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
}

.nav-link:hover {
    color: #0ea5e9;
}

.cart-link {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.cart-count {
    background: #0ea5e9;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Bannière avec image de fond */
.minimal-banner {
    position: relative;
    min-height: 400px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.banner-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.4);
}

.banner-content {
    position: relative;
    z-index: 1;
    text-align: center;
    color: white;
    padding: 2rem;
}

.banner-title {
    font-size: 3rem;
    font-weight: 700;
    margin: 0 0 1.5rem 0;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.banner-scrolling-text {
    height: 60px;
    overflow: hidden;
    position: relative;
}

.scrolling-wrapper {
    position: relative;
    height: 100%;
}

.scrolling-item {
    position: absolute;
    width: 100%;
    font-size: 1.25rem;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.5s ease;
}

.scrolling-item.active {
    opacity: 1;
    transform: translateY(0);
}

/* Section Produit */
.minimal-product {
    padding: 2.5rem 0;
    background: #f9fafb;
}

.product-display {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    max-width: 1100px;
    margin: 0 auto;
    padding: 2rem 0;
}

/* Images à gauche, description à droite */
.product-images-wrapper {
    order: 1;
}

.product-info-wrapper {
    order: 2;
}

.product-images-wrapper {
    position: relative;
}

.product-main-image {
    position: relative;
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
}

.image-slider {
    position: relative;
    width: 100%;
    height: 600px;
    overflow: hidden;
}

.slide {
    display: none;
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    opacity: 0;
    transition: opacity 0.6s ease-in-out;
}

.slide.active {
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 1;
    z-index: 1;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: zoom-in;
    transition: transform 0.4s ease;
    display: block;
}

.product-image:hover {
    transform: scale(1.02);
}

.slider-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,0.9);
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: #374151;
    transition: all 0.3s;
    z-index: 10;
}

.slider-nav:hover {
    background: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.slider-nav.prev {
    left: 1rem;
}

.slider-nav.next {
    right: 1rem;
}

.image-indicators {
    position: absolute;
    bottom: 1rem;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 0.5rem;
    z-index: 10;
}

.indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
    cursor: pointer;
    transition: all 0.3s;
}

.indicator.active {
    background: white;
    width: 30px;
    border-radius: 5px;
}

.product-placeholder {
    height: 500px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
}

.product-placeholder i {
    font-size: 4rem;
    margin-bottom: 1rem;
}

/* Informations produit */
.product-info-wrapper {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    padding: 1rem 0;
}

.product-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
    line-height: 1.3;
}

.product-price {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
}

.price-amount {
    font-size: 2rem;
    font-weight: 700;
    color: #0ea5e9;
}

.price-currency {
    font-size: 1.25rem;
    color: #6b7280;
}

.product-description h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 0.75rem 0;
}

.product-description p {
    font-size: 0.95rem;
    line-height: 1.7;
    color: #4b5563;
    margin: 0;
}

.product-actions {
    margin-top: 1rem;
}

.btn-buy {
    width: 100%;
    padding: 1.25rem 2rem;
    background: #0ea5e9;
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1.25rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
}

.btn-buy:hover {
    background: #0284c7;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(14, 165, 233, 0.4);
}

/* Animations boutons */
@keyframes bounceBtn { 0%,20%,50%,80%,100%{transform:translateY(0);}40%{transform:translateY(-6px);}60%{transform:translateY(-3px);} }
@keyframes pulseBtn { 0%{box-shadow:0 0 0 0 rgba(14,165,233,0.4);}70%{box-shadow:0 0 0 10px rgba(14,165,233,0);}100%{box-shadow:0 0 0 0 rgba(14,165,233,0);} }
@keyframes shakeBtn { 0%,100%{transform:translateX(0);}20%{transform:translateX(-4px);}40%{transform:translateX(4px);}60%{transform:translateX(-3px);}80%{transform:translateX(3px);} }
@keyframes glowBtn { 0%{box-shadow:0 0 10px rgba(14,165,233,0.6);}50%{box-shadow:0 0 22px rgba(14,165,233,0.9);}100%{box-shadow:0 0 10px rgba(14,165,233,0.6);} }
.btn-anim-bounce { animation: bounceBtn 1.2s infinite; }
.btn-anim-pulse { animation: pulseBtn 1.6s infinite; }
.btn-anim-shake { animation: shakeBtn 0.9s infinite; }
.btn-anim-glow { animation: glowBtn 1.6s infinite; }

/* Sections */
.minimal-about, .minimal-faq {
    padding: 4rem 0;
}

.minimal-about {
    background: white;
}

.minimal-faq {
    background: #f9fafb;
}

.section-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    text-align: center;
    margin: 0 0 3rem 0;
}

.about-content {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
}

.about-content p {
    font-size: 1.125rem;
    line-height: 1.8;
    color: #4b5563;
}

.social-links {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
    margin-top: 2rem;
}

.social-link {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 1.5rem;
    transition: all 0.3s;
    text-decoration: none;
}

.social-link:hover {
    background: #0ea5e9;
    color: white;
    transform: translateY(-3px);
}

.faq-list {
    max-width: 800px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.faq-item {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.faq-question {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 1rem 0;
}

.faq-answer {
    font-size: 1rem;
    line-height: 1.8;
    color: #4b5563;
    margin: 0;
}

/* Footer */
.minimal-footer {
    background: #1f2937;
    color: white;
    padding: 3rem 0 1.5rem;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 3rem;
    margin-bottom: 2rem;
}

.footer-section h3, .footer-section h4 {
    margin: 0 0 1rem 0;
}

.footer-section ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-section ul li {
    margin-bottom: 0.5rem;
}

.footer-section a {
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    transition: color 0.2s;
}

.footer-section a:hover {
    color: white;
}

.footer-bottom {
    text-align: center;
    padding-top: 2rem;
    border-top: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.7);
}

/* Modal Image */
.image-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    cursor: zoom-out;
}

.modal-content {
    margin: auto;
    display: block;
    width: 90%;
    max-width: 1200px;
    max-height: 90vh;
    object-fit: contain;
    animation: zoom 0.3s;
}

@keyframes zoom {
    from {transform: scale(0)}
    to {transform: scale(1)}
}

.modal-close {
    position: absolute;
    top: 15px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
}

.modal-close:hover {
    color: #bbb;
}

.empty-products {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    font-size: 4rem;
    color: #9ca3af;
    margin-bottom: 1rem;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

@media (max-width: 768px) {
    .product-display {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .banner-title {
        font-size: 2rem;
    }
    
    .footer-content {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
}
</style>

<script>
// Navigation des images
let currentImageIndex = 0;
const slides = document.querySelectorAll('.slide');
const indicators = document.querySelectorAll('.indicator');

function changeImage(direction) {
    slides[currentImageIndex].classList.remove('active');
    indicators[currentImageIndex].classList.remove('active');
    
    currentImageIndex += direction;
    
    if (currentImageIndex < 0) {
        currentImageIndex = slides.length - 1;
    } else if (currentImageIndex >= slides.length) {
        currentImageIndex = 0;
    }
    
    slides[currentImageIndex].classList.add('active');
    indicators[currentImageIndex].classList.add('active');
    
    // Réinitialiser l'auto-scroll après un changement manuel
    if (autoScrollInterval) {
        clearInterval(autoScrollInterval);
    }
    if (slides.length > 1) {
        autoScrollInterval = setInterval(() => {
            changeImage(1);
        }, 4000);
    }
}

function goToImage(index) {
    slides[currentImageIndex].classList.remove('active');
    indicators[currentImageIndex].classList.remove('active');
    
    currentImageIndex = index;
    
    slides[currentImageIndex].classList.add('active');
    indicators[currentImageIndex].classList.add('active');
    
    // Réinitialiser l'auto-scroll après un changement manuel
    if (autoScrollInterval) {
        clearInterval(autoScrollInterval);
    }
    if (slides.length > 1) {
        autoScrollInterval = setInterval(() => {
            changeImage(1);
        }, 4000);
    }
}

// Auto-scroll des images (défilement automatique)
let autoScrollInterval;
if (slides.length > 1) {
    // Démarrer le défilement automatique toutes les 4 secondes
    autoScrollInterval = setInterval(() => {
        changeImage(1);
    }, 4000);
    
    // Arrêter le défilement automatique au survol
    const sliderContainer = document.querySelector('.product-main-image');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', () => {
            if (autoScrollInterval) {
                clearInterval(autoScrollInterval);
            }
        });
        
        sliderContainer.addEventListener('mouseleave', () => {
            if (slides.length > 1) {
                autoScrollInterval = setInterval(() => {
                    changeImage(1);
                }, 4000);
            }
        });
    }
}

// Texte défilant dans la bannière
const scrollingItems = document.querySelectorAll('.scrolling-item');
let currentTextIndex = 0;

if (scrollingItems.length > 1) {
    setInterval(() => {
        scrollingItems[currentTextIndex].classList.remove('active');
        currentTextIndex = (currentTextIndex + 1) % scrollingItems.length;
        scrollingItems[currentTextIndex].classList.add('active');
    }, 4000);
}

// Modal pour zoomer l'image
function openImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    modal.style.display = 'block';
    modalImg.src = imageSrc;
}

function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
}

// Fermer avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});

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

<style>
/* Styles pour les deux boutons */
.product-actions {
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

.btn-secondary-large {
    width: 100%;
    padding: 15px 30px;
    font-size: 18px;
    font-weight: 600;
    border: 2px solid {{ $store->primary_color ?? '#2563eb' }};
    background: white;
    color: {{ $store->primary_color ?? '#2563eb' }};
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s;
}

.btn-secondary-large:hover {
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
</style>
@endsection

