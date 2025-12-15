@extends('layouts.app')

@section('title', $store->name . ' - Boutique en ligne')

@php
    $primary = $store->primary_color ?? '#0ea5e9';
    $secondary = $store->secondary_color ?? '#8b5cf6';
    $accent = $store->accent_color ?? '#f97316';
    $product = $products->first();
    $images = $product
        ? (is_array($product->images) ? $product->images : (is_string($product->images) ? json_decode($product->images, true) : []))
        : [];
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
<div class="store-public-page aurora-template">
    <!-- Header -->
    <header class="store-header aurora-header">
        <div class="container">
            <div class="store-header-content">
                <div class="store-brand">
                    <div class="brand-icon">
                        @if($store->logo)
                            <img src="{{ $store->logo }}" alt="{{ $store->name }}">
                        @else
                            <i class="fas fa-bolt"></i>
                        @endif
                    </div>
                    <div class="brand-texts">
                        <h1 class="store-name">{{ $store->name }}</h1>
                        <p class="store-tagline">{{ $store->description ?? 'Une expérience shopping lumineuse.' }}</p>
                    </div>
                </div>
                <nav class="store-nav">
                    @if($showHome)<a href="#hero" class="nav-link"><i class="fas fa-home"></i> Accueil</a>@endif
                    @if($showProduct)<a href="#product" class="nav-link"><i class="fas fa-cube"></i> Produit</a>@endif
                    @if($showAboutNav)<a href="#about" class="nav-link"><i class="fas fa-info-circle"></i> À propos</a>@endif
                    @if($showFaqNav)<a href="#faq" class="nav-link"><i class="fas fa-question-circle"></i> FAQ</a>@endif
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
    <section id="hero" class="hero-section" @if($productBanner && strpos($productBanner,'http')===0) style="background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.14), transparent 35%), radial-gradient(circle at 80% 0%, rgba(255,255,255,0.12), transparent 30%), linear-gradient(135deg, rgba(0,0,0,0.55), rgba(0,0,0,0.4)), url('{{ $productBanner }}');" @elseif($store->banner && strpos($store->banner,'http')===0) style="background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.14), transparent 35%), radial-gradient(circle at 80% 0%, rgba(255,255,255,0.12), transparent 30%), linear-gradient(135deg, rgba(0,0,0,0.55), rgba(0,0,0,0.4)), url('{{ $store->banner }}');" @endif>
        <div class="container hero-grid">
            <div class="hero-text">
                <p class="hero-kicker"><i class="fas fa-sparkles"></i> Edition Aurora</p>
                <h2 class="hero-title">{{ $store->name }}</h2>
                <p class="hero-subtitle">{{ $store->description ?? 'Un thème néon, animé, pour mettre en avant votre produit.' }}</p>
                <div class="hero-actions">
                    <a href="#product" class="btn-cta">Voir le produit</a>
                    <a href="#about" class="btn-ghost">En savoir plus</a>
                </div>
                <div class="hero-icons">
                    <span><i class="fas fa-shipping-fast"></i> Livraison rapide</span>
                    <span><i class="fas fa-shield-alt"></i> Paiement sécurisé</span>
                    <span><i class="fas fa-headset"></i> Support 24/7</span>
                </div>
            </div>
            <div class="hero-card">
                @if($product)
                    @php $firstImage = !empty($images) ? $images[0] : null; @endphp
                    <div class="hero-product">
                        <div class="hero-product-image">
                            @if($firstImage)
                                <img src="{{ $firstImage }}" alt="{{ $product->name }}">
                            @else
                                <div class="product-placeholder"><i class="fas fa-image"></i></div>
                            @endif
                        </div>
                        <div class="hero-product-info">
                            <h3>{{ $product->name }}</h3>
                            <p>{{ Str::limit($product->description, 90) }}</p>
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
                    <p class="hero-empty">Ajoutez un produit pour alimenter ce thème.</p>
                @endif
            </div>
        </div>
    </section>

    <!-- Produit -->
    <section id="product" class="product-section">
        <div class="container">
            @if($product)
                <div class="product-layout">
                    <div class="product-gallery">
                        @if(!empty($images))
                            <div class="gallery-main" id="auroraMain">
                                @foreach($images as $index => $image)
                                    <img src="{{ $image }}" alt="{{ $product->name }}" class="gallery-image {{ $index === 0 ? 'active' : '' }}" onclick="openAuroraModal('{{ $image }}')">
                                @endforeach
                            </div>
                            @if(count($images) > 1)
                                <div class="gallery-thumbs">
                                    @foreach($images as $index => $image)
                                        <div class="thumb {{ $index === 0 ? 'active' : '' }}" onclick="auroraGo({{ $index }})">
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
                        <div class="badge-pill"><i class="fas fa-star"></i> Sélection</div>
                        <h2 class="product-title">{{ $product->name }}</h2>
                        <div class="price-row">
                            <div class="price-main">
                                <span class="price-amount">{{ number_format($product->selling_price, 2) }}</span>
                                <span class="price-currency">{{ $store->currency ?? 'USD' }}</span>
                            </div>
                            <span class="pill-info"><i class="fas fa-shield-alt"></i> Garantie 30j</span>
                        </div>
                        <div class="product-description">
                            <p>{{ $product->description ?? 'Aucune description disponible.' }}</p>
                        </div>
                        <form action="{{ url('/cart/add/' . $product->id) }}" method="POST" class="buy-form">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn-cta full{{ $btnClass }}">
                                <i class="fas fa-bolt"></i>
                                {{ $btnText }}
                            </button>
                        </form>
                        <div class="product-icons">
                            <span><i class="fas fa-box"></i> Stock vérifié</span>
                            <span><i class="fas fa-sync"></i> Retours simples</span>
                            <span><i class="fas fa-lock"></i> Paiement sécurisé</span>
                        </div>
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
                        ['q' => 'Payer en toute sécurité ?', 'a' => 'Carte bancaire, mobile money et autres méthodes sécurisées.'],
                        ['q' => 'Délais de livraison ?', 'a' => 'Selon votre localisation, en général 7 à 21 jours ouvrés.'],
                        ['q' => 'Retours ?', 'a' => '14 jours pour retourner un produit non utilisé.'],
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
    <footer class="store-footer aurora-footer">
        <div class="container footer-grid">
            <div>
                <h4>{{ $store->name }}</h4>
                <p>{{ $store->description ?? 'Votre boutique de confiance' }}</p>
            </div>
            <div>
                <h4>Liens</h4>
                <ul>
                    @if($showHome)<li><a href="#hero">Accueil</a></li>@endif
                    @if($showProduct)<li><a href="#product">Produit</a></li>@endif
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
<div id="auroraModal" class="image-modal" onclick="closeAuroraModal()">
    <span class="modal-close">&times;</span>
    <img class="modal-content" id="auroraModalImg">
</div>

<style>
:root {
    --primary: {{ $primary }};
    --secondary: {{ $secondary }};
    --accent: {{ $accent }};
}
.store-public-page { background: #0b1021; color: #e5e7eb; }
body:has(.store-public-page) .topbar { display: none !important; }
body:has(.store-public-page) .main { padding-top: 0 !important; }

.container { max-width: 1200px; margin: 0 auto; padding: 0 1.75rem; }
.aurora-header { background: rgba(11,16,33,0.9); backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 1000; padding: 1.1rem 0; box-shadow: 0 12px 30px rgba(0,0,0,0.35); }
.store-header-content { display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
.store-brand { display: flex; gap: 1rem; align-items: center; }
.brand-icon { width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: grid; place-items: center; color: #fff; overflow: hidden; }
.brand-icon img { width: 100%; height: 100%; object-fit: contain; }
.brand-texts h1 { margin: 0; font-size: 1.35rem; font-weight: 800; color: #fff; }
.brand-texts p { margin: 0; color: #cbd5e1; font-size: 0.95rem; }
.store-nav { display: flex; gap: 1.25rem; align-items: center; }
.nav-link { color: #cbd5e1; text-decoration: none; font-weight: 700; display: inline-flex; gap: 0.4rem; align-items: center; }
.nav-link:hover { color: #fff; }
.cart-link { position: relative; }
.cart-count { background: var(--accent); color: #0b1021; width: 22px; height: 22px; border-radius: 50%; display: grid; place-items: center; font-size: 0.8rem; font-weight: 800; }

.hero-section {
    padding: 4.5rem 0;
    background: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.06), transparent 35%), radial-gradient(circle at 80% 0%, rgba(255,255,255,0.08), transparent 30%), linear-gradient(135deg, #0b1021, #111827);
    color: #fff;
}
.hero-grid { display: grid; grid-template-columns: 1.05fr 0.95fr; gap: 2rem; align-items: center; }
.hero-kicker { letter-spacing: 0.08em; text-transform: uppercase; color: #a5b4fc; font-weight: 700; }
.hero-title { margin: 0.5rem 0; font-size: 2.5rem; font-weight: 900; }
.hero-subtitle { margin: 0 0 1.5rem 0; color: #cbd5e1; }
.hero-actions { display: flex; gap: 0.75rem; align-items: center; }
.btn-cta {
    padding: 0.9rem 1.4rem;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: #0b1021;
    border: none;
    border-radius: 12px;
    font-weight: 800;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 18px 45px rgba(0,0,0,0.35);
    cursor: pointer;
}
.btn-cta.full { width: 100%; justify-content: center; }
.btn-cta:hover { filter: brightness(1.05); transform: translateY(-1px); }
.btn-ghost { padding: 0.9rem 1.2rem; border: 1px solid rgba(255,255,255,0.3); color: #fff; border-radius: 12px; text-decoration: none; }
.hero-icons { display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1.25rem; color: #cbd5e1; }
.hero-icons span { display: inline-flex; gap: 0.4rem; align-items: center; background: rgba(255,255,255,0.05); padding: 0.5rem 0.75rem; border-radius: 10px; }
.hero-card { background: #0f172a; border-radius: 16px; padding: 1.5rem; box-shadow: 0 18px 40px rgba(0,0,0,0.35); }
.hero-product { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: center; }
.hero-product-image img { width: 100%; border-radius: 12px; object-fit: cover; box-shadow: 0 12px 30px rgba(0,0,0,0.3); }
.hero-product-info h3 { margin: 0 0 0.4rem 0; font-weight: 900; color: #fff; }
.hero-product-info p { margin: 0 0 0.7rem 0; color: #cbd5e1; }
.hero-price { display: flex; gap: 0.35rem; align-items: baseline; }
.hero-price .price-amount { font-size: 1.75rem; font-weight: 900; color: var(--accent); }
.hero-price .price-currency { color: #cbd5e1; font-weight: 800; }
.hero-empty { margin: 0; color: #e5e7eb; }

.product-section { padding: 4rem 0; background: #0b1021; }
.product-layout { display: grid; grid-template-columns: 1.05fr 0.95fr; gap: 2rem; align-items: center; }
.product-gallery { background: #0f172a; border-radius: 16px; padding: 1.25rem; box-shadow: 0 14px 38px rgba(0,0,0,0.35); }
.gallery-main { position: relative; }
.gallery-image { display: none; width: 100%; max-height: 520px; object-fit: contain; border-radius: 12px; cursor: zoom-in; background: #0b1021; }
.gallery-image.active { display: block; }
.gallery-thumbs { display: flex; gap: 0.6rem; margin-top: 0.9rem; flex-wrap: wrap; }
.thumb { width: 70px; height: 70px; border-radius: 10px; overflow: hidden; border: 2px solid transparent; cursor: pointer; transition: transform 0.2s; }
.thumb:hover { transform: translateY(-2px); }
.thumb.active { border-color: var(--accent); }
.thumb img { width: 100%; height: 100%; object-fit: cover; }
.product-details { background: #0f172a; border-radius: 16px; padding: 1.5rem; box-shadow: 0 14px 38px rgba(0,0,0,0.3); }
.badge-pill { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.4rem 0.8rem; background: rgba(245,158,11,0.15); color: #fbbf24; border-radius: 999px; font-weight: 800; margin-bottom: 0.7rem; }
.product-title { margin: 0 0 0.5rem 0; font-size: 2rem; font-weight: 900; color: #fff; }
.price-row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
.price-main { display: flex; gap: 0.35rem; align-items: baseline; }
.price-amount { font-size: 2rem; font-weight: 900; color: var(--accent); }
.price-currency { color: #cbd5e1; font-weight: 800; }
.pill-info { background: rgba(255,255,255,0.08); padding: 0.45rem 0.75rem; border-radius: 10px; color: #e5e7eb; display: inline-flex; align-items: center; gap: 0.4rem; }
.product-description { color: #cbd5e1; line-height: 1.7; margin: 1rem 0; }
.buy-form .btn-cta { width: 100%; justify-content: center; }
.product-icons { display: flex; gap: 0.75rem; flex-wrap: wrap; color: #cbd5e1; margin-top: 1rem; }
.product-icons span { display: inline-flex; gap: 0.35rem; align-items: center; background: rgba(255,255,255,0.05); padding: 0.45rem 0.75rem; border-radius: 10px; }

.btn-anim-bounce { animation: bounceBtn 1.2s infinite; }
.btn-anim-pulse { animation: pulseBtn 1.6s infinite; }
.btn-anim-shake { animation: shakeBtn 0.9s infinite; }
.btn-anim-glow { animation: glowBtn 1.6s infinite; }
@keyframes bounceBtn { 0%,20%,50%,80%,100%{transform:translateY(0);}40%{transform:translateY(-6px);}60%{transform:translateY(-3px);} }
@keyframes pulseBtn { 0%{box-shadow:0 0 0 0 rgba(255,255,255,0.35);}70%{box-shadow:0 0 0 10px rgba(255,255,255,0);}100%{box-shadow:0 0 0 0 rgba(255,255,255,0);} }
@keyframes shakeBtn { 0%,100%{transform:translateX(0);}20%{transform:translateX(-4px);}40%{transform:translateX(4px);}60%{transform:translateX(-3px);}80%{transform:translateX(3px);} }
@keyframes glowBtn { 0%{box-shadow:0 0 12px rgba(255,255,255,0.6);}50%{box-shadow:0 0 22px rgba(255,255,255,0.9);}100%{box-shadow:0 0 12px rgba(255,255,255,0.6);} }

.about-section, .faq-section { padding: 4rem 0; background: #0b1021; }
.section-title { text-align: center; margin: 0 0 2rem 0; font-size: 2rem; font-weight: 900; color: #fff; }
.about-text { max-width: 800px; margin: 0 auto; text-align: center; color: #cbd5e1; line-height: 1.8; }
.faq-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem; }
.faq-card { background: #0f172a; padding: 1.25rem; border-radius: 12px; box-shadow: 0 10px 26px rgba(0,0,0,0.25); }
.faq-card h3 { margin: 0 0 0.4rem 0; font-size: 1.05rem; color: #fff; }
.faq-card p { margin: 0; color: #cbd5e1; }

.aurora-footer { background: #060814; color: #cbd5e1; padding: 3rem 0 1.5rem; }
.footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; }
.aurora-footer a { color: #cbd5e1; text-decoration: none; }
.aurora-footer a:hover { color: #fff; }
.footer-bottom { text-align: center; margin-top: 1.5rem; color: #94a3b8; }

.product-placeholder { display: grid; place-items: center; min-height: 260px; color: #94a3b8; background: rgba(255,255,255,0.03); border-radius: 12px; }
.product-placeholder.large { min-height: 320px; }

.image-modal { display: none; position: fixed; z-index: 9999; inset: 0; background: rgba(0,0,0,0.9); }
.image-modal .modal-content { margin: auto; display: block; width: 90%; max-width: 1100px; max-height: 90vh; object-fit: contain; }
.modal-close { position: absolute; top: 20px; right: 30px; color: #fff; font-size: 40px; cursor: pointer; }

@media (max-width: 992px) {
    .hero-grid { grid-template-columns: 1fr; }
    .hero-product { grid-template-columns: 1fr; }
    .product-layout { grid-template-columns: 1fr; }
}
@media (max-width: 640px) {
    .store-header-content { flex-direction: column; align-items: flex-start; }
    .store-nav { flex-wrap: wrap; gap: 0.6rem; }
    .hero-title { font-size: 2rem; }
    .product-details, .product-gallery { padding: 1.2rem; }
}
</style>

<script>
let auroraIndex = 0;
const aMain = document.querySelectorAll('.gallery-image');
const aThumbs = document.querySelectorAll('.thumb');
function auroraShow(i){
    if(!aMain.length) return;
    aMain[auroraIndex]?.classList.remove('active');
    aThumbs[auroraIndex]?.classList.remove('active');
    auroraIndex = (i + aMain.length) % aMain.length;
    aMain[auroraIndex].classList.add('active');
    aThumbs[auroraIndex]?.classList.add('active');
}
function auroraGo(i){ auroraShow(i); }
if(aMain.length>1){ setInterval(()=>auroraShow(auroraIndex+1), 6500); }

function openAuroraModal(src){
    const modal = document.getElementById('auroraModal');
    const img = document.getElementById('auroraModalImg');
    modal.style.display = 'block';
    img.src = src;
}
function closeAuroraModal(){
    document.getElementById('auroraModal').style.display = 'none';
}
document.addEventListener('keydown', e => { if(e.key==='Escape') closeAuroraModal(); });
</script>
@endsection

