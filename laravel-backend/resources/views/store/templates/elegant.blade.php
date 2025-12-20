@extends('layouts.app')

@section('title', $store->name . ' - Boutique en ligne')

@section('content')
@php
    $settings = $store->settings ?? [];
    
    // Détecter si on est sur une page de détails produit
    $isProductDetailPage = request()->routeIs('store.product.show');
    $homeUrl = $isProductDetailPage ? route('store.public', $store->slug) : '#hero';
    
    // Ne définir $product que si on n'est pas en mode multiproduit et qu'il n'est pas déjà défini (page de détails)
    if (!isset($product) && !$store->allow_multiple_products) {
        $product = $products->first();
    }
    
    // Couleurs principales
    $primary = $store->primary_color ?? '#6366f1';
    $secondary = $store->secondary_color ?? '#8b5cf6';
    $accent = $store->accent_color ?? '#ec4899';
    
    // Personnalisation
    $btnText = $store->button_text ?? 'Acheter maintenant';
    $btnAnim = $store->button_animation ?? 'none';
    $btnClass = $btnAnim !== 'none' ? ' btn-anim-'.$btnAnim : '';
    $btnBgColor = $store->button_bg_color ?? ($store->primary_color ?? '#6366f1');
    $btnTextColor = $store->button_text_color ?? '#ffffff';
    
    $headerBgColor = $store->header_bg_color ?? '#ffffff';
    $headerTextColor = $store->header_text_color ?? '#4b5563';
    $headerNameColor = $store->header_name_color ?? '#1f2937';
    $headerNameFont = $store->header_name_font ?? 'inherit';
    $headerNavHoverColor = $store->header_nav_hover_color ?? ($store->primary_color ?? '#6366f1');
    
    $footerBgColor = $store->footer_bg_color ?? '#0f172a';
    $footerTextColor = $store->footer_text_color ?? '#94a3b8';
    $footerLinkColor = $store->footer_link_color ?? '#ffffff';
    $footerTitleColor = $store->footer_title_color ?? '#ffffff';
    
    $pageBgColor = $store->page_bg_color ?? '#f8fafc';
    
    // Sections produit / avis
    $productSectionBgColor = $store->product_section_bg_color ?? '#ffffff';
    $productSectionRounded = $store->product_section_rounded;
    if ($productSectionRounded === null) {
        $productSectionRounded = true;
    }
    
    $reviewsSectionBgColor = $store->reviews_section_bg_color ?? '#ffffff';
    $reviewsSectionRounded = $store->reviews_section_rounded;
    if ($reviewsSectionRounded === null) {
        $reviewsSectionRounded = true;
    }
    
    // Textes de bannière
    $bannerTextsSettings = $settings['banner_texts'] ?? [];
    $bannerTextColor = $store->banner_text_color ?? '#ffffff';
    $bannerTextFont = $store->banner_text_font ?? 'inherit';
    $bannerTextSize = $store->banner_text_size ?? '1.25rem';
    $bannerTextSpeed = $store->banner_text_speed ?? 5;
    $bannerTitleSize = $store->banner_title_size ?? '3.5rem';
    
    // Images de bannière (Thème Élégant)
    $bannerSettings = $settings;
    $useProductBannerImages = $bannerSettings['vibrant_banner_use_product_images'] ?? true;
    $selectedProductBannerImages = $bannerSettings['vibrant_banner_selected_product_images'] ?? [];
    $customBannerImages = $bannerSettings['vibrant_banner_custom_images'] ?? [];
    
    $bannerImages = [];
    
    // 1) Images issues des produits
    if ($useProductBannerImages) {
        if (!empty($selectedProductBannerImages) && is_array($selectedProductBannerImages)) {
            $bannerImages = array_values(array_filter($selectedProductBannerImages, function($img) {
                return !empty($img) && is_string($img);
            }));
        } else {
            $allProductImages = [];
            
            if ($store->allow_multiple_products) {
                foreach ($products as $prod) {
                    $prodImages = [];
                    if (is_array($prod->images)) {
                        $prodImages = array_values(array_filter($prod->images, function($img) {
                            return !empty($img) && is_string($img);
                        }));
                    }
                    if (empty($prodImages) && $prod->relationLoaded('productImages') && $prod->productImages->count() > 0) {
                        $prodImages = $prod->productImages->pluck('image_url')->toArray();
                    }
                    if (!empty($prodImages)) {
                        $allProductImages = array_merge($allProductImages, $prodImages);
                    }
                }
            } else {
                if ($product) {
                    $prodImages = [];
                    if (is_array($product->images)) {
                        $prodImages = array_values(array_filter($product->images, function($img) {
                            return !empty($img) && is_string($img);
                        }));
                    }
                    if (empty($prodImages) && $product->relationLoaded('productImages') && $product->productImages->count() > 0) {
                        $prodImages = $product->productImages->pluck('image_url')->toArray();
                    }
                    $allProductImages = $prodImages;
                }
            }
            
            $bannerImages = array_values(array_unique($allProductImages));
        }
    }
    
    // 2) Images personnalisées
    if (!empty($customBannerImages) && is_array($customBannerImages)) {
        foreach ($customBannerImages as $img) {
            if (!empty($img) && is_string($img)) {
                $bannerImages[] = $img;
            }
        }
    }
    
    $bannerImages = array_values(array_unique($bannerImages));
    
    $showHome = $settings['nav_home'] ?? true;
    $showProduct = $settings['nav_product'] ?? true;
    $showAboutNav = $settings['nav_about'] ?? true;
    $showFaqNav = $settings['nav_faq'] ?? true;
    $showCartNav = $settings['nav_cart'] ?? true;
    $showTrackOrder = $settings['nav_track_order'] ?? true;
    $showAboutSection = $settings['section_about'] ?? true;
    $showFaqSection = $settings['section_faq'] ?? true;
    $footerEmail = $settings['footer_email'] ?? ($store->user->email ?? 'contact@example.com');
    $faqList = $settings['faq'] ?? [];
    $productReviews = $reviews ?? collect([]);
    $whyChooseProduct = $store->why_choose_product ?? '';
    
    // Traductions
    $language = session('language', 'fr');
    $translations = [
        'fr' => [
            'home' => 'Accueil',
            'product' => 'Produit',
            'about' => 'À propos',
            'faq' => 'FAQ',
            'cart' => 'Panier',
            'track_order' => 'Suivre ma commande',
            'about_title' => 'À propos',
            'faq_title' => 'Questions fréquentes',
            'quick_links' => 'Liens rapides',
            'contact' => 'Contact',
            'payment_methods' => 'Moyens de paiement',
            'rights_reserved' => 'Tous droits réservés',
            'product_journey' => 'Parcours du produit',
            'why_choose' => 'Pourquoi choisir ce produit ?',
            'customer_reviews' => 'Avis clients',
            'leave_review' => 'Donner votre avis',
            'your_name' => 'Votre nom',
            'email_optional' => 'Email (optionnel)',
            'rating' => 'Note',
            'your_review' => 'Votre avis',
            'photos_max' => 'Photos (max 5)',
            'upload_photos' => 'Vous pouvez télécharger jusqu\'à 5 photos',
            'submit_review' => 'Envoyer l\'avis',
            'no_reviews' => 'Aucun avis pour le moment. Soyez le premier à donner votre avis !',
        ],
        'en' => [
            'home' => 'Home',
            'product' => 'Product',
            'about' => 'About',
            'faq' => 'FAQ',
            'cart' => 'Cart',
            'track_order' => 'Track Order',
            'about_title' => 'About',
            'faq_title' => 'Frequently Asked Questions',
            'quick_links' => 'Quick Links',
            'contact' => 'Contact',
            'payment_methods' => 'Accepted Payment Methods',
            'rights_reserved' => 'All rights reserved',
            'product_journey' => 'Product Journey',
            'why_choose' => 'Why Choose This Product?',
            'customer_reviews' => 'Customer Reviews',
            'leave_review' => 'Leave a Review',
            'your_name' => 'Your Name',
            'email_optional' => 'Email (optional)',
            'rating' => 'Rating',
            'your_review' => 'Your Review',
            'photos_max' => 'Photos (max 5)',
            'upload_photos' => 'You can upload up to 5 photos',
            'submit_review' => 'Submit Review',
            'no_reviews' => 'No reviews yet. Be the first to review!',
        ]
    ];
    $t = $translations[$language] ?? $translations['fr'];
@endphp

<div class="elegant-store-page" 
     style="--primary: {{ $primary }};
            --secondary: {{ $secondary }};
            --accent: {{ $accent }};
            --page-bg: {{ $pageBgColor }};
            --product-card-bg: {{ $productSectionBgColor }};
            --product-card-radius: {{ $productSectionRounded ? '20px' : '0px' }};
            --review-card-bg: {{ $reviewsSectionBgColor }};
            --review-card-radius: {{ $reviewsSectionRounded ? '16px' : '0px' }};">
    
    <!-- Header Élégant -->
    <header class="elegant-header" style="background: {{ $headerBgColor }};">
        <div class="container">
            <div class="elegant-header-content">
                <a href="{{ $homeUrl }}" class="elegant-brand">
                    @if($store->logo)
                        <img src="{{ $store->logo }}" alt="{{ $store->name }}" class="elegant-logo">
                    @else
                        <div class="elegant-logo-placeholder" style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});">
                            <i class="fas fa-store"></i>
                        </div>
                    @endif
                    <h1 class="elegant-store-name" style="color: {{ $headerNameColor }}; font-family: {{ $headerNameFont }};">{{ $store->name }}</h1>
                </a>
                
                <button class="elegant-mobile-toggle" onclick="toggleElegantMenu()" aria-label="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                
                <nav class="elegant-nav" id="elegantNav">
                    @if($showHome)
                    <a href="{{ $homeUrl }}" class="elegant-nav-link" style="color: {{ $headerTextColor }};" onclick="closeElegantMenu()">
                        {{ $t['home'] }}
                    </a>
                    @endif
                    @if($showProduct)
                    <a href="#products" class="elegant-nav-link" style="color: {{ $headerTextColor }};" onclick="closeElegantMenu()">
                        {{ $t['product'] }}
                    </a>
                    @endif
                    @if($showAboutNav)
                    <a href="#about" class="elegant-nav-link" style="color: {{ $headerTextColor }};" onclick="closeElegantMenu()">
                        {{ $t['about'] }}
                    </a>
                    @endif
                    @if($showFaqNav)
                    <a href="#faq" class="elegant-nav-link" style="color: {{ $headerTextColor }};" onclick="closeElegantMenu()">
                        {{ $t['faq'] }}
                    </a>
                    @endif
                    @if($showTrackOrder)
                    <a href="{{ route('track.order') }}" class="elegant-nav-link" style="color: {{ $headerTextColor }};" onclick="closeElegantMenu()">
                        {{ $t['track_order'] }}
                    </a>
                    @endif
                    @if($showCartNav)
                    <a href="{{ route('cart') }}" class="elegant-cart-link" style="color: {{ $headerTextColor }};" onclick="closeElegantMenu()">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="elegant-cart-badge" id="elegantCartCount">{{ session('cart') ? count(session('cart')) : 0 }}</span>
                    </a>
                    @endif
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section avec Bannière Élégante -->
    <section id="hero" class="elegant-hero">
        @if(!empty($bannerImages))
            <div class="elegant-banner-slider" id="elegantBannerSlider">
                @foreach($bannerImages as $index => $bannerImg)
                    <div class="elegant-banner-slide {{ $index === 0 ? 'active' : '' }}" 
                         style="background-image: linear-gradient(135deg, rgba(0,0,0,0.3), rgba(0,0,0,0.2)), url('{{ $bannerImg }}');">
                    </div>
                @endforeach
            </div>
        @else
            <div class="elegant-banner-gradient" style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }}, {{ $accent }});">
            </div>
        @endif
        
        <div class="elegant-hero-content">
            <div class="elegant-hero-text">
                <h2 class="elegant-hero-title" style="color: {{ $bannerTextColor }}; font-size: {{ $bannerTitleSize }};">
                    {{ $store->name }}
                </h2>
                <p class="elegant-hero-subtitle" style="color: {{ $bannerTextColor }};">
                    {{ $store->description ?? 'Découvrez notre collection exclusive' }}
                </p>
                
                @if(!empty($bannerTextsSettings))
                    <div class="elegant-banner-texts" id="elegantBannerTexts">
                        @foreach($bannerTextsSettings as $key => $textData)
                            @if(!empty($textData['text']))
                                <div class="elegant-banner-text-item" 
                                     data-effect="{{ $textData['effect'] ?? 'fade' }}"
                                     style="color: {{ $bannerTextColor }}; font-family: {{ $bannerTextFont }}; font-size: {{ $bannerTextSize }};">
                                    {{ $textData['text'] }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
                
                @if($store->allow_multiple_products && $products->count() > 0)
                    <a href="#products" class="elegant-hero-btn" style="background: {{ $btnBgColor }}; color: {{ $btnTextColor }};">
                        <span>Découvrir nos produits</span>
                        <i class="fas fa-arrow-down"></i>
                    </a>
                @elseif($product)
                    <a href="#product-detail" class="elegant-hero-btn" style="background: {{ $btnBgColor }}; color: {{ $btnTextColor }};">
                        <span>Voir le produit</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                @endif
            </div>
        </div>
        
        @if(!empty($bannerImages) && count($bannerImages) > 1)
            <div class="elegant-banner-dots">
                @foreach($bannerImages as $index => $bannerImg)
                    <button class="elegant-banner-dot {{ $index === 0 ? 'active' : '' }}" 
                            onclick="goToBannerSlide({{ $index }})" 
                            aria-label="Slide {{ $index + 1 }}"></button>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Section Produits -->
    <section id="products" class="elegant-products-section">
        <div class="container">
            @if($products->count() > 0)
                @if($store->allow_multiple_products && !isset($product))
                    {{-- MODE MULTI-PRODUITS : Affichage en grille élégante --}}
                    <div class="elegant-products-header">
                        <h2 class="elegant-section-title">Nos Produits</h2>
                        <p class="elegant-section-subtitle">Découvrez notre sélection exclusive</p>
                    </div>
                    
                    <div class="elegant-products-grid">
                        @foreach($products as $prod)
                            @php
                                $prodImages = [];
                                if (is_array($prod->images)) {
                                    $prodImages = array_values(array_filter($prod->images, function($img) {
                                        return !empty($img) && is_string($img);
                                    }));
                                }
                                if (empty($prodImages) && $prod->relationLoaded('productImages') && $prod->productImages->count() > 0) {
                                    $prodImages = $prod->productImages->pluck('image_url')->toArray();
                                }
                                $mainImage = !empty($prodImages) ? $prodImages[0] : null;
                            @endphp
                            
                            <div class="elegant-product-card">
                                <a href="{{ route('store.product.show', ['slug' => $store->slug, 'id' => $prod->id]) }}" class="elegant-product-link">
                                    <div class="elegant-product-image-wrapper">
                                        @if($mainImage)
                                            <img src="{{ $mainImage }}" alt="{{ $prod->name }}" class="elegant-product-image">
                                        @else
                                            <div class="elegant-product-placeholder">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                        <div class="elegant-product-overlay">
                                            <span class="elegant-product-price" style="background: {{ $btnBgColor }}; color: {{ $btnTextColor }};">
                                                {{ number_format($prod->selling_price, 2) }} {{ $store->currency ?? 'USD' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="elegant-product-info">
                                        <h3 class="elegant-product-name">{{ $prod->name }}</h3>
                                        @if($prod->description)
                                            <p class="elegant-product-desc">{{ Str::limit($prod->description, 80) }}</p>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- MODE 1 PRODUIT OU PAGE DÉTAILS : Affichage détaillé --}}
                    @php
                        if (!isset($product)) {
                            $product = $products->first();
                        }
                        
                        $productImages = [];
                        if (is_array($product->images)) {
                            $productImages = array_values(array_filter($product->images, function($img) {
                                return !empty($img) && is_string($img);
                            }));
                        }
                        if (empty($productImages) && $product->relationLoaded('productImages') && $product->productImages->count() > 0) {
                            $productImages = $product->productImages->pluck('image_url')->toArray();
                        }
                        
                        $currentPrice = $product->selling_price;
                        if ($product->promo_price && $product->promo_end_date && now() <= $product->promo_end_date) {
                            $currentPrice = $product->promo_price;
                            $hasPromotion = true;
                        } else {
                            $hasPromotion = false;
                        }
                    @endphp
                    
                    <div id="product-detail" class="elegant-product-detail">
                        <div class="elegant-product-detail-grid">
                            {{-- Colonne Images --}}
                            <div class="elegant-product-images">
                                @if(!empty($productImages))
                                    <div class="elegant-main-image-wrapper">
                                        <img src="{{ $productImages[0] }}" 
                                             alt="{{ $product->name }}" 
                                             class="elegant-main-image" 
                                             id="elegantMainImage"
                                             onclick="openElegantImageModal('{{ $productImages[0] }}')">
                                        <button class="elegant-zoom-btn" onclick="openElegantImageModal('{{ $productImages[0] }}')" title="Zoomer">
                                            <i class="fas fa-search-plus"></i>
                                        </button>
                                    </div>
                                    
                                    @if(count($productImages) > 1)
                                        <div class="elegant-thumbnails">
                                            @foreach($productImages as $index => $img)
                                                <div class="elegant-thumb-item {{ $index === 0 ? 'active' : '' }}" 
                                                     onclick="changeElegantImage('{{ $img }}', {{ $index }})">
                                                    <img src="{{ $img }}" alt="Thumbnail {{ $index + 1 }}">
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @else
                                    <div class="elegant-no-image">
                                        <i class="fas fa-image"></i>
                                        <p>Aucune image disponible</p>
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Colonne Informations --}}
                            <div class="elegant-product-details">
                                <h1 class="elegant-product-title">{{ $product->name }}</h1>
                                
                                <div class="elegant-price-section">
                                    <div class="elegant-price-main">
                                        <span class="elegant-price-value" style="color: {{ $primary }};">
                                            {{ number_format($currentPrice, 2) }}
                                        </span>
                                        <span class="elegant-price-currency">{{ $store->currency ?? 'USD' }}</span>
                                    </div>
                                    @if($hasPromotion && $product->selling_price > $currentPrice)
                                        <div class="elegant-price-old">
                                            <span>{{ number_format($product->selling_price, 2) }} {{ $store->currency ?? 'USD' }}</span>
                                            <span class="elegant-discount-badge">
                                                -{{ number_format((($product->selling_price - $currentPrice) / $product->selling_price) * 100, 0) }}%
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="elegant-description-section">
                                    <h3 class="elegant-desc-title">Description</h3>
                                    <div class="elegant-desc-content">
                                        {!! nl2br(e($product->description ?? 'Aucune description disponible.')) !!}
                                    </div>
                                </div>
                                
                                @if($product->status === 'active')
                                    <div class="elegant-quantity-section">
                                        <label class="elegant-quantity-label">Quantité</label>
                                        <div class="elegant-quantity-controls">
                                            <button type="button" class="elegant-qty-btn" onclick="changeElegantQuantity(-1)">-</button>
                                            <input type="number" 
                                                   id="elegantQuantity" 
                                                   name="quantity" 
                                                   value="1" 
                                                   min="1" 
                                                   class="elegant-qty-input"
                                                   onchange="updateElegantTotal()">
                                            <button type="button" class="elegant-qty-btn" onclick="changeElegantQuantity(1)">+</button>
                                        </div>
                                        <div class="elegant-total-display">
                                            <span>Total: </span>
                                            <span id="elegantTotalPrice" style="color: {{ $primary }}; font-weight: 700;">
                                                {{ number_format($currentPrice, 2) }} {{ $store->currency ?? 'USD' }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="elegant-actions">
                                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="elegant-buy-form" onsubmit="event.preventDefault(); addToCartAndCheckoutElegant(this);">
                                            @csrf
                                            <input type="hidden" name="quantity" id="elegantBuyQuantity" value="1">
                                            <button type="submit" class="elegant-btn-primary{{ $btnClass }}" style="background: {{ $btnBgColor }}; color: {{ $btnTextColor }};">
                                                <i class="fas fa-bolt"></i>
                                                {{ $btnText }}
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="elegant-cart-form" onsubmit="updateElegantCartQuantity(); return true;">
                                            @csrf
                                            <input type="hidden" name="quantity" id="elegantCartQuantity" value="1">
                                            <button type="submit" class="elegant-btn-secondary">
                                                <i class="fas fa-shopping-bag"></i>
                                                Ajouter au panier
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="elegant-unavailable">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Ce produit n'est actuellement pas disponible</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="elegant-empty-products">
                    <i class="fas fa-box-open"></i>
                    <h3>Aucun produit disponible</h3>
                    <p>Ajoutez des produits pour commencer à vendre.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Section Parcours du produit -->
    @if(isset($product) && $product && !$store->allow_multiple_products)
    <section class="elegant-journey-section">
        <div class="container">
            <h2 class="elegant-section-title">{{ $t['product_journey'] }}</h2>
            <div class="elegant-journey-steps">
                <div class="elegant-journey-step">
                    <div class="elegant-step-number" style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});">1</div>
                    <div class="elegant-step-content">
                        <h3>Commande</h3>
                        <p>Passez votre commande en quelques clics</p>
                    </div>
                </div>
                <div class="elegant-journey-step">
                    <div class="elegant-step-number" style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});">2</div>
                    <div class="elegant-step-content">
                        <h3>Confirmation</h3>
                        <p>Recevez une confirmation immédiate</p>
                    </div>
                </div>
                <div class="elegant-journey-step">
                    <div class="elegant-step-number" style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});">3</div>
                    <div class="elegant-step-content">
                        <h3>Expédition</h3>
                        <p>Votre produit est expédié rapidement</p>
                    </div>
                </div>
                <div class="elegant-journey-step">
                    <div class="elegant-step-number" style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});">4</div>
                    <div class="elegant-step-content">
                        <h3>Livraison</h3>
                        <p>Recevez votre produit à domicile</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Section Pourquoi choisir -->
    @if(isset($product) && $product && !empty($whyChooseProduct) && !$store->allow_multiple_products)
    <section class="elegant-why-choose-section">
        <div class="container">
            <h2 class="elegant-section-title">{{ $t['why_choose'] }}</h2>
            <div class="elegant-why-choose-content">
                <p>{{ $whyChooseProduct }}</p>
            </div>
        </div>
    </section>
    @endif

    <!-- Section Avis clients -->
    @if(isset($product) && $product && !$store->allow_multiple_products)
    <section class="elegant-reviews-section" style="background: var(--review-card-bg);">
        <div class="container">
            <h2 class="elegant-section-title">{{ $t['customer_reviews'] }}</h2>
            
            @if($productReviews->count() > 0)
                <div class="elegant-reviews-list">
                    @foreach($productReviews as $review)
                        <div class="elegant-review-card" style="background: var(--review-card-bg); border-radius: var(--review-card-radius);">
                            <div class="elegant-review-header">
                                <div class="elegant-review-author">
                                    <div class="elegant-author-avatar" style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});">
                                        {{ strtoupper(substr($review->customer_name, 0, 1)) }}
                                    </div>
                                    <div class="elegant-author-info">
                                        <h4>{{ $review->customer_name }}</h4>
                                        <div class="elegant-review-rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->rating ? 'active' : '' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <span class="elegant-review-date">{{ $review->created_at->format('d/m/Y') }}</span>
                            </div>
                            <p class="elegant-review-comment">{{ $review->comment }}</p>
                            @if($review->images && is_array($review->images) && count($review->images) > 0)
                                <div class="elegant-review-images">
                                    @foreach(array_slice($review->images, 0, 5) as $reviewImg)
                                        <img src="{{ $reviewImg }}" alt="Avis photo" onclick="openElegantImageModal('{{ $reviewImg }}')">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="elegant-no-reviews">
                    <i class="fas fa-comments"></i>
                    <p>{{ $t['no_reviews'] }}</p>
                </div>
            @endif
            
            <!-- Formulaire d'avis -->
            <div class="elegant-review-form-wrapper">
                <h3 class="elegant-review-form-title">{{ $t['leave_review'] }}</h3>
                <form action="{{ route('product.review.submit', ['slug' => $store->slug, 'id' => $product->id]) }}" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      class="elegant-review-form">
                    @csrf
                    <div class="elegant-form-row">
                        <div class="elegant-form-group">
                            <label for="elegant_review_name">{{ $t['your_name'] }} *</label>
                            <input type="text" id="elegant_review_name" name="customer_name" required class="elegant-form-input">
                        </div>
                        <div class="elegant-form-group">
                            <label for="elegant_review_email">{{ $t['email_optional'] }}</label>
                            <input type="email" id="elegant_review_email" name="customer_email" class="elegant-form-input">
                        </div>
                    </div>
                    <div class="elegant-form-group">
                        <label>{{ $t['rating'] }} *</label>
                        <div class="elegant-rating-input">
                            @for($i = 5; $i >= 1; $i--)
                                <input type="radio" id="elegant_rating_{{ $i }}" name="rating" value="{{ $i }}" required>
                                <label for="elegant_rating_{{ $i }}" class="elegant-rating-star">
                                    <i class="fas fa-star"></i>
                                </label>
                            @endfor
                        </div>
                    </div>
                    <div class="elegant-form-group">
                        <label for="elegant_review_comment">{{ $t['your_review'] }} *</label>
                        <textarea id="elegant_review_comment" name="comment" required rows="5" class="elegant-form-textarea"></textarea>
                    </div>
                    <div class="elegant-form-group">
                        <label for="elegant_review_images">{{ $t['photos_max'] }}</label>
                        <input type="file" id="elegant_review_images" name="images[]" multiple accept="image/*" class="elegant-form-file">
                        <p class="elegant-form-hint">{{ $t['upload_photos'] }}</p>
                    </div>
                    <button type="submit" class="elegant-btn-submit-review" style="background: {{ $btnBgColor }}; color: {{ $btnTextColor }};">
                        <i class="fas fa-paper-plane"></i>
                        {{ $t['submit_review'] }}
                    </button>
                </form>
            </div>
        </div>
    </section>
    @endif

    <!-- Section À propos -->
    @if($showAboutSection)
    <section id="about" class="elegant-about-section">
        <div class="container">
            <div class="elegant-about-content">
                <h2 class="elegant-section-title">{{ $t['about_title'] }} {{ $store->name }}</h2>
                <div class="elegant-about-text">
                    <p>{{ $store->description ?? 'Votre boutique de confiance pour des produits de qualité.' }}</p>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Section FAQ -->
    @if($showFaqSection)
    <section id="faq" class="elegant-faq-section">
        <div class="container">
            <h2 class="elegant-section-title">{{ $t['faq_title'] }}</h2>
            <div class="elegant-faq-list">
                @php
                    $defaultFaqs = [
                        ['q' => 'Quels sont les modes de paiement acceptés ?', 'a' => 'Nous acceptons les cartes bancaires, mobile money et autres méthodes de paiement sécurisées.'],
                        ['q' => 'Quels sont les délais de livraison ?', 'a' => 'Les délais varient selon votre localisation, généralement entre 7 et 21 jours ouvrés.'],
                        ['q' => 'Puis-je retourner un produit ?', 'a' => 'Oui, vous avez 14 jours pour retourner un produit non utilisé dans son emballage d\'origine.'],
                    ];
                    $faqs = [];
                    for($i = 1; $i <= 3; $i++) {
                        $faqs[] = [
                            'q' => $faqList[$i]['q'] ?? $defaultFaqs[$i-1]['q'],
                            'a' => $faqList[$i]['a'] ?? $defaultFaqs[$i-1]['a'],
                        ];
                    }
                @endphp
                @foreach($faqs as $index => $faq)
                    <div class="elegant-faq-item">
                        <button class="elegant-faq-question" onclick="toggleElegantFaq({{ $index }})">
                            <span>{{ $faq['q'] }}</span>
                            <i class="fas fa-chevron-down elegant-faq-icon"></i>
                        </button>
                        <div class="elegant-faq-answer">
                            <p>{{ $faq['a'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Footer Élégant -->
    <footer class="elegant-footer" style="background: {{ $footerBgColor }}; color: {{ $footerTextColor }};">
        <div class="container">
            <div class="elegant-footer-grid">
                <div class="elegant-footer-col">
                    <h4 style="color: {{ $footerTitleColor }};">{{ $store->name }}</h4>
                    <p style="color: {{ $footerTextColor }};">{{ $store->description ?? 'Votre boutique de confiance' }}</p>
                </div>
                <div class="elegant-footer-col">
                    <h4 style="color: {{ $footerTitleColor }};">{{ $t['quick_links'] }}</h4>
                    <ul>
                        @if($showHome)<li><a href="{{ $homeUrl }}" style="color: {{ $footerLinkColor }};">{{ $t['home'] }}</a></li>@endif
                        @if($showProduct)<li><a href="#products" style="color: {{ $footerLinkColor }};">{{ $t['product'] }}</a></li>@endif
                        @if($showAboutSection)<li><a href="#about" style="color: {{ $footerLinkColor }};">{{ $t['about'] }}</a></li>@endif
                        @if($showFaqSection)<li><a href="#faq" style="color: {{ $footerLinkColor }};">{{ $t['faq'] }}</a></li>@endif
                    </ul>
                </div>
                <div class="elegant-footer-col">
                    <h4 style="color: {{ $footerTitleColor }};">{{ $t['contact'] }}</h4>
                    <p style="color: {{ $footerTextColor }};">Email: {{ $footerEmail }}</p>
                </div>
            </div>
            
            <div class="elegant-footer-payment">
                <p style="color: {{ $footerTextColor }};">{{ $t['payment_methods'] }}</p>
                <div class="elegant-payment-icons">
                    <i class="fab fa-cc-visa"></i>
                    <i class="fab fa-cc-mastercard"></i>
                    <i class="fab fa-cc-paypal"></i>
                </div>
            </div>
            
            <div class="elegant-footer-bottom" style="color: {{ $footerTextColor }};">
                <p>&copy; {{ date('Y') }} {{ $store->name }}. {{ $t['rights_reserved'] }}.</p>
            </div>
        </div>
    </footer>
</div>

<!-- Modal Image -->
<div id="elegantImageModal" class="elegant-image-modal" onclick="closeElegantImageModal()">
    <span class="elegant-modal-close">&times;</span>
    <img class="elegant-modal-image" id="elegantModalImage">
</div>

<style>
:root {
    --primary: {{ $primary }};
    --secondary: {{ $secondary }};
    --accent: {{ $accent }};
}

.elegant-store-page {
    min-height: 100vh;
    background: var(--page-bg);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

body:has(.elegant-store-page) .topbar {
    display: none !important;
}

body:has(.elegant-store-page) .main {
    padding-top: 0 !important;
}

.container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 2rem;
}

/* Header Élégant */
.elegant-header {
    position: sticky;
    top: 0;
    z-index: 1000;
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95) !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    transition: all 0.3s;
}

.elegant-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 0;
}

.elegant-brand {
    display: flex;
    align-items: center;
    gap: 1rem;
    text-decoration: none;
    transition: transform 0.2s;
}

.elegant-brand:hover {
    transform: translateY(-2px);
}

.elegant-logo {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.elegant-logo-placeholder {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.elegant-store-name {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    letter-spacing: -0.5px;
}

.elegant-mobile-toggle {
    display: none;
    flex-direction: column;
    gap: 5px;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
}

.elegant-mobile-toggle span {
    width: 25px;
    height: 3px;
    background: {{ $headerTextColor }};
    border-radius: 2px;
    transition: all 0.3s;
}

.elegant-nav {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.elegant-nav-link {
    text-decoration: none;
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.2s;
    position: relative;
    padding: 0.5rem 0;
}

.elegant-nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: {{ $headerNavHoverColor }};
    transition: width 0.3s;
}

.elegant-nav-link:hover::after {
    width: 100%;
}

.elegant-nav-link:hover {
    color: {{ $headerNavHoverColor }} !important;
}

.elegant-cart-link {
    position: relative;
    text-decoration: none;
    font-size: 1.25rem;
    transition: transform 0.2s;
}

.elegant-cart-link:hover {
    transform: scale(1.1);
}

.elegant-cart-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: {{ $primary }};
    color: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: 700;
}

/* Hero Section */
.elegant-hero {
    position: relative;
    height: 90vh;
    min-height: 600px;
    max-height: 800px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.elegant-banner-slider {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.elegant-banner-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    opacity: 0;
    transition: opacity 1s ease-in-out;
}

.elegant-banner-slide.active {
    opacity: 1;
}

.elegant-banner-gradient {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: 200% 200%;
    animation: gradientShift 8s ease infinite;
}

@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.elegant-hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    max-width: 800px;
    padding: 2rem;
}

.elegant-hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    margin: 0 0 1.5rem 0;
    text-shadow: 2px 2px 10px rgba(0,0,0,0.3);
    letter-spacing: -1px;
    animation: fadeInUp 0.8s ease-out;
}

.elegant-hero-subtitle {
    font-size: 1.5rem;
    margin: 0 0 2rem 0;
    opacity: 0.95;
    font-weight: 400;
    animation: fadeInUp 0.8s ease-out 0.2s both;
}

.elegant-banner-texts {
    min-height: 60px;
    margin: 2rem 0;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.elegant-banner-text-item {
    position: absolute;
    opacity: 0;
    transition: opacity 0.5s;
    font-weight: 500;
    text-shadow: 1px 1px 5px rgba(0,0,0,0.3);
}

.elegant-banner-text-item.active {
    opacity: 1;
}

.elegant-hero-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1.25rem 2.5rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    transition: all 0.3s;
    animation: fadeInUp 0.8s ease-out 0.4s both;
}

.elegant-hero-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
}

.elegant-banner-dots {
    position: absolute;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 0.75rem;
    z-index: 3;
}

.elegant-banner-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    background: transparent;
    cursor: pointer;
    transition: all 0.3s;
    padding: 0;
}

.elegant-banner-dot.active {
    background: white;
    transform: scale(1.2);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Section Produits */
.elegant-products-section {
    padding: 6rem 0;
    background: var(--page-bg);
}

.elegant-products-header {
    text-align: center;
    margin-bottom: 4rem;
}

.elegant-section-title {
    font-size: 2.75rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 1rem 0;
    letter-spacing: -1px;
}

.elegant-section-subtitle {
    font-size: 1.25rem;
    color: #64748b;
    margin: 0;
}

.elegant-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2.5rem;
}

.elegant-product-card {
    background: var(--product-card-bg);
    border-radius: var(--product-card-radius);
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.elegant-product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.15);
}

.elegant-product-link {
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.elegant-product-image-wrapper {
    position: relative;
    width: 100%;
    aspect-ratio: 1;
    overflow: hidden;
    background: #f1f5f9;
}

.elegant-product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s;
}

.elegant-product-card:hover .elegant-product-image {
    transform: scale(1.1);
}

.elegant-product-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #cbd5e1;
    font-size: 3rem;
}

.elegant-product-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 1.5rem;
    background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
    opacity: 0;
    transition: opacity 0.3s;
}

.elegant-product-card:hover .elegant-product-overlay {
    opacity: 1;
}

.elegant-product-price {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 700;
    font-size: 1.1rem;
}

.elegant-product-info {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.elegant-product-name {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 0.5rem 0;
}

.elegant-product-desc {
    font-size: 0.95rem;
    color: #64748b;
    margin: 0;
    line-height: 1.6;
    flex: 1;
}

/* Product Detail */
.elegant-product-detail {
    padding: 4rem 0;
}

.elegant-product-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: start;
}

.elegant-product-images {
    position: sticky;
    top: 100px;
}

.elegant-main-image-wrapper {
    position: relative;
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}

.elegant-main-image {
    width: 100%;
    aspect-ratio: 1;
    object-fit: cover;
    border-radius: 12px;
    cursor: zoom-in;
    transition: transform 0.3s;
}

.elegant-main-image:hover {
    transform: scale(1.02);
}

.elegant-zoom-btn {
    position: absolute;
    top: 3rem;
    right: 3rem;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(0,0,0,0.7);
    color: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    transition: all 0.3s;
    backdrop-filter: blur(10px);
}

.elegant-zoom-btn:hover {
    background: rgba(0,0,0,0.9);
    transform: scale(1.1);
}

.elegant-thumbnails {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0.75rem;
}

.elegant-thumb-item {
    aspect-ratio: 1;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    border: 3px solid transparent;
    transition: all 0.3s;
    opacity: 0.7;
}

.elegant-thumb-item:hover {
    opacity: 1;
    transform: scale(1.05);
}

.elegant-thumb-item.active {
    border-color: var(--primary);
    opacity: 1;
}

.elegant-thumb-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.elegant-no-image {
    width: 100%;
    aspect-ratio: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    border-radius: 20px;
    color: #94a3b8;
}

.elegant-no-image i {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.elegant-product-details {
    background: white;
    padding: 3rem;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
}

.elegant-product-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 2rem 0;
    letter-spacing: -1px;
    line-height: 1.2;
}

.elegant-price-section {
    margin-bottom: 2.5rem;
    padding-bottom: 2rem;
    border-bottom: 2px solid #e2e8f0;
}

.elegant-price-main {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.elegant-price-value {
    font-size: 3rem;
    font-weight: 800;
}

.elegant-price-currency {
    font-size: 1.5rem;
    color: #64748b;
    font-weight: 500;
}

.elegant-price-old {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.elegant-price-old span:first-child {
    color: #94a3b8;
    text-decoration: line-through;
    font-size: 1.25rem;
}

.elegant-discount-badge {
    background: #ef4444;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 700;
}

.elegant-description-section {
    margin-bottom: 2.5rem;
}

.elegant-desc-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 1rem 0;
}

.elegant-desc-content {
    color: #475569;
    line-height: 1.8;
    font-size: 1.05rem;
}

.elegant-quantity-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 12px;
}

.elegant-quantity-label {
    display: block;
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.75rem;
}

.elegant-quantity-controls {
    display: inline-flex;
    align-items: center;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    background: white;
    margin-bottom: 1rem;
}

.elegant-qty-btn {
    width: 45px;
    height: 45px;
    border: none;
    background: transparent;
    color: #334155;
    font-size: 1.25rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.elegant-qty-btn:hover {
    background: #f1f5f9;
    color: var(--primary);
}

.elegant-qty-input {
    width: 80px;
    text-align: center;
    border: none;
    background: transparent;
    color: #0f172a;
    font-weight: 700;
    font-size: 1.1rem;
    padding: 0.5rem;
}

.elegant-total-display {
    font-size: 1.1rem;
    color: #475569;
}

.elegant-actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.elegant-btn-primary,
.elegant-btn-secondary {
    width: 100%;
    padding: 1.25rem 2rem;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    transition: all 0.3s;
    border: none;
}

.elegant-btn-primary {
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.elegant-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
}

.elegant-btn-secondary {
    background: white;
    color: var(--primary);
    border: 2px solid var(--primary);
}

.elegant-btn-secondary:hover {
    background: var(--primary);
    color: white;
}

.elegant-unavailable {
    padding: 1.5rem;
    background: #fef3c7;
    border: 2px solid #fbbf24;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: #92400e;
}

.elegant-empty-products {
    text-align: center;
    padding: 6rem 2rem;
    color: #94a3b8;
}

.elegant-empty-products i {
    font-size: 5rem;
    margin-bottom: 1.5rem;
    display: block;
}

.elegant-empty-products h3 {
    font-size: 1.75rem;
    color: #64748b;
    margin: 0 0 0.5rem 0;
}

/* Journey Section */
.elegant-journey-section {
    padding: 6rem 0;
    background: white;
}

.elegant-journey-steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.elegant-journey-step {
    display: flex;
    gap: 1.5rem;
    align-items: start;
    padding: 2rem;
    background: #f8fafc;
    border-radius: 16px;
    transition: all 0.3s;
}

.elegant-journey-step:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.elegant-step-number {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    font-weight: 800;
    flex-shrink: 0;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.elegant-step-content h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 0.5rem 0;
}

.elegant-step-content p {
    color: #64748b;
    margin: 0;
    line-height: 1.6;
}

/* Why Choose Section */
.elegant-why-choose-section {
    padding: 6rem 0;
    background: var(--page-bg);
}

.elegant-why-choose-content {
    max-width: 900px;
    margin: 0 auto;
    text-align: center;
    font-size: 1.2rem;
    line-height: 2;
    color: #475569;
}

/* Reviews Section */
.elegant-reviews-section {
    padding: 6rem 0;
}

.elegant-reviews-list {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    margin-bottom: 4rem;
    max-width: 1000px;
    margin-left: auto;
    margin-right: auto;
}

.elegant-review-card {
    padding: 2.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s;
}

.elegant-review-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.elegant-review-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 1.5rem;
}

.elegant-review-author {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.elegant-author-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.elegant-author-info h4 {
    font-size: 1.15rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 0.5rem 0;
}

.elegant-review-rating {
    display: flex;
    gap: 0.25rem;
}

.elegant-review-rating .fa-star {
    color: #e2e8f0;
    font-size: 1rem;
}

.elegant-review-rating .fa-star.active {
    color: #fbbf24;
}

.elegant-review-date {
    color: #94a3b8;
    font-size: 0.9rem;
}

.elegant-review-comment {
    color: #475569;
    line-height: 1.8;
    font-size: 1.05rem;
    margin: 0 0 1rem 0;
}

.elegant-review-images {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-top: 1rem;
}

.elegant-review-images img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.2s;
}

.elegant-review-images img:hover {
    transform: scale(1.1);
}

.elegant-no-reviews {
    text-align: center;
    padding: 4rem 2rem;
    color: #94a3b8;
}

.elegant-no-reviews i {
    font-size: 4rem;
    margin-bottom: 1rem;
    display: block;
}

.elegant-no-reviews p {
    font-size: 1.1rem;
    margin: 0;
}

/* Review Form */
.elegant-review-form-wrapper {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    padding: 3rem;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
}

.elegant-review-form-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 2rem 0;
}

.elegant-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.elegant-form-group {
    margin-bottom: 1.5rem;
}

.elegant-form-group label {
    display: block;
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.elegant-form-input,
.elegant-form-textarea {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.2s;
    font-family: inherit;
}

.elegant-form-input:focus,
.elegant-form-textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.elegant-form-textarea {
    resize: vertical;
    min-height: 120px;
}

.elegant-rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 0.5rem;
}

.elegant-rating-input input[type="radio"] {
    display: none;
}

.elegant-rating-star {
    font-size: 2rem;
    color: #e2e8f0;
    cursor: pointer;
    transition: all 0.2s;
}

.elegant-rating-input input[type="radio"]:checked ~ .elegant-rating-star,
.elegant-rating-input input[type="radio"]:checked + .elegant-rating-star,
.elegant-rating-star:hover,
.elegant-rating-input input[type="radio"]:checked ~ .elegant-rating-star ~ .elegant-rating-star {
    color: #fbbf24;
}

.elegant-form-file {
    width: 100%;
    padding: 0.875rem;
    border: 2px dashed #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
    cursor: pointer;
    transition: all 0.2s;
}

.elegant-form-file:hover {
    border-color: var(--primary);
    background: white;
}

.elegant-form-hint {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0.5rem 0 0 0;
}

.elegant-btn-submit-review {
    width: 100%;
    padding: 1.25rem 2rem;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    transition: all 0.3s;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.elegant-btn-submit-review:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
}

/* About Section */
.elegant-about-section {
    padding: 6rem 0;
    background: white;
}

.elegant-about-content {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
}

.elegant-about-text {
    font-size: 1.15rem;
    line-height: 1.9;
    color: #475569;
}

/* FAQ Section */
.elegant-faq-section {
    padding: 6rem 0;
    background: var(--page-bg);
}

.elegant-faq-list {
    max-width: 900px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.elegant-faq-item {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: all 0.3s;
}

.elegant-faq-item:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.elegant-faq-question {
    width: 100%;
    padding: 1.5rem 2rem;
    background: transparent;
    border: none;
    text-align: left;
    font-size: 1.1rem;
    font-weight: 600;
    color: #0f172a;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.elegant-faq-icon {
    transition: transform 0.3s;
    color: var(--primary);
}

.elegant-faq-item.active .elegant-faq-icon {
    transform: rotate(180deg);
}

.elegant-faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    padding: 0 2rem;
}

.elegant-faq-item.active .elegant-faq-answer {
    max-height: 500px;
    padding: 0 2rem 1.5rem 2rem;
}

.elegant-faq-answer p {
    color: #64748b;
    line-height: 1.8;
    margin: 0;
}

/* Footer */
.elegant-footer {
    padding: 4rem 0 2rem;
    margin-top: 4rem;
}

.elegant-footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 3rem;
    margin-bottom: 3rem;
}

.elegant-footer-col h4 {
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0 0 1rem 0;
}

.elegant-footer-col p {
    line-height: 1.8;
    margin: 0;
}

.elegant-footer-col ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.elegant-footer-col ul li {
    margin-bottom: 0.75rem;
}

.elegant-footer-col ul li a {
    text-decoration: none;
    transition: opacity 0.2s;
}

.elegant-footer-col ul li a:hover {
    opacity: 0.8;
}

.elegant-footer-payment {
    text-align: center;
    padding: 2rem 0;
    border-top: 1px solid rgba(255,255,255,0.1);
    border-bottom: 1px solid rgba(255,255,255,0.1);
    margin-bottom: 2rem;
}

.elegant-footer-payment p {
    margin: 0 0 1rem 0;
    font-weight: 600;
}

.elegant-payment-icons {
    display: flex;
    justify-content: center;
    gap: 2rem;
    font-size: 2.5rem;
    color: rgba(255,255,255,0.7);
}

.elegant-footer-bottom {
    text-align: center;
    padding-top: 2rem;
    font-size: 0.9rem;
}

/* Modal Image */
.elegant-image-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.95);
    cursor: zoom-out;
    align-items: center;
    justify-content: center;
}

.elegant-image-modal.active {
    display: flex;
}

.elegant-modal-image {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
    border-radius: 8px;
}

.elegant-modal-close {
    position: absolute;
    top: 30px;
    right: 40px;
    color: white;
    font-size: 45px;
    font-weight: bold;
    cursor: pointer;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    transition: all 0.3s;
}

.elegant-modal-close:hover {
    background: rgba(255,255,255,0.2);
    transform: scale(1.1);
}

/* Animations boutons */
.btn-anim-bounce {
    animation: bounceBtn 1.5s infinite;
}

.btn-anim-pulse {
    animation: pulseBtn 2s infinite;
}

.btn-anim-shake {
    animation: shakeBtn 0.5s infinite;
}

.btn-anim-glow {
    animation: glowBtn 2s infinite;
}

@keyframes bounceBtn {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

@keyframes pulseBtn {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

@keyframes shakeBtn {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

@keyframes glowBtn {
    0%, 100% { box-shadow: 0 0 10px var(--primary); }
    50% { box-shadow: 0 0 20px var(--primary), 0 0 30px var(--primary); }
}

/* Responsive */
@media (max-width: 1024px) {
    .elegant-product-detail-grid {
        grid-template-columns: 1fr;
        gap: 3rem;
    }
    
    .elegant-product-images {
        position: static;
    }
    
    .elegant-products-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 2rem;
    }
}

@media (max-width: 768px) {
    .elegant-mobile-toggle {
        display: flex;
    }
    
    .elegant-nav {
        position: fixed;
        top: 80px;
        left: 0;
        right: 0;
        background: white;
        flex-direction: column;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        transform: translateX(-100%);
        transition: transform 0.3s;
        z-index: 999;
    }
    
    .elegant-nav.active {
        transform: translateX(0);
    }
    
    .elegant-hero-title {
        font-size: 2.5rem;
    }
    
    .elegant-hero-subtitle {
        font-size: 1.25rem;
    }
    
    .elegant-section-title {
        font-size: 2rem;
    }
    
    .elegant-product-title {
        font-size: 2rem;
    }
    
    .elegant-thumbnails {
        grid-template-columns: repeat(4, 1fr);
    }
    
    .container {
        padding: 0 1.5rem;
    }
}

@media (max-width: 640px) {
    .elegant-hero {
        height: 70vh;
        min-height: 500px;
    }
    
    .elegant-hero-title {
        font-size: 2rem;
    }
    
    .elegant-products-grid {
        grid-template-columns: 1fr;
    }
    
    .elegant-thumbnails {
        grid-template-columns: repeat(3, 1fr);
    }
}
</style>

<script>
// Navigation mobile
function toggleElegantMenu() {
    const nav = document.getElementById('elegantNav');
    nav.classList.toggle('active');
}

function closeElegantMenu() {
    const nav = document.getElementById('elegantNav');
    nav.classList.remove('active');
}

// Banner slider
let currentBannerSlide = 0;
const bannerSlides = document.querySelectorAll('.elegant-banner-slide');

function goToBannerSlide(index) {
    if (bannerSlides[index]) {
        bannerSlides[currentBannerSlide].classList.remove('active');
        currentBannerSlide = index;
        bannerSlides[currentBannerSlide].classList.add('active');
        
        document.querySelectorAll('.elegant-banner-dot').forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
    }
}

if (bannerSlides.length > 1) {
    setInterval(() => {
        const next = (currentBannerSlide + 1) % bannerSlides.length;
        goToBannerSlide(next);
    }, 5000);
}

// Banner texts animation
const bannerTextItems = document.querySelectorAll('.elegant-banner-text-item');
let currentTextIndex = 0;

if (bannerTextItems.length > 0) {
    bannerTextItems[0].classList.add('active');
    
    const speed = {{ $bannerTextSpeed ?? 5 }};
    const baseDuration = 3000;
    const duration = baseDuration / speed;
    
    setInterval(() => {
        bannerTextItems[currentTextIndex].classList.remove('active');
        currentTextIndex = (currentTextIndex + 1) % bannerTextItems.length;
        bannerTextItems[currentTextIndex].classList.add('active');
    }, duration);
}

// Product images
function changeElegantImage(imageSrc, index) {
    document.getElementById('elegantMainImage').src = imageSrc;
    document.querySelectorAll('.elegant-thumb-item').forEach((thumb, i) => {
        thumb.classList.toggle('active', i === index);
    });
}

function openElegantImageModal(imageSrc) {
    document.getElementById('elegantModalImage').src = imageSrc;
    document.getElementById('elegantImageModal').classList.add('active');
}

function closeElegantImageModal() {
    document.getElementById('elegantImageModal').classList.remove('active');
}

// Quantity
function changeElegantQuantity(delta) {
    const input = document.getElementById('elegantQuantity');
    const current = parseInt(input.value) || 1;
    const newValue = Math.max(1, current + delta);
    input.value = newValue;
    updateElegantTotal();
    updateElegantCartQuantity();
}

function updateElegantTotal() {
    const quantity = parseInt(document.getElementById('elegantQuantity').value) || 1;
    const unitPrice = {{ number_format($currentPrice ?? ($product->selling_price ?? 0), 2, '.', '') }};
    const total = (unitPrice * quantity).toFixed(2);
    document.getElementById('elegantTotalPrice').textContent = total + ' {{ $store->currency ?? "USD" }}';
}

function updateElegantCartQuantity() {
    const quantity = document.getElementById('elegantQuantity').value;
    document.getElementById('elegantBuyQuantity').value = quantity;
    document.getElementById('elegantCartQuantity').value = quantity;
}

function addToCartAndCheckoutElegant(form) {
    updateElegantCartQuantity();
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

// FAQ
function toggleElegantFaq(index) {
    const item = document.querySelectorAll('.elegant-faq-item')[index];
    const isActive = item.classList.contains('active');
    
    document.querySelectorAll('.elegant-faq-item').forEach(faq => {
        faq.classList.remove('active');
    });
    
    if (!isActive) {
        item.classList.add('active');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    updateElegantTotal();
    updateElegantCartQuantity();
    
    const quantityInput = document.getElementById('elegantQuantity');
    if (quantityInput) {
        quantityInput.addEventListener('change', () => {
            updateElegantTotal();
            updateElegantCartQuantity();
        });
    }
});
</script>

@endsection
