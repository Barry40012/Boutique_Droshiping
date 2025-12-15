@extends('layouts.app')

@section('title', ($product->name ?? 'Produit') . ' - ' . $store->name)

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

    <!-- Section Produit -->
    <section id="product" class="product-section minimal-product">
        <div class="container">
            <div class="product-display">
                <!-- Images du produit avec navigation -->
                <div class="product-images-wrapper">
                    <div class="product-main-image">
                        @if(!empty($productImages) && is_array($productImages))
                            <div class="image-slider" id="imageSlider">
                                @foreach($productImages as $index => $image)
                                    <div class="slide {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ $image }}" alt="{{ $product->name }}" class="product-image" onclick="openImageModal('{{ $image }}')">
                                    </div>
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
                        <span class="price-amount">${{ number_format($product->selling_price, 2) }}</span>
                        <span class="price-currency">{{ $store->currency ?? 'USD' }}</span>
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
    <section id="faq" class="faq-section minimal-faq">
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
    <footer class="store-footer minimal-footer">
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

<!-- Modal pour zoom image -->
<div id="imageModal" class="image-modal" onclick="closeImageModal()">
    <span class="modal-close">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<style>
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

.btn-primary-large {
    background: {{ $store->primary_color ?? '#2563eb' }};
    color: white;
}

.btn-primary-large:hover {
    background: {{ $store->secondary_color ?? '#1d4ed8' }};
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-secondary-large {
    background: white;
    color: {{ $store->primary_color ?? '#2563eb' }};
    border: 2px solid {{ $store->primary_color ?? '#2563eb' }};
}

.btn-secondary-large:hover {
    background: {{ $store->primary_color ?? '#2563eb' }};
    color: white;
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

<script>
// Gestion du slider d'images
let currentImageIndex = 0;
const slides = document.querySelectorAll('.slide');

function changeImage(direction) {
    slides[currentImageIndex].classList.remove('active');
    currentImageIndex += direction;
    if (currentImageIndex < 0) currentImageIndex = slides.length - 1;
    if (currentImageIndex >= slides.length) currentImageIndex = 0;
    slides[currentImageIndex].classList.add('active');
    updateIndicators();
}

function goToImage(index) {
    slides[currentImageIndex].classList.remove('active');
    currentImageIndex = index;
    slides[currentImageIndex].classList.add('active');
    updateIndicators();
}

function updateIndicators() {
    document.querySelectorAll('.indicator').forEach((ind, i) => {
        ind.classList.toggle('active', i === currentImageIndex);
    });
}

// Modal image
function openImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    modal.style.display = 'block';
    modalImg.src = imageSrc;
}

function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
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

