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
    
    // Couleurs principales (Aurora - couleurs par défaut plus vibrantes)
    $primary = $store->primary_color ?? '#0ea5e9';
    $secondary = $store->secondary_color ?? '#8b5cf6';
    $accent = $store->accent_color ?? '#f97316';
    
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

<div class="aurora-store-page" 
     style="--primary: {{ $primary }};
            --secondary: {{ $secondary }};
            --accent: {{ $accent }};
            --page-bg: {{ $pageBgColor }};
            --product-card-bg: {{ $productSectionBgColor }};
            --product-card-radius: {{ $productSectionRounded ? '24px' : '0px' }};
            --review-card-bg: {{ $reviewsSectionBgColor }};
            --review-card-radius: {{ $reviewsSectionRounded ? '20px' : '0px' }};">
    
    <!-- Effets Aurora en arrière-plan -->
    <div class="aurora-background-effects">
        <div class="aurora-particle"></div>
        <div class="aurora-particle"></div>
        <div class="aurora-particle"></div>
        <div class="aurora-particle"></div>
        <div class="aurora-particle"></div>
    </div>
    
    <!-- Header Aurora avec Glassmorphism -->
    <header class="aurora-header" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
        <div class="container">
            <div class="aurora-header-content">
                <a href="{{ $homeUrl }}" class="aurora-brand">
                    @if($store->logo)
                        <img src="{{ $store->logo }}" alt="{{ $store->name }}" class="aurora-logo">
                        <div class="aurora-logo-glow"></div>
                    @else
                        <div class="aurora-logo-placeholder" style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }}, {{ $accent }});">
                            <i class="fas fa-store"></i>
                            <div class="aurora-logo-glow"></div>
                        </div>
                    @endif
                    <h1 class="aurora-store-name" style="color: {{ $headerNameColor }}; font-family: {{ $headerNameFont }};">
                        <span class="aurora-text-glow">{{ $store->name }}</span>
                    </h1>
                </a>
                
                <button class="aurora-mobile-toggle" onclick="toggleAuroraMenu()" aria-label="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                
                <nav class="aurora-nav" id="auroraNav">
                    @if($showHome)
                    <a href="{{ $homeUrl }}" class="aurora-nav-link" style="color: {{ $headerTextColor }};" onclick="closeAuroraMenu()">
                        <span class="aurora-nav-text">{{ $t['home'] }}</span>
                        <span class="aurora-nav-underline"></span>
                    </a>
                    @endif
                    @if($showProduct)
                    <a href="#products" class="aurora-nav-link" style="color: {{ $headerTextColor }};" onclick="closeAuroraMenu()">
                        <span class="aurora-nav-text">{{ $t['product'] }}</span>
                        <span class="aurora-nav-underline"></span>
                    </a>
                    @endif
                    @if($showAboutNav)
                    <a href="#about" class="aurora-nav-link" style="color: {{ $headerTextColor }};" onclick="closeAuroraMenu()">
                        <span class="aurora-nav-text">{{ $t['about'] }}</span>
                        <span class="aurora-nav-underline"></span>
                    </a>
                    @endif
                    @if($showFaqNav)
                    <a href="#faq" class="aurora-nav-link" style="color: {{ $headerTextColor }};" onclick="closeAuroraMenu()">
                        <span class="aurora-nav-text">{{ $t['faq'] }}</span>
                        <span class="aurora-nav-underline"></span>
                    </a>
                    @endif
                    @if($showTrackOrder)
                    <a href="{{ route('track.order') }}" class="aurora-nav-link" style="color: {{ $headerTextColor }};" onclick="closeAuroraMenu()">
                        <span class="aurora-nav-text">{{ $t['track_order'] }}</span>
                        <span class="aurora-nav-underline"></span>
                    </a>
                    @endif
                    @if($showCartNav)
                    <a href="{{ route('cart') }}" class="aurora-cart-link" style="color: {{ $headerTextColor }};" onclick="closeAuroraMenu()">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="aurora-cart-badge" id="auroraCartCount">{{ session('cart') ? count(session('cart')) : 0 }}</span>
                        <span class="aurora-cart-pulse"></span>
                    </a>
                    @endif
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section Aurora avec effets visuels exceptionnels -->
    <section id="hero" class="aurora-hero">
        <!-- Effets Aurora animés -->
        <div class="aurora-waves">
            <div class="aurora-wave" style="background: linear-gradient(90deg, transparent, {{ $primary }}, transparent);"></div>
            <div class="aurora-wave" style="background: linear-gradient(90deg, transparent, {{ $secondary }}, transparent);"></div>
            <div class="aurora-wave" style="background: linear-gradient(90deg, transparent, {{ $accent }}, transparent);"></div>
        </div>
        
        @if(!empty($bannerImages))
            <div class="aurora-banner-slider" id="auroraBannerSlider">
                @foreach($bannerImages as $index => $bannerImg)
                    <div class="aurora-banner-slide {{ $index === 0 ? 'active' : '' }}" 
                         style="background-image: linear-gradient(135deg, rgba(0,0,0,0.4), rgba(0,0,0,0.3)), url('{{ $bannerImg }}');">
                        <div class="aurora-overlay-glow"></div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="aurora-banner-gradient" style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }}, {{ $accent }}, {{ $primary }});">
                <div class="aurora-gradient-shimmer"></div>
            </div>
        @endif
        
        <div class="aurora-hero-content">
            <div class="aurora-hero-text">
                <div class="aurora-title-wrapper">
                    <h2 class="aurora-hero-title" style="color: {{ $bannerTextColor }}; font-size: {{ $bannerTitleSize }};">
                        <span class="aurora-title-glow">{{ $store->name }}</span>
                    </h2>
                    <div class="aurora-title-underline"></div>
                </div>
                <p class="aurora-hero-subtitle" style="color: {{ $bannerTextColor }};">
                    {{ $store->description ?? 'Découvrez notre collection exclusive' }}
                </p>
                
                @if(!empty($bannerTextsSettings))
                    <div class="aurora-banner-texts" id="auroraBannerTexts">
                        @foreach($bannerTextsSettings as $key => $textData)
                            @if(!empty($textData['text']))
                                <div class="aurora-banner-text-item" 
                                     data-effect="{{ $textData['effect'] ?? 'fade' }}"
                                     style="color: {{ $bannerTextColor }}; font-family: {{ $bannerTextFont }}; font-size: {{ $bannerTextSize }};">
                                    <span class="aurora-text-shimmer">{{ $textData['text'] }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
                
                @if($store->allow_multiple_products && $products->count() > 0)
                    <a href="#products" class="aurora-hero-btn" style="background: linear-gradient(135deg, {{ $btnBgColor }}, {{ $secondary }}); color: {{ $btnTextColor }};">
                        <span>Découvrir nos produits</span>
                        <i class="fas fa-arrow-down"></i>
                        <div class="aurora-btn-glow"></div>
                    </a>
                @elseif($product)
                    <a href="#product-detail" class="aurora-hero-btn" style="background: linear-gradient(135deg, {{ $btnBgColor }}, {{ $secondary }}); color: {{ $btnTextColor }};">
                        <span>Voir le produit</span>
                        <i class="fas fa-arrow-right"></i>
                        <div class="aurora-btn-glow"></div>
                    </a>
                @endif
            </div>
        </div>
        
        @if(!empty($bannerImages) && count($bannerImages) > 1)
            <div class="aurora-banner-dots">
                @foreach($bannerImages as $index => $bannerImg)
                    <button class="aurora-banner-dot {{ $index === 0 ? 'active' : '' }}" 
                            onclick="goToAuroraBannerSlide({{ $index }})" 
                            aria-label="Slide {{ $index + 1 }}">
                        <span class="aurora-dot-glow"></span>
                    </button>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Section Produits -->
    <section id="products" class="aurora-products-section">
        <div class="container">
            @if($products->count() > 0)
                @if($store->allow_multiple_products && !isset($product))
                    {{-- MODE MULTI-PRODUITS : Affichage en grille élégante --}}
                    <div class="aurora-products-header">
                        <h2 class="aurora-section-title">Nos Produits</h2>
                        <p class="aurora-section-subtitle">Découvrez notre sélection exclusive</p>
                    </div>
                    
                    <div class="aurora-products-grid">
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
                            
                            <div class="aurora-product-card">
                                <a href="{{ route('store.product.show', ['slug' => $store->slug, 'id' => $prod->id]) }}" class="aurora-product-link">
                                    <div class="aurora-product-image-wrapper">
                                        @if($mainImage)
                                            <img src="{{ $mainImage }}" alt="{{ $prod->name }}" class="aurora-product-image">
                                        @else
                                            <div class="aurora-product-placeholder">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                        <div class="aurora-product-overlay">
                                            <span class="aurora-product-price" style="background: {{ $btnBgColor }}; color: {{ $btnTextColor }};">
                                                {{ number_format($prod->selling_price, 2) }} {{ $store->currency ?? 'USD' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="aurora-product-info">
                                        <h3 class="aurora-product-name">{{ $prod->name }}</h3>
                                        @if($prod->description)
                                            <p class="aurora-product-desc">{{ Str::limit($prod->description, 80) }}</p>
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
                    
                    <div id="product-detail" class="aurora-product-detail">
                        <div class="aurora-product-detail-grid">
                            {{-- Colonne Images --}}
                            <div class="aurora-product-images">
                                @if(!empty($productImages))
                                    <div class="aurora-main-image-wrapper">
                                        <img src="{{ $productImages[0] }}" 
                                             alt="{{ $product->name }}" 
                                             class="aurora-main-image" 
                                             id="auroraMainImage"
                                             onclick="openAuroraImageModal('{{ $productImages[0] }}')">
                                        <button class="aurora-zoom-btn" onclick="openAuroraImageModal('{{ $productImages[0] }}')" title="Zoomer">
                                            <i class="fas fa-search-plus"></i>
                                        </button>
                                    </div>
                                    
                                    @if(count($productImages) > 1)
                                        <div class="aurora-thumbnails">
                                            @foreach($productImages as $index => $img)
                                                <div class="aurora-thumb-item {{ $index === 0 ? 'active' : '' }}" 
                                                     onclick="changeAuroraImage('{{ $img }}', {{ $index }})">
                                                    <img src="{{ $img }}" alt="Thumbnail {{ $index + 1 }}">
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @else
                                    <div class="aurora-no-image">
                                        <i class="fas fa-image"></i>
                                        <p>Aucune image disponible</p>
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Colonne Informations --}}
                            <div class="aurora-product-details">
                                <h1 class="aurora-product-title">{{ $product->name }}</h1>
                                
                                <div class="aurora-price-section">
                                    <div class="aurora-price-main">
                                        <span class="aurora-price-value" style="color: {{ $primary }};">
                                            {{ number_format($currentPrice, 2) }}
                                        </span>
                                        <span class="aurora-price-currency">{{ $store->currency ?? 'USD' }}</span>
                                    </div>
                                    @if($hasPromotion && $product->selling_price > $currentPrice)
                                        <div class="aurora-price-old">
                                            <span>{{ number_format($product->selling_price, 2) }} {{ $store->currency ?? 'USD' }}</span>
                                            <span class="aurora-discount-badge">
                                                -{{ number_format((($product->selling_price - $currentPrice) / $product->selling_price) * 100, 0) }}%
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="aurora-description-section">
                                    <h3 class="aurora-desc-title">Description</h3>
                                    <div class="aurora-desc-content">
                                        {!! nl2br(e($product->description ?? 'Aucune description disponible.')) !!}
                                    </div>
                                </div>
                                
                                @if($product->status === 'active')
                                    <div class="aurora-quantity-section">
                                        <label class="aurora-quantity-label">Quantité</label>
                                        <div class="aurora-quantity-controls">
                                            <button type="button" class="aurora-qty-btn" onclick="changeAuroraQuantity(-1)">-</button>
                                            <input type="number" 
                                                   id="auroraQuantity" 
                                                   name="quantity" 
                                                   value="1" 
                                                   min="1" 
                                                   class="aurora-qty-input"
                                                   onchange="updateAuroraTotal()">
                                            <button type="button" class="aurora-qty-btn" onclick="changeAuroraQuantity(1)">+</button>
                                        </div>
                                        <div class="aurora-total-display">
                                            <span>Total: </span>
                                            <span id="auroraTotalPrice" style="color: {{ $primary }}; font-weight: 700;">
                                                {{ number_format($currentPrice, 2) }} {{ $store->currency ?? 'USD' }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="aurora-actions">
                                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="aurora-buy-form" onsubmit="event.preventDefault(); addToCartAndCheckoutAurora(this);">
                                            @csrf
                                            <input type="hidden" name="quantity" id="auroraBuyQuantity" value="1">
                                            <button type="submit" class="aurora-btn-primary{{ $btnClass }}" style="background: {{ $btnBgColor }}; color: {{ $btnTextColor }};">
                                                <i class="fas fa-bolt"></i>
                                                {{ $btnText }}
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="aurora-cart-form" onsubmit="updateAuroraCartQuantity(); return true;">
                                            @csrf
                                            <input type="hidden" name="quantity" id="auroraCartQuantity" value="1">
                                            <button type="submit" class="aurora-btn-secondary">
                                                <i class="fas fa-shopping-bag"></i>
                                                Ajouter au panier
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="aurora-unavailable">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Ce produit n'est actuellement pas disponible</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="aurora-empty-products">
                    <i class="fas fa-box-open"></i>
                    <h3>Aucun produit disponible</h3>
                    <p>Ajoutez des produits pour commencer à vendre.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Section Parcours du produit -->
    @if(isset($product) && $product && !$store->allow_multiple_products)
    <section class="aurora-journey-section">
        <div class="container">
            <h2 class="aurora-section-title">{{ $t['product_journey'] }}</h2>
            <div class="aurora-journey-steps">
                <div class="aurora-journey-step">
                    <div class="aurora-step-number" style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});">1</div>
                    <div class="aurora-step-content">
                        <h3>Commande</h3>
                        <p>Passez votre commande en quelques clics</p>
                    </div>
                </div>
                <div class="aurora-journey-step">
                    <div class="aurora-step-number" style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});">2</div>
                    <div class="aurora-step-content">
                        <h3>Confirmation</h3>
                        <p>Recevez une confirmation immédiate</p>
                    </div>
                </div>
                <div class="aurora-journey-step">
                    <div class="aurora-step-number" style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});">3</div>
                    <div class="aurora-step-content">
                        <h3>Expédition</h3>
                        <p>Votre produit est expédié rapidement</p>
                    </div>
                </div>
                <div class="aurora-journey-step">
                    <div class="aurora-step-number" style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});">4</div>
                    <div class="aurora-step-content">
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
    <section class="aurora-why-choose-section">
        <div class="container">
            <h2 class="aurora-section-title">{{ $t['why_choose'] }}</h2>
            <div class="aurora-why-choose-content">
                <p>{{ $whyChooseProduct }}</p>
            </div>
        </div>
    </section>
    @endif

    <!-- Section Avis clients -->
    @if(isset($product) && $product && !$store->allow_multiple_products)
    <section class="aurora-reviews-section" style="background: var(--review-card-bg);">
        <div class="container">
            <h2 class="aurora-section-title">{{ $t['customer_reviews'] }}</h2>
            
            @if($productReviews->count() > 0)
                <div class="aurora-reviews-list">
                    @foreach($productReviews as $review)
                        <div class="aurora-review-card" style="background: var(--review-card-bg); border-radius: var(--review-card-radius);">
                            <div class="aurora-review-header">
                                <div class="aurora-review-author">
                                    <div class="aurora-author-avatar" style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});">
                                        {{ strtoupper(substr($review->customer_name, 0, 1)) }}
                                    </div>
                                    <div class="aurora-author-info">
                                        <h4>{{ $review->customer_name }}</h4>
                                        <div class="aurora-review-rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->rating ? 'active' : '' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <span class="aurora-review-date">{{ $review->created_at->format('d/m/Y') }}</span>
                            </div>
                            <p class="aurora-review-comment">{{ $review->comment }}</p>
                            @if($review->images && is_array($review->images) && count($review->images) > 0)
                                <div class="aurora-review-images">
                                    @foreach(array_slice($review->images, 0, 5) as $reviewImg)
                                        <img src="{{ $reviewImg }}" alt="Avis photo" onclick="openAuroraImageModal('{{ $reviewImg }}')">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="aurora-no-reviews">
                    <i class="fas fa-comments"></i>
                    <p>{{ $t['no_reviews'] }}</p>
                </div>
            @endif
            
            <!-- Formulaire d'avis -->
            <div class="aurora-review-form-wrapper">
                <h3 class="aurora-review-form-title">{{ $t['leave_review'] }}</h3>
                <form action="{{ route('product.review.submit', ['slug' => $store->slug, 'id' => $product->id]) }}" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      class="aurora-review-form">
                    @csrf
                    <div class="aurora-form-row">
                        <div class="aurora-form-group">
                            <label for="elegant_review_name">{{ $t['your_name'] }} *</label>
                            <input type="text" id="elegant_review_name" name="customer_name" required class="aurora-form-input">
                        </div>
                        <div class="aurora-form-group">
                            <label for="elegant_review_email">{{ $t['email_optional'] }}</label>
                            <input type="email" id="elegant_review_email" name="customer_email" class="aurora-form-input">
                        </div>
                    </div>
                    <div class="aurora-form-group">
                        <label>{{ $t['rating'] }} *</label>
                        <div class="aurora-rating-input">
                            @for($i = 5; $i >= 1; $i--)
                                <input type="radio" id="elegant_rating_{{ $i }}" name="rating" value="{{ $i }}" required>
                                <label for="elegant_rating_{{ $i }}" class="aurora-rating-star">
                                    <i class="fas fa-star"></i>
                                </label>
                            @endfor
                        </div>
                    </div>
                    <div class="aurora-form-group">
                        <label for="elegant_review_comment">{{ $t['your_review'] }} *</label>
                        <textarea id="elegant_review_comment" name="comment" required rows="5" class="aurora-form-textarea"></textarea>
                    </div>
                    <div class="aurora-form-group">
                        <label for="elegant_review_images">{{ $t['photos_max'] }}</label>
                        <input type="file" id="elegant_review_images" name="images[]" multiple accept="image/*" class="aurora-form-file">
                        <p class="aurora-form-hint">{{ $t['upload_photos'] }}</p>
                    </div>
                    <button type="submit" class="aurora-btn-submit-review" style="background: {{ $btnBgColor }}; color: {{ $btnTextColor }};">
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
    <section id="about" class="aurora-about-section">
        <div class="container">
            <div class="aurora-about-content">
                <h2 class="aurora-section-title">{{ $t['about_title'] }} {{ $store->name }}</h2>
                <div class="aurora-about-text">
                    <p>{{ $store->description ?? 'Votre boutique de confiance pour des produits de qualité.' }}</p>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Section FAQ -->
    @if($showFaqSection)
    <section id="faq" class="aurora-faq-section">
        <div class="container">
            <h2 class="aurora-section-title">{{ $t['faq_title'] }}</h2>
            <div class="aurora-faq-list">
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
                    <div class="aurora-faq-item">
                        <button class="aurora-faq-question" onclick="toggleAuroraFaq({{ $index }})">
                            <span>{{ $faq['q'] }}</span>
                            <i class="fas fa-chevron-down aurora-faq-icon"></i>
                        </button>
                        <div class="aurora-faq-answer">
                            <p>{{ $faq['a'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Footer Élégant -->
    <footer class="aurora-footer" style="background: {{ $footerBgColor }}; color: {{ $footerTextColor }};">
        <div class="container">
            <div class="aurora-footer-grid">
                <div class="aurora-footer-col">
                    <h4 style="color: {{ $footerTitleColor }};">{{ $store->name }}</h4>
                    <p style="color: {{ $footerTextColor }};">{{ $store->description ?? 'Votre boutique de confiance' }}</p>
                </div>
                <div class="aurora-footer-col">
                    <h4 style="color: {{ $footerTitleColor }};">{{ $t['quick_links'] }}</h4>
                    <ul>
                        @if($showHome)<li><a href="{{ $homeUrl }}" style="color: {{ $footerLinkColor }};">{{ $t['home'] }}</a></li>@endif
                        @if($showProduct)<li><a href="#products" style="color: {{ $footerLinkColor }};">{{ $t['product'] }}</a></li>@endif
                        @if($showAboutSection)<li><a href="#about" style="color: {{ $footerLinkColor }};">{{ $t['about'] }}</a></li>@endif
                        @if($showFaqSection)<li><a href="#faq" style="color: {{ $footerLinkColor }};">{{ $t['faq'] }}</a></li>@endif
                    </ul>
                </div>
                <div class="aurora-footer-col">
                    <h4 style="color: {{ $footerTitleColor }};">{{ $t['contact'] }}</h4>
                    <p style="color: {{ $footerTextColor }};">Email: {{ $footerEmail }}</p>
                </div>
            </div>
            
            <div class="aurora-footer-payment">
                <p style="color: {{ $footerTextColor }};">{{ $t['payment_methods'] }}</p>
                <div class="aurora-payment-icons">
                    <i class="fab fa-cc-visa"></i>
                    <i class="fab fa-cc-mastercard"></i>
                    <i class="fab fa-cc-paypal"></i>
                </div>
            </div>
            
            <div class="aurora-footer-bottom" style="color: {{ $footerTextColor }};">
                <p>&copy; {{ date('Y') }} {{ $store->name }}. {{ $t['rights_reserved'] }}.</p>
            </div>
        </div>
    </footer>
</div>

<!-- Modal Image -->
<div id="auroraImageModal" class="aurora-image-modal" onclick="closeAuroraImageModal()">
    <span class="aurora-modal-close">&times;</span>
    <img class="aurora-modal-image" id="auroraModalImage">
</div>

<style>
:root {
    --primary: {{ $primary }};
    --secondary: {{ $secondary }};
    --accent: {{ $accent }};
}

.aurora-store-page {
    min-height: 100vh;
    background: var(--page-bg);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

body:has(.aurora-store-page) .topbar {
    display: none !important;
}

body:has(.aurora-store-page) .main {
    padding-top: 0 !important;
}

.container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 2rem;
}

/* Header Aurora avec Glassmorphism */
.aurora-header {
    position: sticky;
    top: 0;
    z-index: 1000;
    backdrop-filter: blur(20px) saturate(180%);
    background: rgba(255, 255, 255, 0.1) !important;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    border-bottom: 1px solid rgba(255, 255, 255, 0.18);
    transition: all 0.3s;
}

.aurora-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--primary), var(--secondary), transparent);
    opacity: 0.6;
}

.aurora-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 0;
}

.aurora-brand {
    display: flex;
    align-items: center;
    gap: 1rem;
    text-decoration: none;
    transition: transform 0.2s;
}

.aurora-brand:hover {
    transform: translateY(-2px);
}

.aurora-logo {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    position: relative;
    transition: all 0.3s;
}

.aurora-logo-glow {
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    border-radius: 16px;
    background: linear-gradient(135deg, var(--primary), var(--secondary), var(--accent));
    opacity: 0;
    filter: blur(8px);
    transition: opacity 0.3s;
    z-index: -1;
}

.aurora-brand:hover .aurora-logo-glow {
    opacity: 0.6;
}

.aurora-logo-placeholder {
    width: 50px;
    height: 50px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    position: relative;
    overflow: hidden;
}

.aurora-logo-placeholder::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: auroraShimmer 3s infinite;
}

.aurora-store-name {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    letter-spacing: -0.5px;
    position: relative;
}

.aurora-text-glow {
    text-shadow: 0 0 20px rgba(14, 165, 233, 0.5), 0 0 40px rgba(139, 92, 246, 0.3);
    transition: text-shadow 0.3s;
}

.aurora-brand:hover .aurora-text-glow {
    text-shadow: 0 0 30px rgba(14, 165, 233, 0.8), 0 0 60px rgba(139, 92, 246, 0.5);
}

.aurora-mobile-toggle {
    display: none;
    flex-direction: column;
    gap: 5px;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
}

.aurora-mobile-toggle span {
    width: 25px;
    height: 3px;
    background: {{ $headerTextColor }};
    border-radius: 2px;
    transition: all 0.3s;
}

.aurora-nav {
    display: flex;
    align-items: center;
    gap: 2rem;
    position: relative;
}

.aurora-nav-link {
    position: relative;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.3s;
    padding: 0.5rem 0;
    overflow: hidden;
}

.aurora-nav-text {
    position: relative;
    z-index: 2;
    transition: color 0.3s;
}

.aurora-nav-underline {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    transition: width 0.3s;
    box-shadow: 0 0 10px var(--primary);
}

.aurora-nav-link:hover .aurora-nav-underline {
    width: 100%;
}

.aurora-nav-link:hover .aurora-nav-text {
    color: var(--primary) !important;
    text-shadow: 0 0 10px rgba(14, 165, 233, 0.5);
}

.aurora-cart-link {
    position: relative;
    text-decoration: none;
    font-size: 1.25rem;
    transition: all 0.3s;
}

.aurora-cart-link:hover {
    transform: scale(1.15);
}

.aurora-cart-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: 700;
    box-shadow: 0 0 15px rgba(14, 165, 233, 0.6);
    animation: auroraPulse 2s infinite;
}

.aurora-cart-pulse {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: var(--primary);
    opacity: 0;
    animation: auroraRipple 2s infinite;
}

.aurora-nav-link {
    text-decoration: none;
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.2s;
    position: relative;
    padding: 0.5rem 0;
}

.aurora-nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: {{ $headerNavHoverColor }};
    transition: width 0.3s;
}

.aurora-nav-link:hover::after {
    width: 100%;
}

.aurora-nav-link:hover {
    color: {{ $headerNavHoverColor }} !important;
}

.aurora-cart-link {
    position: relative;
    text-decoration: none;
    font-size: 1.25rem;
    transition: transform 0.2s;
}

.aurora-cart-link:hover {
    transform: scale(1.1);
}

.aurora-cart-badge {
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

/* Animations Aurora */
@keyframes auroraShimmer {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

@keyframes auroraPulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 15px rgba(14, 165, 233, 0.6);
    }
    50% {
        transform: scale(1.1);
        box-shadow: 0 0 25px rgba(14, 165, 233, 1);
    }
}

@keyframes auroraRipple {
    0% {
        transform: scale(1);
        opacity: 0.8;
    }
    100% {
        transform: scale(2.5);
        opacity: 0;
    }
}

@keyframes auroraFloat {
    0%, 100% {
        transform: translate(0, 0) scale(1);
        opacity: 0.5;
    }
    33% {
        transform: translate(50px, -50px) scale(1.2);
        opacity: 0.8;
    }
    66% {
        transform: translate(-30px, 30px) scale(0.9);
        opacity: 0.6;
    }
}

/* Hero Section Aurora */
.aurora-hero {
    position: relative;
    height: 95vh;
    min-height: 700px;
    max-height: 900px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
}

/* Vagues Aurora animées */
.aurora-waves {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
    overflow: hidden;
}

.aurora-wave {
    position: absolute;
    width: 200%;
    height: 200%;
    opacity: 0.3;
    animation: auroraWaveMove 20s infinite linear;
    filter: blur(80px);
}

.aurora-wave:nth-child(1) {
    top: -50%;
    left: -50%;
    animation-delay: 0s;
}

.aurora-wave:nth-child(2) {
    top: -30%;
    right: -50%;
    animation-delay: -7s;
    animation-duration: 25s;
}

.aurora-wave:nth-child(3) {
    bottom: -50%;
    left: -30%;
    animation-delay: -14s;
    animation-duration: 30s;
}

@keyframes auroraWaveMove {
    0% {
        transform: translate(0, 0) rotate(0deg);
    }
    50% {
        transform: translate(50px, 50px) rotate(180deg);
    }
    100% {
        transform: translate(0, 0) rotate(360deg);
    }
}

.aurora-banner-slider {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 2;
}

.aurora-banner-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    opacity: 0;
    transition: opacity 1.5s ease-in-out;
}

.aurora-banner-slide.active {
    opacity: 1;
}

.aurora-overlay-glow {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(ellipse at center, rgba(14, 165, 233, 0.3), transparent 70%);
    animation: auroraGlow 4s ease-in-out infinite alternate;
}

@keyframes auroraGlow {
    0% {
        opacity: 0.3;
        transform: scale(1);
    }
    100% {
        opacity: 0.6;
        transform: scale(1.1);
    }
}

.aurora-banner-gradient {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: 300% 300%;
    animation: auroraGradientShift 12s ease infinite;
    z-index: 2;
}

.aurora-gradient-shimmer {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: auroraShimmerSlide 4s infinite;
}

@keyframes auroraGradientShift {
    0%, 100% { 
        background-position: 0% 50%; 
    }
    25% { 
        background-position: 100% 0%; 
    }
    50% { 
        background-position: 100% 100%; 
    }
    75% { 
        background-position: 0% 100%; 
    }
}

@keyframes auroraShimmerSlide {
    0% {
        left: -100%;
    }
    100% {
        left: 100%;
    }
}

.aurora-hero-content {
    position: relative;
    z-index: 3;
    text-align: center;
    max-width: 900px;
    padding: 2rem;
}

.aurora-title-wrapper {
    position: relative;
    display: inline-block;
}

.aurora-hero-title {
    font-size: 4rem;
    font-weight: 900;
    margin: 0 0 1.5rem 0;
    letter-spacing: -2px;
    animation: auroraTitleFloat 3s ease-in-out infinite;
    position: relative;
}

.aurora-title-glow {
    background: linear-gradient(135deg, {{ $bannerTextColor }}, rgba(255,255,255,0.9), {{ $bannerTextColor }});
    background-size: 200% 200%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: auroraTitleShimmer 3s ease-in-out infinite;
    text-shadow: 0 0 40px rgba(14, 165, 233, 0.8), 0 0 80px rgba(139, 92, 246, 0.6);
    display: inline-block;
}

.aurora-title-underline {
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
    border-radius: 2px;
    box-shadow: 0 0 20px var(--primary), 0 0 40px var(--secondary);
    animation: auroraUnderlineExpand 1s ease-out 0.5s forwards;
}

@keyframes auroraTitleShimmer {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

@keyframes auroraTitleFloat {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

@keyframes auroraUnderlineExpand {
    0% {
        width: 0;
    }
    100% {
        width: 80%;
    }
}

.aurora-hero-subtitle {
    font-size: 1.75rem;
    margin: 2rem 0;
    opacity: 0.95;
    font-weight: 400;
    animation: auroraFadeInUp 0.8s ease-out 0.3s both;
    text-shadow: 0 2px 20px rgba(0,0,0,0.3);
}

.aurora-banner-texts {
    min-height: 60px;
    margin: 2rem 0;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.aurora-banner-text-item {
    position: absolute;
    opacity: 0;
    transition: opacity 0.5s;
    font-weight: 500;
    text-shadow: 1px 1px 5px rgba(0,0,0,0.3);
}

.aurora-banner-text-item.active {
    opacity: 1;
}

.aurora-hero-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1.5rem 3rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 700;
    font-size: 1.2rem;
    position: relative;
    overflow: hidden;
    transition: all 0.3s;
    animation: auroraFadeInUp 0.8s ease-out 0.5s both;
    box-shadow: 0 10px 40px rgba(14, 165, 233, 0.4), 0 0 60px rgba(139, 92, 246, 0.3);
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.aurora-btn-glow {
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.4), transparent 70%);
    animation: auroraBtnShimmer 3s infinite;
    pointer-events: none;
}

.aurora-hero-btn:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 20px 60px rgba(14, 165, 233, 0.6), 0 0 80px rgba(139, 92, 246, 0.5);
}

.aurora-hero-btn:hover .aurora-btn-glow {
    animation: auroraBtnShimmer 1s infinite;
}

@keyframes auroraBtnShimmer {
    0% {
        transform: translate(-100%, -100%) rotate(0deg);
    }
    100% {
        transform: translate(100%, 100%) rotate(360deg);
    }
}

@keyframes auroraFadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.aurora-banner-dots {
    position: absolute;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 0.75rem;
    z-index: 3;
}

.aurora-banner-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    background: transparent;
    cursor: pointer;
    transition: all 0.3s;
    padding: 0;
}

.aurora-banner-dot.active {
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
.aurora-products-section {
    padding: 6rem 0;
    background: var(--page-bg);
}

.aurora-products-header {
    text-align: center;
    margin-bottom: 4rem;
}

.aurora-section-title {
    font-size: 3.5rem;
    font-weight: 900;
    background: linear-gradient(135deg, var(--primary), var(--secondary), var(--accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0 0 1rem 0;
    letter-spacing: -2px;
    position: relative;
    text-align: center;
    animation: auroraTitleShimmer 4s ease-in-out infinite;
}

.aurora-section-title::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 4px;
    background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
    border-radius: 2px;
    box-shadow: 0 0 20px var(--primary);
}

.aurora-section-subtitle {
    font-size: 1.5rem;
    color: #64748b;
    margin: 2rem 0 0 0;
    text-align: center;
    font-weight: 400;
}

.aurora-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 3rem;
    margin-top: 4rem;
}

.aurora-product-card {
    background: var(--product-card-bg);
    border-radius: var(--product-card-radius);
    overflow: hidden;
    position: relative;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    height: 100%;
    display: flex;
    flex-direction: column;
    border: 1px solid rgba(14, 165, 233, 0.2);
    box-shadow: 0 8px 32px rgba(0,0,0,0.1), 0 0 0 0 rgba(14, 165, 233, 0);
}

.aurora-product-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.1), rgba(139, 92, 246, 0.1));
    opacity: 0;
    transition: opacity 0.4s;
    z-index: 1;
    pointer-events: none;
}

.aurora-product-card:hover::before {
    opacity: 1;
}

.aurora-product-card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 20px 60px rgba(14, 165, 233, 0.3), 0 0 80px rgba(139, 92, 246, 0.2), 0 0 0 4px rgba(14, 165, 233, 0.3);
    border-color: rgba(14, 165, 233, 0.5);
}

.aurora-product-link {
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.aurora-product-image-wrapper {
    position: relative;
    width: 100%;
    aspect-ratio: 1;
    overflow: hidden;
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    z-index: 2;
}

.aurora-product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    filter: brightness(1);
}

.aurora-product-card:hover .aurora-product-image {
    transform: scale(1.15) rotate(2deg);
    filter: brightness(1.1);
}

.aurora-product-image-wrapper::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.2), rgba(139, 92, 246, 0.2));
    opacity: 0;
    transition: opacity 0.4s;
    pointer-events: none;
}

.aurora-product-card:hover .aurora-product-image-wrapper::after {
    opacity: 1;
}

.aurora-product-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #cbd5e1;
    font-size: 3rem;
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
}

.aurora-product-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 1.5rem;
    background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
    opacity: 0;
    transition: opacity 0.4s;
    z-index: 3;
}

.aurora-product-card:hover .aurora-product-overlay {
    opacity: 1;
}

.aurora-product-price {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 800;
    font-size: 1.2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    position: relative;
    overflow: hidden;
}

.aurora-product-price::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: auroraShimmer 2s infinite;
}

.aurora-product-info {
    padding: 2rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    z-index: 2;
    position: relative;
}

.aurora-product-name {
    font-size: 1.35rem;
    font-weight: 800;
    background: linear-gradient(135deg, #0f172a, #334155);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0 0 0.75rem 0;
    transition: all 0.3s;
}

.aurora-product-card:hover .aurora-product-name {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.aurora-product-desc {
    font-size: 1rem;
    color: #64748b;
    margin: 0;
    line-height: 1.7;
    flex: 1;
}

/* Product Detail */
.aurora-product-detail {
    padding: 4rem 0;
}

.aurora-product-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: start;
}

.aurora-product-images {
    position: sticky;
    top: 100px;
}

.aurora-main-image-wrapper {
    position: relative;
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}

.aurora-main-image {
    width: 100%;
    aspect-ratio: 1;
    object-fit: cover;
    border-radius: 12px;
    cursor: zoom-in;
    transition: transform 0.3s;
}

.aurora-main-image:hover {
    transform: scale(1.02);
}

.aurora-zoom-btn {
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

.aurora-zoom-btn:hover {
    background: rgba(0,0,0,0.9);
    transform: scale(1.1);
}

.aurora-thumbnails {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0.75rem;
}

.aurora-thumb-item {
    aspect-ratio: 1;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    border: 3px solid transparent;
    transition: all 0.3s;
    opacity: 0.7;
}

.aurora-thumb-item:hover {
    opacity: 1;
    transform: scale(1.05);
}

.aurora-thumb-item.active {
    border-color: var(--primary);
    opacity: 1;
}

.aurora-thumb-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.aurora-no-image {
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

.aurora-no-image i {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.aurora-product-details {
    background: white;
    padding: 3rem;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
}

.aurora-product-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 2rem 0;
    letter-spacing: -1px;
    line-height: 1.2;
}

.aurora-price-section {
    margin-bottom: 2.5rem;
    padding-bottom: 2rem;
    border-bottom: 2px solid #e2e8f0;
}

.aurora-price-main {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.aurora-price-value {
    font-size: 3rem;
    font-weight: 800;
}

.aurora-price-currency {
    font-size: 1.5rem;
    color: #64748b;
    font-weight: 500;
}

.aurora-price-old {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.aurora-price-old span:first-child {
    color: #94a3b8;
    text-decoration: line-through;
    font-size: 1.25rem;
}

.aurora-discount-badge {
    background: #ef4444;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 700;
}

.aurora-description-section {
    margin-bottom: 2.5rem;
}

.aurora-desc-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 1rem 0;
}

.aurora-desc-content {
    color: #475569;
    line-height: 1.8;
    font-size: 1.05rem;
}

.aurora-quantity-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 12px;
}

.aurora-quantity-label {
    display: block;
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.75rem;
}

.aurora-quantity-controls {
    display: inline-flex;
    align-items: center;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    background: white;
    margin-bottom: 1rem;
}

.aurora-qty-btn {
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

.aurora-qty-btn:hover {
    background: #f1f5f9;
    color: var(--primary);
}

.aurora-qty-input {
    width: 80px;
    text-align: center;
    border: none;
    background: transparent;
    color: #0f172a;
    font-weight: 700;
    font-size: 1.1rem;
    padding: 0.5rem;
}

.aurora-total-display {
    font-size: 1.1rem;
    color: #475569;
}

.aurora-actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.aurora-btn-primary,
.aurora-btn-secondary {
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

.aurora-btn-primary {
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.aurora-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
}

.aurora-btn-secondary {
    background: white;
    color: var(--primary);
    border: 2px solid var(--primary);
}

.aurora-btn-secondary:hover {
    background: var(--primary);
    color: white;
}

.aurora-unavailable {
    padding: 1.5rem;
    background: #fef3c7;
    border: 2px solid #fbbf24;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: #92400e;
}

.aurora-empty-products {
    text-align: center;
    padding: 6rem 2rem;
    color: #94a3b8;
}

.aurora-empty-products i {
    font-size: 5rem;
    margin-bottom: 1.5rem;
    display: block;
}

.aurora-empty-products h3 {
    font-size: 1.75rem;
    color: #64748b;
    margin: 0 0 0.5rem 0;
}

/* Journey Section */
.aurora-journey-section {
    padding: 6rem 0;
    background: white;
}

.aurora-journey-steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.aurora-journey-step {
    display: flex;
    gap: 1.5rem;
    align-items: start;
    padding: 2rem;
    background: #f8fafc;
    border-radius: 16px;
    transition: all 0.3s;
}

.aurora-journey-step:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.aurora-step-number {
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

.aurora-step-content h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 0.5rem 0;
}

.aurora-step-content p {
    color: #64748b;
    margin: 0;
    line-height: 1.6;
}

/* Why Choose Section */
.aurora-why-choose-section {
    padding: 6rem 0;
    background: var(--page-bg);
}

.aurora-why-choose-content {
    max-width: 900px;
    margin: 0 auto;
    text-align: center;
    font-size: 1.2rem;
    line-height: 2;
    color: #475569;
}

/* Reviews Section */
.aurora-reviews-section {
    padding: 6rem 0;
}

.aurora-reviews-list {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    margin-bottom: 4rem;
    max-width: 1000px;
    margin-left: auto;
    margin-right: auto;
}

.aurora-review-card {
    padding: 2.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s;
}

.aurora-review-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.aurora-review-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 1.5rem;
}

.aurora-review-author {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.aurora-author-avatar {
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

.aurora-author-info h4 {
    font-size: 1.15rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 0.5rem 0;
}

.aurora-review-rating {
    display: flex;
    gap: 0.25rem;
}

.aurora-review-rating .fa-star {
    color: #e2e8f0;
    font-size: 1rem;
}

.aurora-review-rating .fa-star.active {
    color: #fbbf24;
}

.aurora-review-date {
    color: #94a3b8;
    font-size: 0.9rem;
}

.aurora-review-comment {
    color: #475569;
    line-height: 1.8;
    font-size: 1.05rem;
    margin: 0 0 1rem 0;
}

.aurora-review-images {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-top: 1rem;
}

.aurora-review-images img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.2s;
}

.aurora-review-images img:hover {
    transform: scale(1.1);
}

.aurora-no-reviews {
    text-align: center;
    padding: 4rem 2rem;
    color: #94a3b8;
}

.aurora-no-reviews i {
    font-size: 4rem;
    margin-bottom: 1rem;
    display: block;
}

.aurora-no-reviews p {
    font-size: 1.1rem;
    margin: 0;
}

/* Review Form */
.aurora-review-form-wrapper {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    padding: 3rem;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
}

.aurora-review-form-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 2rem 0;
}

.aurora-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.aurora-form-group {
    margin-bottom: 1.5rem;
}

.aurora-form-group label {
    display: block;
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.aurora-form-input,
.aurora-form-textarea {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.2s;
    font-family: inherit;
}

.aurora-form-input:focus,
.aurora-form-textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.aurora-form-textarea {
    resize: vertical;
    min-height: 120px;
}

.aurora-rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 0.5rem;
}

.aurora-rating-input input[type="radio"] {
    display: none;
}

.aurora-rating-star {
    font-size: 2rem;
    color: #e2e8f0;
    cursor: pointer;
    transition: all 0.2s;
}

.aurora-rating-input input[type="radio"]:checked ~ .aurora-rating-star,
.aurora-rating-input input[type="radio"]:checked + .aurora-rating-star,
.aurora-rating-star:hover,
.aurora-rating-input input[type="radio"]:checked ~ .aurora-rating-star ~ .aurora-rating-star {
    color: #fbbf24;
}

.aurora-form-file {
    width: 100%;
    padding: 0.875rem;
    border: 2px dashed #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
    cursor: pointer;
    transition: all 0.2s;
}

.aurora-form-file:hover {
    border-color: var(--primary);
    background: white;
}

.aurora-form-hint {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0.5rem 0 0 0;
}

.aurora-btn-submit-review {
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

.aurora-btn-submit-review:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
}

/* About Section */
.aurora-about-section {
    padding: 6rem 0;
    background: white;
}

.aurora-about-content {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
}

.aurora-about-text {
    font-size: 1.15rem;
    line-height: 1.9;
    color: #475569;
}

/* FAQ Section */
.aurora-faq-section {
    padding: 6rem 0;
    background: var(--page-bg);
}

.aurora-faq-list {
    max-width: 900px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.aurora-faq-item {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: all 0.3s;
}

.aurora-faq-item:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.aurora-faq-question {
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

.aurora-faq-icon {
    transition: transform 0.3s;
    color: var(--primary);
}

.aurora-faq-item.active .aurora-faq-icon {
    transform: rotate(180deg);
}

.aurora-faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    padding: 0 2rem;
}

.aurora-faq-item.active .aurora-faq-answer {
    max-height: 500px;
    padding: 0 2rem 1.5rem 2rem;
}

.aurora-faq-answer p {
    color: #64748b;
    line-height: 1.8;
    margin: 0;
}

/* Footer */
.aurora-footer {
    padding: 4rem 0 2rem;
    margin-top: 4rem;
}

.aurora-footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 3rem;
    margin-bottom: 3rem;
}

.aurora-footer-col h4 {
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0 0 1rem 0;
}

.aurora-footer-col p {
    line-height: 1.8;
    margin: 0;
}

.aurora-footer-col ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.aurora-footer-col ul li {
    margin-bottom: 0.75rem;
}

.aurora-footer-col ul li a {
    text-decoration: none;
    transition: opacity 0.2s;
}

.aurora-footer-col ul li a:hover {
    opacity: 0.8;
}

.aurora-footer-payment {
    text-align: center;
    padding: 2rem 0;
    border-top: 1px solid rgba(255,255,255,0.1);
    border-bottom: 1px solid rgba(255,255,255,0.1);
    margin-bottom: 2rem;
}

.aurora-footer-payment p {
    margin: 0 0 1rem 0;
    font-weight: 600;
}

.aurora-payment-icons {
    display: flex;
    justify-content: center;
    gap: 2rem;
    font-size: 2.5rem;
    color: rgba(255,255,255,0.7);
}

.aurora-footer-bottom {
    text-align: center;
    padding-top: 2rem;
    font-size: 0.9rem;
}

/* Modal Image */
.aurora-image-modal {
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

.aurora-image-modal.active {
    display: flex;
}

.aurora-modal-image {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
    border-radius: 8px;
}

.aurora-modal-close {
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

.aurora-modal-close:hover {
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
    .aurora-product-detail-grid {
        grid-template-columns: 1fr;
        gap: 3rem;
    }
    
    .aurora-product-images {
        position: static;
    }
    
    .aurora-products-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 2rem;
    }
}

@media (max-width: 768px) {
    .aurora-mobile-toggle {
        display: flex;
    }
    
    .aurora-nav {
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
    
    .aurora-nav.active {
        transform: translateX(0);
    }
    
    .aurora-hero-title {
        font-size: 2.5rem;
    }
    
    .aurora-hero-subtitle {
        font-size: 1.25rem;
    }
    
    .aurora-section-title {
        font-size: 2rem;
    }
    
    .aurora-product-title {
        font-size: 2rem;
    }
    
    .aurora-thumbnails {
        grid-template-columns: repeat(4, 1fr);
    }
    
    .container {
        padding: 0 1.5rem;
    }
}

@media (max-width: 640px) {
    .aurora-hero {
        height: 70vh;
        min-height: 500px;
    }
    
    .aurora-hero-title {
        font-size: 2rem;
    }
    
    .aurora-products-grid {
        grid-template-columns: 1fr;
    }
    
    .aurora-thumbnails {
        grid-template-columns: repeat(3, 1fr);
    }
}
</style>

<script>
// Navigation mobile
function toggleAuroraMenu() {
    const nav = document.getElementById('auroraNav');
    nav.classList.toggle('active');
}

function closeAuroraMenu() {
    const nav = document.getElementById('auroraNav');
    nav.classList.remove('active');
}

// Banner slider
let currentBannerSlide = 0;
const bannerSlides = document.querySelectorAll('.aurora-banner-slide');

function goToAuroraBannerSlide(index) {
    if (bannerSlides[index]) {
        bannerSlides[currentBannerSlide].classList.remove('active');
        currentBannerSlide = index;
        bannerSlides[currentBannerSlide].classList.add('active');
        
        document.querySelectorAll('.aurora-banner-dot').forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
    }
}

if (bannerSlides.length > 1) {
    setInterval(() => {
        const next = (currentBannerSlide + 1) % bannerSlides.length;
        goToAuroraBannerSlide(next);
    }, 5000);
}

// Banner texts animation
const bannerTextItems = document.querySelectorAll('.aurora-banner-text-item');
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
function changeAuroraImage(imageSrc, index) {
    document.getElementById('auroraMainImage').src = imageSrc;
    document.querySelectorAll('.aurora-thumb-item').forEach((thumb, i) => {
        thumb.classList.toggle('active', i === index);
    });
}

function openAuroraImageModal(imageSrc) {
    document.getElementById('auroraModalImage').src = imageSrc;
    document.getElementById('auroraImageModal').classList.add('active');
}

function closeAuroraImageModal() {
    document.getElementById('auroraImageModal').classList.remove('active');
}

// Quantity
function changeAuroraQuantity(delta) {
    const input = document.getElementById('auroraQuantity');
    const current = parseInt(input.value) || 1;
    const newValue = Math.max(1, current + delta);
    input.value = newValue;
    updateAuroraTotal();
    updateAuroraCartQuantity();
}

function updateAuroraTotal() {
    const quantity = parseInt(document.getElementById('auroraQuantity').value) || 1;
    const unitPrice = {{ number_format($currentPrice ?? ($product->selling_price ?? 0), 2, '.', '') }};
    const total = (unitPrice * quantity).toFixed(2);
    document.getElementById('auroraTotalPrice').textContent = total + ' {{ $store->currency ?? "USD" }}';
}

function updateAuroraCartQuantity() {
    const quantity = document.getElementById('auroraQuantity').value;
    document.getElementById('auroraBuyQuantity').value = quantity;
    document.getElementById('auroraCartQuantity').value = quantity;
}

function addToCartAndCheckoutAurora(form) {
    updateAuroraCartQuantity();
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
function toggleAuroraFaq(index) {
    const item = document.querySelectorAll('.aurora-faq-item')[index];
    const isActive = item.classList.contains('active');
    
    document.querySelectorAll('.aurora-faq-item').forEach(faq => {
        faq.classList.remove('active');
    });
    
    if (!isActive) {
        item.classList.add('active');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    updateAuroraTotal();
    updateAuroraCartQuantity();
    
    const quantityInput = document.getElementById('auroraQuantity');
    if (quantityInput) {
        quantityInput.addEventListener('change', () => {
            updateAuroraTotal();
            updateAuroraCartQuantity();
        });
    }
});
</script>

@endsection
