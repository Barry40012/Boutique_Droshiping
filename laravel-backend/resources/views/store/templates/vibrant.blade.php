@extends('layouts.app')

@section('title', $store->name . ' - Boutique en ligne')

@php
    $primary = $store->primary_color ?? '#0ea5e9';
    $secondary = $store->secondary_color ?? '#a855f7';
    $accent = $store->accent_color ?? '#f97316';
@endphp

@section('content')
@php
    $settings = $store->settings ?? [];
    $product = $products->first();
    $productBanner = $product->banner ?? null;
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
<div class="store-public-page vibrant-template">
    <!-- Header -->
    <header class="store-header vibrant-header">
        <div class="container">
            <div class="store-header-content">
                <div class="store-brand">
                    @if($store->logo)
                        <img src="{{ $store->logo }}" alt="{{ $store->name }}" class="store-logo">
                    @else
                        <i class="fas fa-store store-icon"></i>
                    @endif
                    <div class="brand-texts">
                        <h1 class="store-name">{{ $store->name }}</h1>
                        @if($store->description)
                            <p class="store-tagline">{{ Str::limit($store->description, 60) }}</p>
                        @endif
                    </div>
                </div>
                <nav class="store-nav">
                    @if($showHome)<a href="#hero" class="nav-link">Accueil</a>@endif
                    @if($showProduct)<a href="#products" class="nav-link">Produit</a>@endif
                    @if($showAboutNav)<a href="#about" class="nav-link">À propos</a>@endif
                    @if($showFaqNav)<a href="#faq" class="nav-link">FAQ</a>@endif
                    @if($showCartNav)
                    <a href="{{ route('cart') }}" class="nav-link cart-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">{{ session('cart') ? count(session('cart')) : 0 }}</span>
                    </a>
                    @endif
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section id="hero" class="hero-section" @if($productBanner && strpos($productBanner,'http')===0) style="background-image: linear-gradient(135deg, rgba(0,0,0,0.45), rgba(0,0,0,0.55)), url('{{ $productBanner }}');" @elseif($store->banner && strpos($store->banner,'http')===0) style="background-image: linear-gradient(135deg, rgba(0,0,0,0.45), rgba(0,0,0,0.55)), url('{{ $store->banner }}');" @endif>
        <div class="container hero-grid">
            <div class="hero-text">
                <p class="badge">Nouveau</p>
                <h2 class="hero-title">Découvrez {{ $store->name }}</h2>
                <p class="hero-subtitle">{{ $store->description ?? 'La boutique idéale pour vos produits favoris.' }}</p>
                <div class="hero-actions">
                    <a href="#products" class="btn-cta">
                        Voir le produit
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="#about" class="btn-ghost">En savoir plus</a>
                </div>
                <div class="hero-stats">
                    <div class="stat">
                        <div class="stat-value">100%</div>
                        <div class="stat-label">Satisfaction</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value">24/7</div>
                        <div class="stat-label">Support</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value">Rapide</div>
                        <div class="stat-label">Livraison</div>
                    </div>
                </div>
            </div>
            <div class="hero-card">
                @php
                    $product = $products->first();
                    $productImages = $product
                        ? (is_array($product->images) ? $product->images : (is_string($product->images) ? json_decode($product->images, true) : []))
                        : [];
                    $firstImage = !empty($productImages) ? $productImages[0] : null;
                @endphp
                @if($product)
                    <div class="hero-product-card">
                        <div class="hero-product-image">
                            @if($firstImage)
                                <img src="{{ $firstImage }}" alt="{{ $product->name }}">
                            @else
                                <div class="product-placeholder"><i class="fas fa-image"></i></div>
                            @endif
                        </div>
                        <div class="hero-product-info">
                            <h3>{{ $product->name }}</h3>
                            <p>{{ Str::limit($product->description, 80) }}</p>
                            <div class="hero-price">
                                <span class="price-amount">{{ number_format($product->selling_price, 2) }}</span>
                                <span class="price-currency">{{ $store->currency ?? 'USD' }}</span>
                            </div>
                            <form action="{{ url('/cart/add/' . $product->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn-cta full{{ $btnClass }}">
                                    <i class="fas fa-shopping-cart"></i>
                                    {{ $btnText }}
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <p class="hero-empty">Ajoutez un produit pour compléter cette section.</p>
                @endif
            </div>
        </div>
    </section>

    <!-- Produit détaillé -->
    <section id="products" class="product-section">
        <div class="container">
            @if($products->count() > 0)
                <div class="product-layout">
                    <div class="product-media">
                        @if(!empty($productImages))
                            <div class="image-slider" id="vibrantSlider">
                                @foreach($productImages as $index => $image)
                                    <div class="slide {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ $image }}" alt="{{ $product->name }}" onclick="openVibrantModal('{{ $image }}')">
                                    </div>
                                @endforeach
                            </div>
                            @if(count($productImages) > 1)
                                <div class="slider-controls">
                                    <button class="ctrl prev" onclick="vibrantChange(-1)"><i class="fas fa-chevron-left"></i></button>
                                    <button class="ctrl next" onclick="vibrantChange(1)"><i class="fas fa-chevron-right"></i></button>
                                </div>
                                <div class="thumbs">
                                    @foreach($productImages as $index => $image)
                                        <div class="thumb {{ $index === 0 ? 'active' : '' }}" onclick="vibrantGo({{ $index }})">
                                            <img src="{{ $image }}" alt="thumb">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="product-placeholder large"><i class="fas fa-image"></i></div>
                        @endif
                    </div>
                    <div class="product-details">
                        <h2 class="product-title">{{ $product->name }}</h2>
                        <div class="product-price-row">
                            <div class="price-main">
                                <span class="price-amount">{{ number_format($product->selling_price, 2) }}</span>
                                <span class="price-currency">{{ $store->currency ?? 'USD' }}</span>
                            </div>
                            <div class="badge-pill">Livraison rapide</div>
                        </div>
                        <div class="product-description">
                            <p>{{ $product->description ?? 'Aucune description disponible.' }}</p>
                        </div>
                        @if($product->status === 'active')
                            <div class="product-single-actions">
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="buy-now-form" onsubmit="event.preventDefault(); addToCartAndCheckout(this);">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-cta full{{ $btnClass }}">
                                        <i class="fas fa-bolt"></i>
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
                                        <i class="fas fa-shopping-cart"></i>
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
            @else
                <div class="empty-products">
                    <i class="fas fa-box-open empty-icon"></i>
                    <p>Aucun produit disponible pour le moment.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- À propos -->
    @if($showAboutSection)
    <section id="about" class="about-section">
        <div class="container">
            <h2 class="section-title">À propos de {{ $store->name }}</h2>
            @if($store->description)
                <p class="about-text">{{ $store->description }}</p>
            @endif
        </div>
    </section>
    @endif

    <!-- FAQ -->
    @if($showFaqSection)
    <section id="faq" class="faq-section">
        <div class="container">
            <h2 class="section-title">Questions fréquentes</h2>
            <div class="faq-grid">
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
                    <div class="faq-card">
                        <h3>{{ $faq['q'] }}</h3>
                        <p>{{ $faq['a'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Footer -->
    <footer class="store-footer vibrant-footer">
        <div class="container footer-grid">
            <div>
                <h4>{{ $store->name }}</h4>
                <p>{{ $store->description ?? 'Votre boutique de confiance' }}</p>
            </div>
            <div>
                <h4>Liens</h4>
                <ul>
                    @if($showHome)<li><a href="#hero">Accueil</a></li>@endif
                    @if($showProduct)<li><a href="#products">Produit</a></li>@endif
                    @if($showFaqSection)<li><a href="#faq">FAQ</a></li>@endif
                </ul>
            </div>
            <div>
                <h4>Contact</h4>
                <p>Email: {{ $footerEmail }}</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} {{ $store->name }}. Tous droits réservés.</p>
        </div>
    </footer>
</div>

<!-- Modal Image -->
<div id="vibrantModal" class="image-modal" onclick="closeVibrantModal()">
    <span class="modal-close">&times;</span>
    <img class="modal-content" id="vibrantModalImg">
</div>

<style>
:root {
    --primary: {{ $primary }};
    --secondary: {{ $secondary }};
    --accent: {{ $accent }};
}

.store-public-page { background: #ffffff; }
body:has(.store-public-page) .topbar { display: none !important; }
body:has(.store-public-page) .main { padding-top: 0 !important; }

.vibrant-header {
    position: sticky;
    top: 0;
    z-index: 1000;
    background: #fff;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    padding: 1.25rem 0;
}
.store-header-content { display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
.store-brand { display: flex; align-items: center; gap: 1rem; }
.store-logo { width: 54px; height: 54px; border-radius: 14px; object-fit: contain; }
.store-icon { font-size: 2.25rem; color: var(--primary); }
.brand-texts { display: flex; flex-direction: column; gap: 0.15rem; }
.store-name { margin: 0; font-size: 1.35rem; font-weight: 800; color: #0f172a; }
.store-tagline { margin: 0; color: #64748b; font-size: 0.95rem; }
.store-nav { display: flex; gap: 1.5rem; align-items: center; }
.nav-link { color: #475569; text-decoration: none; font-weight: 600; }
.nav-link:hover { color: var(--primary); }
.cart-link { display: flex; align-items: center; gap: 0.5rem; }
.cart-count { background: var(--primary); color: #fff; border-radius: 50%; width: 22px; height: 22px; display: grid; place-items: center; font-size: 0.8rem; }

.hero-section {
    background: linear-gradient(120deg, rgba(0,0,0,0.6), rgba(0,0,0,0.35)), linear-gradient(135deg, var(--primary), var(--secondary));
    color: #fff;
    padding: 4rem 0;
}
.hero-grid { display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 2rem; align-items: center; }
.hero-text .badge { display: inline-block; padding: 0.4rem 0.8rem; background: rgba(255,255,255,0.2); border-radius: 999px; font-weight: 600; }
.hero-title { margin: 1rem 0 0.5rem 0; font-size: 2.5rem; font-weight: 800; }
.hero-subtitle { margin: 0 0 1.5rem 0; font-size: 1.1rem; color: rgba(255,255,255,0.9); }
.hero-actions { display: flex; gap: 0.75rem; align-items: center; }
.btn-cta {
    padding: 0.85rem 1.4rem;
    background: #fff;
    color: #0f172a;
    border-radius: 10px;
    border: none;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}
.btn-cta.full { width: 100%; justify-content: center; }
.btn-cta:hover { transform: translateY(-2px); }
.btn-ghost { padding: 0.85rem 1.2rem; border: 1px solid rgba(255,255,255,0.4); color: #fff; border-radius: 10px; text-decoration: none; }
.hero-stats { display: flex; gap: 1.5rem; margin-top: 1.5rem; }
.stat { background: rgba(255,255,255,0.12); padding: 1rem; border-radius: 12px; text-align: center; min-width: 120px; }
.stat-value { font-size: 1.25rem; font-weight: 800; }
.stat-label { color: rgba(255,255,255,0.85); }
.hero-card { background: #fff; border-radius: 16px; padding: 1.5rem; box-shadow: 0 15px 40px rgba(0,0,0,0.15); }
.hero-product-card { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: center; }
.hero-product-image img { width: 100%; border-radius: 12px; object-fit: cover; }
.hero-product-info h3 { margin: 0 0 0.5rem 0; font-weight: 800; }
.hero-product-info p { margin: 0 0 0.75rem 0; color: #475569; }
.hero-price { display: flex; align-items: baseline; gap: 0.35rem; }
.hero-price .price-amount { font-size: 1.75rem; font-weight: 800; color: var(--primary); }
.hero-price .price-currency { color: #64748b; font-weight: 700; }
.hero-empty { margin: 0; color: #0f172a; }

.product-section { padding: 4rem 0; background: #f8fafc; }
.product-layout { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 2.5rem; align-items: center; }
.product-media { position: relative; }
.image-slider { position: relative; width: 100%; border-radius: 14px; overflow: hidden; background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
.slide { display: none; }
.slide.active { display: block; }
.slide img { width: 100%; object-fit: contain; max-height: 520px; background: #fff; }
.slider-controls { position: absolute; top: 50%; left: 0; right: 0; display: flex; justify-content: space-between; padding: 0 0.75rem; transform: translateY(-50%); }
.ctrl { width: 46px; height: 46px; border-radius: 50%; border: none; background: #fff; box-shadow: 0 6px 16px rgba(0,0,0,0.15); cursor: pointer; }
.thumbs { display: flex; gap: 0.5rem; margin-top: 0.75rem; flex-wrap: wrap; }
.thumb { width: 70px; height: 70px; border-radius: 10px; overflow: hidden; border: 2px solid transparent; cursor: pointer; }
.thumb.active { border-color: var(--primary); }
.thumb img { width: 100%; height: 100%; object-fit: cover; }
.product-placeholder { display: grid; place-items: center; min-height: 320px; color: #94a3b8; background: #fff; }
.product-details { background: #fff; border-radius: 14px; padding: 1.75rem; box-shadow: 0 12px 30px rgba(0,0,0,0.05); }
.product-title { margin: 0 0 0.75rem 0; font-size: 2rem; font-weight: 800; color: #0f172a; }
.product-price-row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
.price-main { display: flex; gap: 0.35rem; align-items: baseline; }
.price-amount { font-size: 2rem; font-weight: 800; color: var(--primary); }
.price-currency { color: #475569; font-weight: 700; }
.badge-pill { padding: 0.35rem 0.8rem; background: rgba(14,165,233,0.15); color: var(--primary); border-radius: 999px; font-weight: 700; font-size: 0.9rem; }
.product-description { color: #475569; line-height: 1.7; margin: 1rem 0; }
.buy-form .btn-cta { width: 100%; justify-content: center; }
.btn-anim-bounce { animation: bounceBtn 1.2s infinite; }
.btn-anim-pulse { animation: pulseBtn 1.6s infinite; }
.btn-anim-shake { animation: shakeBtn 0.9s infinite; }
.btn-anim-glow { animation: glowBtn 1.6s infinite; }
@keyframes bounceBtn { 0%,20%,50%,80%,100%{transform:translateY(0);}40%{transform:translateY(-6px);}60%{transform:translateY(-3px);} }
@keyframes pulseBtn { 0%{box-shadow:0 0 0 0 rgba(14,165,233,0.3);}70%{box-shadow:0 0 0 10px rgba(14,165,233,0);}100%{box-shadow:0 0 0 0 rgba(14,165,233,0);} }
@keyframes shakeBtn { 0%,100%{transform:translateX(0);}20%{transform:translateX(-4px);}40%{transform:translateX(4px);}60%{transform:translateX(-3px);}80%{transform:translateX(3px);} }
@keyframes glowBtn { 0%{box-shadow:0 0 12px rgba(14,165,233,0.6);}50%{box-shadow:0 0 22px rgba(14,165,233,0.9);}100%{box-shadow:0 0 12px rgba(14,165,233,0.6);} }

.about-section, .faq-section { padding: 4rem 0; background: #fff; }
.section-title { text-align: center; margin: 0 0 2rem 0; font-size: 2rem; font-weight: 800; color: #0f172a; }
.about-text { max-width: 800px; margin: 0 auto; text-align: center; color: #475569; line-height: 1.8; }
.faq-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem; }
.faq-card { background: #f8fafc; padding: 1.5rem; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.03); }
.faq-card h3 { margin: 0 0 0.5rem 0; font-size: 1.1rem; color: #0f172a; }
.faq-card p { margin: 0; color: #475569; }

.vibrant-footer { background: #0f172a; color: #e2e8f0; padding: 3rem 0 1.5rem; }
.footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; }
.vibrant-footer a { color: #cbd5e1; text-decoration: none; }
.vibrant-footer a:hover { color: #fff; }
.footer-bottom { text-align: center; margin-top: 1.5rem; color: #94a3b8; }

.image-modal { display: none; position: fixed; z-index: 9999; inset: 0; background: rgba(0,0,0,0.9); }
.image-modal .modal-content { margin: auto; display: block; width: 90%; max-width: 1100px; max-height: 90vh; object-fit: contain; }
.modal-close { position: absolute; top: 20px; right: 30px; color: #fff; font-size: 40px; cursor: pointer; }

@media (max-width: 992px) {
    .hero-grid { grid-template-columns: 1fr; }
    .hero-product-card { grid-template-columns: 1fr; }
    .product-layout { grid-template-columns: 1fr; }
}

@media (max-width: 640px) {
    .store-header-content { flex-direction: column; align-items: flex-start; gap: 0.75rem; }
    .store-nav { flex-wrap: wrap; gap: 0.75rem; }
    .hero-title { font-size: 2rem; }
    .product-details { padding: 1.25rem; }
}
</style>

<script>
// Slider
let vibrantIndex = 0;
const vSlides = document.querySelectorAll('#vibrantSlider .slide');
const vThumbs = document.querySelectorAll('.thumb');

function vibrantShow(i) {
    if (!vSlides.length) return;
    vSlides[vibrantIndex]?.classList.remove('active');
    vThumbs[vibrantIndex]?.classList.remove('active');
    vibrantIndex = (i + vSlides.length) % vSlides.length;
    vSlides[vibrantIndex].classList.add('active');
    vThumbs[vibrantIndex]?.classList.add('active');
}
function vibrantChange(dir) { vibrantShow(vibrantIndex + dir); }
function vibrantGo(i) { vibrantShow(i); }
if (vSlides.length > 1) { setInterval(() => vibrantChange(1), 6000); }

// Modal
function openVibrantModal(src) {
    const modal = document.getElementById('vibrantModal');
    const img = document.getElementById('vibrantModalImg');
    modal.style.display = 'block';
    img.src = src;
}
function closeVibrantModal() {
    document.getElementById('vibrantModal').style.display = 'none';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeVibrantModal(); });
</script>
@endsection

