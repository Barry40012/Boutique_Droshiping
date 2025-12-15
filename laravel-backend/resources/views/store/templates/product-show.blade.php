@extends('layouts.app')

@section('title', $product->name . ' - ' . $store->name)

@section('content')
@php
    $isPublicStore = true;
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
    $productImages = is_array($product->images) ? $product->images : [];
    
    // Couleurs personnalisées
    $primaryColor = $store->primary_color ?? '#2563eb';
    $secondaryColor = $store->secondary_color ?? '#1d4ed8';
    $accentColor = $store->accent_color ?? '#10b981';
@endphp

<div class="product-show-page">
    <!-- Header de la boutique -->
    <header class="store-header">
        <div class="container">
            <div class="store-header-content">
                <div class="store-brand">
                    @if($store->logo)
                        <img src="{{ $store->logo }}" alt="{{ $store->name }}" class="store-logo">
                    @else
                        <i class="fas fa-store store-icon"></i>
                    @endif
                    <span class="store-name">{{ $store->name }}</span>
                </div>
                <nav class="store-nav">
                    @if($showHome)
                        <a href="{{ route('store.public', $store->slug) }}">Accueil</a>
                    @endif
                    @if($showProduct)
                        <a href="{{ route('store.public', $store->slug) }}#products">Produits</a>
                    @endif
                    @if($showAboutNav)
                        <a href="{{ route('store.public', $store->slug) }}#about">À propos</a>
                    @endif
                    @if($showFaqNav)
                        <a href="{{ route('store.public', $store->slug) }}#faq">FAQ</a>
                    @endif
                    @if($showCartNav)
                        <a href="{{ route('cart') }}">
                            <i class="fas fa-shopping-cart"></i> Panier
                        </a>
                    @endif
                </nav>
            </div>
        </div>
    </header>

    <!-- Contenu du produit -->
    <main class="product-show-main">
        <div class="container">
            <div class="product-show-grid">
                <!-- Images du produit -->
                <div class="product-images-section">
                    @if(!empty($productImages))
                        <div class="product-main-image">
                            <img src="{{ $productImages[0] }}" alt="{{ $product->name }}" id="mainProductImage">
                        </div>
                        @if(count($productImages) > 1)
                            <div class="product-thumbnails">
                                @foreach($productImages as $index => $image)
                                    <div class="thumbnail-item {{ $index === 0 ? 'active' : '' }}" onclick="changeMainImage('{{ $image }}', this)">
                                        <img src="{{ $image }}" alt="Image {{ $index + 1 }}">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="product-placeholder-image">
                            <i class="fas fa-image"></i>
                            <p>Aucune image</p>
                        </div>
                    @endif
                </div>

                <!-- Informations du produit -->
                <div class="product-info-section">
                    <h1 class="product-title">{{ $product->name }}</h1>
                    
                    <div class="product-price-section">
                        <div class="product-price-main">${{ number_format($product->selling_price, 2) }}</div>
                        @if($product->supplier_price)
                            <div class="product-price-supplier">Prix fournisseur: ${{ number_format($product->supplier_price, 2) }}</div>
                        @endif
                    </div>

                    @if($product->description)
                        <div class="product-description">
                            <h3>Description</h3>
                            <div class="description-content">
                                {!! nl2br(e($product->description)) !!}
                            </div>
                        </div>
                    @endif

                    @if($product->status === 'active')
                        <!-- Boutons d'action personnalisables -->
                        <div class="product-actions">
                            <!-- Bouton Acheter maintenant (personnalisable) -->
                            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="buy-now-form" onsubmit="event.preventDefault(); addToCartAndCheckout(this);">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn-buy btn-primary-large{{ $btnClass }}" style="background: {{ $primaryColor }}; border-color: {{ $primaryColor }};">
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
                                <button type="submit" class="btn-add-cart btn-secondary-large" style="border-color: {{ $primaryColor }}; color: {{ $primaryColor }};" onmouseover="this.style.background='{{ $primaryColor }}'; this.style.color='white';" onmouseout="this.style.background='white'; this.style.color='{{ $primaryColor }}';">
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

                    @if($product->supplier_link)
                        <div class="product-supplier-info">
                            <small>
                                <i class="fas fa-link"></i> 
                                <a href="{{ $product->supplier_link }}" target="_blank">Voir sur le site fournisseur</a>
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

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
            
            <!-- Réseaux sociaux -->
            @if($store->facebook_url || $store->instagram_url || $store->twitter_url || $store->youtube_url)
                <div class="social-links">
                    @if($store->facebook_url)
                        <a href="{{ $store->facebook_url }}" target="_blank" class="social-link">
                            <i class="fab fa-facebook"></i>
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
    @if($showFaqSection && !empty($faqList))
    <section id="faq" class="faq-section">
        <div class="container">
            <h2 class="section-title">Questions fréquentes</h2>
            <div class="faq-list">
                @foreach($faqList as $faq)
                    @if(isset($faq['q']) && isset($faq['a']))
                        <div class="faq-item">
                            <div class="faq-question">
                                <h3>{{ $faq['q'] }}</h3>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>{{ $faq['a'] }}</p>
                            </div>
                        </div>
                    @endif
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
                    <h4>{{ $store->name }}</h4>
                    <p>{{ $store->description ?? '' }}</p>
                </div>
                <div class="footer-section">
                    <h4>Liens rapides</h4>
                    <ul>
                        @if($showHome)
                            <li><a href="{{ route('store.public', $store->slug) }}">Accueil</a></li>
                        @endif
                        @if($showProduct)
                            <li><a href="{{ route('store.public', $store->slug) }}#products">Produits</a></li>
                        @endif
                        @if($showAboutNav)
                            <li><a href="{{ route('store.public', $store->slug) }}#about">À propos</a></li>
                        @endif
                        @if($showFaqNav)
                            <li><a href="{{ route('store.public', $store->slug) }}#faq">FAQ</a></li>
                        @endif
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    @if($footerEmail)
                        <p><a href="mailto:{{ $footerEmail }}">{{ $footerEmail }}</a></p>
                    @endif
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} {{ $store->name }}. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</div>

<style>
.product-show-page {
    min-height: 100vh;
    background: #f9fafb;
}

.store-header {
    background: white;
    border-bottom: 1px solid #e5e7eb;
    padding: 20px 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.store-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.store-brand {
    display: flex;
    align-items: center;
    gap: 15px;
}

.store-logo {
    width: 50px;
    height: 50px;
    object-fit: contain;
    border-radius: 8px;
}

.store-icon {
    font-size: 32px;
    color: #2563eb;
}

.store-name {
    font-size: 24px;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.store-nav {
    display: flex;
    gap: 20px;
    align-items: center;
}

.store-nav a {
    color: #6b7280;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
}

.store-nav a:hover {
    color: #2563eb;
}

.store-logo {
    width: 50px;
    height: 50px;
    object-fit: contain;
    border-radius: 8px;
}

.store-name {
    font-size: 24px;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.store-nav {
    display: flex;
    gap: 20px;
}

.store-nav a {
    color: #6b7280;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
}

.store-nav a:hover {
    color: #2563eb;
}

.product-show-main {
    padding: 40px 0;
}

.product-show-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.product-main-image {
    width: 100%;
    height: 500px;
    background: #f9fafb;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 20px;
}

.product-main-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    background: white;
}

.product-thumbnails {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.thumbnail-item {
    width: 80px;
    height: 80px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.2s;
}

.thumbnail-item:hover,
.thumbnail-item.active {
    border-color: #2563eb;
}

.thumbnail-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-placeholder-image {
    width: 100%;
    height: 500px;
    background: #f9fafb;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 48px;
}

.product-title {
    font-size: 32px;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 20px 0;
}

.product-price-section {
    margin-bottom: 30px;
    padding: 20px;
    background: #f9fafb;
    border-radius: 8px;
}

.product-price-main {
    font-size: 36px;
    font-weight: 700;
    color: #059669;
    margin-bottom: 8px;
}

.product-price-supplier {
    font-size: 14px;
    color: #6b7280;
}

.product-description {
    margin-bottom: 30px;
}

.product-description h3 {
    font-size: 20px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 12px;
}

.description-content {
    color: #4b5563;
    line-height: 1.8;
    font-size: 16px;
}

.product-actions {
    margin-bottom: 20px;
}

.add-to-cart-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 10px;
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

/* Styles pour les animations de boutons */
.btn-anim-bounce {
    animation: bounce 1s infinite;
}
.btn-anim-pulse {
    animation: pulse 2s infinite;
}
.btn-anim-shake {
    animation: shake 0.5s infinite;
}
.btn-anim-glow {
    animation: glow 2s infinite;
    box-shadow: 0 0 20px rgba(37, 99, 235, 0.6);
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

@keyframes glow {
    0%, 100% { box-shadow: 0 0 20px rgba(37, 99, 235, 0.6); }
    50% { box-shadow: 0 0 30px rgba(37, 99, 235, 0.9); }
}

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

.btn-primary-large,
.btn-secondary-large {
    width: 100%;
    padding: 15px 30px;
    font-size: 18px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s;
}

.btn-primary-large:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-secondary-large:hover {
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

.product-supplier-info {
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.product-supplier-info a {
    color: #2563eb;
    text-decoration: none;
}

.store-footer {
    background: #1f2937;
    color: white;
    padding: 40px 0 20px;
    margin-top: 60px;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    margin-bottom: 30px;
}

.footer-section h4 {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 15px;
    color: white;
}

.footer-section p {
    color: #9ca3af;
    line-height: 1.6;
}

.footer-section ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-section ul li {
    margin-bottom: 10px;
}

.footer-section ul li a {
    color: #9ca3af;
    text-decoration: none;
    transition: color 0.2s;
}

.footer-section ul li a:hover {
    color: white;
}

.footer-section a {
    color: #60a5fa;
    text-decoration: none;
}

.footer-section a:hover {
    text-decoration: underline;
}

.footer-bottom {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid #374151;
    color: #9ca3af;
}

.social-links {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 20px;
}

.social-link {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #2563eb;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s;
    font-size: 18px;
}

.social-link:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.about-section,
.faq-section {
    padding: 60px 0;
    background: white;
}

.section-title {
    font-size: 32px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 40px;
    color: #1f2937;
}

.about-content {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
    color: #4b5563;
    line-height: 1.8;
    font-size: 16px;
}

.faq-list {
    max-width: 800px;
    margin: 0 auto;
}

.faq-item {
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 15px;
}

.faq-question {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    cursor: pointer;
    transition: background 0.2s;
}

.faq-question:hover {
    background: #f9fafb;
}

.faq-question h3 {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.faq-question i {
    transition: transform 0.3s;
    color: #2563eb;
}

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
    padding: 0 20px;
}

.faq-answer p {
    padding: 20px 0;
    color: #4b5563;
    line-height: 1.8;
    margin: 0;
}

@media (max-width: 968px) {
    .product-show-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }
}
</style>

<script>
function changeMainImage(imageSrc, element) {
    document.getElementById('mainProductImage').src = imageSrc;
    document.querySelectorAll('.thumbnail-item').forEach(item => {
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

// FAQ accordion
document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', function() {
        const answer = this.nextElementSibling;
        const isOpen = answer.style.maxHeight;
        
        // Fermer toutes les autres réponses
        document.querySelectorAll('.faq-answer').forEach(ans => {
            if (ans !== answer) {
                ans.style.maxHeight = null;
                ans.previousElementSibling.querySelector('i').style.transform = 'rotate(0deg)';
            }
        });
        
        // Toggle la réponse actuelle
        if (isOpen) {
            answer.style.maxHeight = null;
            this.querySelector('i').style.transform = 'rotate(0deg)';
        } else {
            answer.style.maxHeight = answer.scrollHeight + 'px';
            this.querySelector('i').style.transform = 'rotate(180deg)';
        }
    });
});
</script>
@endsection

