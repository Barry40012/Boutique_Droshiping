@extends('layouts.app')

@section('title', $store->name . ' - Boutique en ligne')

@php
    $primary = $store->primary_color ?? '#111827';
    $secondary = $store->secondary_color ?? '#1f2937';
    $accent = $store->accent_color ?? '#f59e0b';
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
<div class="store-public-page elegant-template">
    <!-- Header -->
    <header class="store-header elegant-header">
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
                        <p class="store-tagline">{{ $store->description ?? 'Découvrez nos produits.' }}</p>
                    </div>
                </div>
                <nav class="store-nav">
                    @if($showHome)<a href="#hero" class="nav-link">Accueil</a>@endif
                    @if($showProduct)<a href="#product" class="nav-link">Produit</a>@endif
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
    <section id="hero" class="hero-section" @if($productBanner && strpos($productBanner,'http')===0) style="background-image: linear-gradient(120deg, rgba(0,0,0,0.55), rgba(0,0,0,0.35)), url('{{ $productBanner }}');" @elseif($store->banner && strpos($store->banner,'http')===0) style="background-image: linear-gradient(120deg, rgba(0,0,0,0.55), rgba(0,0,0,0.35)), url('{{ $store->banner }}');" @endif>
        <div class="container hero-grid">
            <div class="hero-text">
                <p class="hero-kicker">Collection exclusive</p>
                <h2 class="hero-title">{{ $store->name }}</h2>
                <p class="hero-subtitle">{{ $store->description ?? 'Un design sobre et élégant pour valoriser votre offre.' }}</p>
                <div class="hero-actions">
                    <a href="#product" class="btn-cta">Découvrir le produit</a>
                    <a href="#about" class="btn-ghost">À propos</a>
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

    <!-- Produit principal -->
    <section id="product" class="product-section">
        <div class="container">
            @if($product)
                <div class="product-layout">
                    <div class="product-media">
                        @if(!empty($images))
                            <div class="main-visual">
                                <div class="main-slide" id="elegantMain">
                                    @foreach($images as $index => $image)
                                        <img src="{{ $image }}" alt="{{ $product->name }}" class="main-image {{ $index === 0 ? 'active' : '' }}" onclick="openElegantModal('{{ $image }}')">
                                    @endforeach
                                </div>
                            </div>
                            @if(count($images) > 1)
                                <div class="thumbs">
                                    @foreach($images as $index => $image)
                                        <div class="thumb {{ $index === 0 ? 'active' : '' }}" onclick="elegantGo({{ $index }})">
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
                        <div class="price-row">
                            <div class="price-left">
                                <span class="price-amount">{{ number_format($product->selling_price, 2) }}</span>
                                <span class="price-currency">{{ $store->currency ?? 'USD' }}</span>
                            </div>
                            <div class="badge-pill">Disponible</div>
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
    <footer class="store-footer elegant-footer">
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
<div id="elegantModal" class="image-modal" onclick="closeElegantModal()">
    <span class="modal-close">&times;</span>
    <img class="modal-content" id="elegantModalImg">
</div>

<style>
:root {
    --primary: {{ $primary }};
    --secondary: {{ $secondary }};
    --accent: {{ $accent }};
}
.store-public-page { background: #f9fafb; }
body:has(.store-public-page) .topbar { display: none !important; }
body:has(.store-public-page) .main { padding-top: 0 !important; }

.elegant-header { background: #fff; padding: 1.25rem 0; box-shadow: 0 6px 20px rgba(0,0,0,0.06); position: sticky; top: 0; z-index: 1000; }
.store-header-content { display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
.store-brand { display: flex; gap: 1rem; align-items: center; }
.store-logo { width: 56px; height: 56px; border-radius: 12px; object-fit: contain; }
.store-icon { font-size: 2.25rem; color: var(--primary); }
.brand-texts h1 { margin: 0; font-size: 1.35rem; font-weight: 800; color: #0f172a; }
.brand-texts p { margin: 0; color: #6b7280; }
.store-nav { display: flex; gap: 1.25rem; align-items: center; }
.nav-link { text-decoration: none; color: #475569; font-weight: 600; }
.nav-link:hover { color: var(--primary); }
.cart-link { display: flex; align-items: center; gap: 0.4rem; }
.cart-count { background: var(--primary); color: #fff; width: 22px; height: 22px; border-radius: 50%; display: grid; place-items: center; font-size: 0.8rem; }

.hero-section {
    padding: 4rem 0;
    background: linear-gradient(135deg, var(--secondary), #0f172a);
    color: #fff;
}
.hero-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: center; }
.hero-text .hero-kicker { text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700; opacity: 0.8; }
.hero-title { margin: 0.6rem 0; font-size: 2.4rem; font-weight: 800; }
.hero-subtitle { margin: 0 0 1.4rem 0; color: rgba(255,255,255,0.85); font-size: 1.05rem; }
.hero-actions { display: flex; gap: 0.75rem; }
.btn-cta {
    padding: 0.85rem 1.35rem;
    background: #fff;
    color: #0f172a;
    border: none;
    border-radius: 10px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    box-shadow: 0 10px 30px rgba(0,0,0,0.12);
}
.btn-cta.full { width: 100%; justify-content: center; }
.btn-cta:hover { transform: translateY(-2px); }
.btn-ghost { padding: 0.85rem 1.2rem; border: 1px solid rgba(255,255,255,0.4); color: #fff; border-radius: 10px; text-decoration: none; }

.hero-card { background: #fff; border-radius: 16px; padding: 1.5rem; box-shadow: 0 15px 40px rgba(0,0,0,0.12); color: #0f172a; }
.hero-product { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: center; }
.hero-product-image img { width: 100%; border-radius: 12px; object-fit: cover; }
.hero-product-info h3 { margin: 0 0 0.4rem 0; font-weight: 800; }
.hero-product-info p { margin: 0 0 0.6rem 0; color: #475569; }
.hero-price { display: flex; gap: 0.35rem; align-items: baseline; }
.hero-price .price-amount { font-size: 1.6rem; font-weight: 800; color: var(--primary); }
.hero-price .price-currency { color: #6b7280; font-weight: 700; }

.product-section { padding: 4rem 0; background: #f9fafb; }
.product-layout { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 2.5rem; align-items: center; }
.main-visual { background: #fff; border-radius: 14px; padding: 1.25rem; box-shadow: 0 12px 30px rgba(0,0,0,0.08); }
.main-slide { position: relative; }
.main-image { display: none; width: 100%; max-height: 520px; object-fit: contain; cursor: zoom-in; }
.main-image.active { display: block; }
.thumbs { display: flex; gap: 0.6rem; margin-top: 0.8rem; flex-wrap: wrap; }
.thumb { width: 70px; height: 70px; border-radius: 10px; overflow: hidden; border: 2px solid transparent; cursor: pointer; }
.thumb.active { border-color: var(--primary); }
.thumb img { width: 100%; height: 100%; object-fit: cover; }
.product-details { background: #fff; border-radius: 14px; padding: 1.75rem; box-shadow: 0 12px 30px rgba(0,0,0,0.05); }
.product-title { margin: 0 0 0.65rem 0; font-size: 2rem; font-weight: 800; color: #0f172a; }
.price-row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
.price-left { display: flex; gap: 0.35rem; align-items: baseline; }
.price-amount { font-size: 2rem; font-weight: 800; color: var(--primary); }
.price-currency { color: #475569; font-weight: 700; }
.badge-pill { padding: 0.35rem 0.75rem; background: rgba(245,158,11,0.12); color: #b45309; border-radius: 999px; font-weight: 700; font-size: 0.9rem; }
.product-description { color: #475569; line-height: 1.7; margin: 1rem 0; }
.buy-form .btn-cta { width: 100%; justify-content: center; }
.btn-anim-bounce { animation: bounceBtn 1.2s infinite; }
.btn-anim-pulse { animation: pulseBtn 1.6s infinite; }
.btn-anim-shake { animation: shakeBtn 0.9s infinite; }
.btn-anim-glow { animation: glowBtn 1.6s infinite; }

@keyframes bounceBtn { 0%,20%,50%,80%,100%{transform:translateY(0);}40%{transform:translateY(-6px);}60%{transform:translateY(-3px);} }
@keyframes pulseBtn { 0%{box-shadow:0 0 0 0 rgba(14,165,233,0.4);}70%{box-shadow:0 0 0 10px rgba(14,165,233,0);}100%{box-shadow:0 0 0 0 rgba(14,165,233,0);} }
@keyframes shakeBtn { 0%,100%{transform:translateX(0);}20%{transform:translateX(-4px);}40%{transform:translateX(4px);}60%{transform:translateX(-3px);}80%{transform:translateX(3px);} }
@keyframes glowBtn { 0%{box-shadow:0 0 10px rgba(14,165,233,0.6);}50%{box-shadow:0 0 22px rgba(14,165,233,0.9);}100%{box-shadow:0 0 10px rgba(14,165,233,0.6);} }

.about-section, .faq-section { padding: 4rem 0; background: #fff; }
.section-title { text-align: center; margin: 0 0 2rem 0; font-size: 2rem; font-weight: 800; color: #0f172a; }
.about-text { max-width: 800px; margin: 0 auto; text-align: center; color: #475569; line-height: 1.8; }
.faq-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem; }
.faq-card { background: #f8fafc; padding: 1.25rem; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.03); }
.faq-card h3 { margin: 0 0 0.4rem 0; font-size: 1.05rem; color: #0f172a; }
.faq-card p { margin: 0; color: #475569; }

.elegant-footer { background: #0f172a; color: #e2e8f0; padding: 3rem 0 1.5rem; }
.footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; }
.elegant-footer a { color: #cbd5e1; text-decoration: none; }
.elegant-footer a:hover { color: #fff; }
.footer-bottom { text-align: center; margin-top: 1.5rem; color: #94a3b8; }

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
    .product-details { padding: 1.25rem; }
}
</style>

<script>
let elegantIndex = 0;
const eMain = document.querySelectorAll('.main-image');
const eThumbs = document.querySelectorAll('.thumb');
function elegantShow(i){
    if(!eMain.length) return;
    eMain[elegantIndex]?.classList.remove('active');
    eThumbs[elegantIndex]?.classList.remove('active');
    elegantIndex = (i + eMain.length) % eMain.length;
    eMain[elegantIndex].classList.add('active');
    eThumbs[elegantIndex]?.classList.add('active');
}
function elegantGo(i){ elegantShow(i); }
if(eMain.length>1){ setInterval(()=>elegantShow(elegantIndex+1), 7000); }

function openElegantModal(src){
    const modal = document.getElementById('elegantModal');
    const img = document.getElementById('elegantModalImg');
    modal.style.display = 'block';
    img.src = src;
}
function closeElegantModal(){
    document.getElementById('elegantModal').style.display = 'none';
}
document.addEventListener('keydown', e => { if(e.key==='Escape') closeElegantModal(); });
</script>
@endsection

