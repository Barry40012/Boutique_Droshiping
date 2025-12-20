@extends('layouts.app')

@section('title', $store->name . ' - Boutique en ligne')

@php
    $primary = $store->primary_color ?? '#3b82f6';
    $secondary = $store->secondary_color ?? '#8b5cf6';
    $accent = $store->accent_color ?? '#10b981';
@endphp

@section('content')
@php
    $settings = $store->settings ?? [];
    // Ne définir $product que si on n'est pas en mode multiproduit
    // En mode multiproduit, $product ne sera défini que sur la page de détails d'un produit
    // Ne pas écraser $product s'il est déjà défini (page de détails)
    if (!isset($product)) {
        if (!$store->allow_multiple_products) {
            $product = $products->first();
        } else {
            $product = null;
        }
    }
    $productBanner = $product->banner ?? null;
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
    $btnText = $store->button_text ?? 'Acheter maintenant';
    $btnAnim = $store->button_animation ?? 'none';
    $btnClass = $btnAnim !== 'none' ? ' btn-anim-'.$btnAnim : '';
    $btnBgColor = $store->button_bg_color ?? ($store->button_color ?? $primary);
    $btnTextColor = $store->button_text_color ?? '#ffffff';
    
    // Détecter si on est sur une page de détails produit
    $isProductDetailPage = request()->routeIs('store.product.show');
    $homeUrl = $isProductDetailPage ? route('store.public', $store->slug) : '#hero';
    
    // Personnalisation bannière
    $bannerTitleColor = $store->banner_title_color ?? '#ffffff';
    $bannerTitleAnimation = $store->banner_title_animation ?? 'none';
    $bannerButtonText = $store->banner_button_text ?? 'Voir le produit';
    $bannerButtonBgColor = $store->banner_button_bg_color ?? ($store->banner_button_color ?? $primary);
    $bannerButtonTextColor = $store->banner_button_text_color ?? '#ffffff';
    
    // Personnalisation header
    $headerBgColor = $store->header_bg_color ?? '#ffffff';
    $headerTextColor = $store->header_text_color ?? '#4b5563';
    $headerNameColor = $store->header_name_color ?? '#1f2937';
    $headerNameFont = $store->header_name_font ?? 'inherit';
    $headerNavHoverColor = $store->header_nav_hover_color ?? ($store->primary_color ?? '#3b82f6');
    
    // Personnalisation footer
    $footerBgColor = $store->footer_bg_color ?? '#1f2937';
    $footerTextColor = $store->footer_text_color ?? '#9ca3af';
    $footerLinkColor = $store->footer_link_color ?? '#ffffff';
    $footerTitleColor = $store->footer_title_color ?? '#ffffff';

    // Fond de page
    $pageBgColor = $store->page_bg_color ?? '#ffffff';

    // Textes de bannière
    $bannerTextsSettings = $settings['banner_texts'] ?? [];
    $bannerTextAnimation = $store->banner_text_animation ?? 'scroll';
    $bannerTextItems = collect($bannerTextsSettings)
        ->filter(function ($item) {
            return !empty($item['text'] ?? null);
        })
        ->map(function ($item) use ($bannerTextAnimation) {
            return [
                'text' => $item['text'],
                'effect' => $item['effect'] ?? $bannerTextAnimation,
            ];
        })
        ->values()
        ->all();
    $bannerTextColor = $store->banner_text_color ?? ($store->banner_title_color ?? '#ffffff');
    $bannerTextFont = $store->banner_text_font ?? 'inherit';
    $bannerTextSize = $store->banner_text_size ?? '1.1rem';
    $bannerTitleSize = $store->banner_title_size ?? '3rem';
    $bannerTextSpeed = $store->banner_text_speed ?? 5; // 1-10, 5 = normal
    $bannerTextColor = $store->banner_text_color ?? ($store->banner_title_color ?? '#ffffff');

    // Sections produit / avis (fond + coins arrondis)
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
    
    // Images de bannière (Thème Vibrant)
    $bannerSettings = $settings;
    $useProductBannerImages = $bannerSettings['vibrant_banner_use_product_images'] ?? true;
    $selectedProductBannerImages = $bannerSettings['vibrant_banner_selected_product_images'] ?? [];
    $customBannerImages = $bannerSettings['vibrant_banner_custom_images'] ?? [];

    $bannerImages = [];

    // 1) Images issues des produits (tous les produits en mode multiproduit, ou produit unique en mode monoproduit)
    if ($useProductBannerImages) {
        if (!empty($selectedProductBannerImages) && is_array($selectedProductBannerImages)) {
            // Si des images sont explicitement sélectionnées, les utiliser
            $bannerImages = array_values(array_filter($selectedProductBannerImages, function($img) {
                return !empty($img) && is_string($img);
            }));
        } else {
            // Sinon, récupérer les images depuis tous les produits disponibles
            $allProductImages = [];
            
            if ($store->allow_multiple_products) {
                // Mode multiproduit : récupérer les images de tous les produits
                foreach ($products as $prod) {
                    $prodImages = [];
                    if (is_array($prod->images)) {
                        $prodImages = array_values(array_filter($prod->images, function($img) {
                            return !empty($img) && is_string($img);
                        }));
                    }
                    // Aussi vérifier la relation productImages
                    if (empty($prodImages) && $prod->relationLoaded('productImages') && $prod->productImages->count() > 0) {
                        $prodImages = $prod->productImages->pluck('image_url')->toArray();
                    }
                    if (!empty($prodImages)) {
                        $allProductImages = array_merge($allProductImages, $prodImages);
                    }
                }
            } else {
                // Mode monoproduit : utiliser le produit unique
                if ($product) {
                    $prodImages = [];
                    if (is_array($product->images)) {
                        $prodImages = array_values(array_filter($product->images, function($img) {
                            return !empty($img) && is_string($img);
                        }));
                    }
                    // Aussi vérifier la relation productImages
                    if (empty($prodImages) && $product->relationLoaded('productImages') && $product->productImages->count() > 0) {
                        $prodImages = $product->productImages->pluck('image_url')->toArray();
                    }
                    $allProductImages = $prodImages;
                }
            }
            
            // Supprimer les doublons et réindexer
            $bannerImages = array_values(array_unique($allProductImages));
        }
    }

    // 2) Images de bannière personnalisées (uploads depuis la personnalisation)
    if (!empty($customBannerImages) && is_array($customBannerImages)) {
        foreach ($customBannerImages as $img) {
            if (!empty($img) && is_string($img)) {
                $bannerImages[] = $img;
            }
        }
    }

    // 3) Nettoyage : supprimer les doublons et réindexer
    $bannerImages = array_values(array_unique($bannerImages));
@endphp

<div class="store-public-page vibrant-template"
     style="--product-card-bg: {{ $productSectionBgColor }};
            --product-card-radius: {{ $productSectionRounded ? '16px' : '0px' }};
            --review-card-bg: {{ $reviewsSectionBgColor }};
            --review-card-radius: {{ $reviewsSectionRounded ? '16px' : '0px' }};">
    <!-- Header simple et professionnel -->
    <header class="vibrant-header">
        <div class="container">
            <div class="header-content">
                <a href="{{ $homeUrl }}" class="brand-section" style="text-decoration: none; display: flex; align-items: center; gap: 1rem; cursor: pointer;">
                    @if($store->logo)
                        <img src="{{ $store->logo }}" alt="{{ $store->name }}" class="store-logo">
                    @else
                        <div class="logo-placeholder">
                            <i class="fas fa-store"></i>
                        </div>
                    @endif
                    <h1 class="store-name" style="color: {{ $headerNameColor }}; font-family: {{ $headerNameFont }}; margin: 0;">{{ $store->name }}</h1>
                </a>
                
                <!-- Menu hamburger pour mobile -->
                <button class="mobile-menu-toggle" onclick="toggleVibrantMobileMenu()" aria-label="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                
                <nav class="main-nav" id="vibrantMainNav">
                    @if($showHome)<a href="{{ $homeUrl }}" class="nav-item" style="color: {{ $headerTextColor }};" onclick="closeVibrantMobileMenu()">Accueil</a>@endif
                    @if($showProduct)<a href="#product" class="nav-item" style="color: {{ $headerTextColor }};" onclick="closeVibrantMobileMenu()">Produit</a>@endif
                    @if($showAboutNav)<a href="#about" class="nav-item" style="color: {{ $headerTextColor }};" onclick="closeVibrantMobileMenu()">À propos</a>@endif
                    @if($showFaqNav)<a href="#faq" class="nav-item" style="color: {{ $headerTextColor }};" onclick="closeVibrantMobileMenu()">FAQ</a>@endif
                    @if($showTrackOrder)
                        <a href="{{ route('track.order') }}" class="nav-item" style="color: {{ $headerTextColor }};" onclick="closeVibrantMobileMenu()">Suivre ma commande</a>
                    @endif
                    @if($showCartNav)
                    <a href="{{ route('cart') }}" class="nav-item cart-icon" style="color: {{ $headerTextColor }};" onclick="closeVibrantMobileMenu()">
                        <i class="fas fa-shopping-cart"></i>
                        @if(session('cart') && count(session('cart')) > 0)
                            <span class="cart-badge">{{ count(session('cart')) }}</span>
                        @endif
                    </a>
                    @endif
                </nav>
            </div>
        </div>
    </header>

    <!-- Bannière avec défilement automatique -->
    <section id="hero" class="hero-banner">
        <div class="banner-carousel" id="bannerCarousel">
            @if(!empty($bannerImages))
                @foreach($bannerImages as $index => $image)
                    <div class="banner-slide {{ $index === 0 ? 'active' : '' }}" style="background-image: linear-gradient(135deg, rgba(0,0,0,0.4), rgba(0,0,0,0.3)), url('{{ $image }}');">
                        <div class="banner-overlay">
                            <div class="banner-content">
                                <h2 class="banner-title banner-title-{{ $bannerTitleAnimation }}" style="color: {{ $bannerTitleColor }}; font-size: {{ $bannerTitleSize }}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">{{ $store->name }}</h2>
                                @php
                                    $hasBannerTexts = !empty($bannerTextItems);
                                @endphp
                                @if($hasBannerTexts)
                                    <div class="banner-scrolling-text" data-default-animation="{{ $bannerTextAnimation }}" data-speed="{{ $bannerTextSpeed }}" style="min-height: 50px; margin: 1rem 0;">
                                        <div class="scrolling-wrapper">
                                            @foreach($bannerTextItems as $index => $item)
                                                @php
                                                    $effect = $item['effect'] ?? $bannerTextAnimation;
                                                @endphp
                                                <span class="scrolling-item {{ $index === 0 ? 'active' : '' }} effect-{{ $effect }}"
                                                      style="color: {{ $bannerTextColor }}; font-family: {{ $bannerTextFont }}; font-size: {{ $bannerTextSize }}; {{ $index === 0 ? 'opacity: 1;' : 'opacity: 0;' }}"
                                                      data-effect="{{ $effect }}">
                                                    {{ $item['text'] }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif($store->description)
                                    <div class="banner-scrolling-text" data-default-animation="scroll" data-speed="{{ $bannerTextSpeed }}">
                                        <div class="scrolling-wrapper">
                                            <span class="scrolling-item active effect-scroll"
                                                  style="color: {{ $bannerTextColor }}; font-family: {{ $bannerTextFont }}; font-size: {{ $bannerTextSize }};"
                                                  data-effect="scroll">{{ $store->description }}</span>
                                        </div>
                                    </div>
                                @endif
                                <a href="#product" class="banner-cta" style="background: {{ $bannerButtonBgColor }}; color: {{ $bannerButtonTextColor }};">
                                    {{ $bannerButtonText }}
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @elseif($store->banner)
                <div class="banner-slide active" style="background-image: linear-gradient(135deg, rgba(0,0,0,0.4), rgba(0,0,0,0.3)), url('{{ $store->banner }}');">
                    <div class="banner-overlay">
                        <div class="banner-content">
                            <h2 class="banner-title banner-title-{{ $bannerTitleAnimation }}" style="color: {{ $bannerTitleColor }}; font-size: {{ $bannerTitleSize }}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">{{ $store->name }}</h2>
                            <a href="#product" class="banner-cta" style="background: {{ $bannerButtonBgColor }}; color: {{ $bannerButtonTextColor }};">
                                {{ $bannerButtonText }}
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="banner-slide active" style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});">
                    <div class="banner-overlay">
                        <div class="banner-content">
                            <h2 class="banner-title banner-title-{{ $bannerTitleAnimation }}" style="color: {{ $bannerTitleColor }}; font-size: {{ $bannerTitleSize }}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">{{ $store->name }}</h2>
                            @php
                                $hasBannerTexts = !empty($bannerTextItems);
                            @endphp
                            @if($hasBannerTexts)
                                <div class="banner-scrolling-text" data-default-animation="{{ $bannerTextAnimation }}" data-speed="{{ $bannerTextSpeed }}" style="min-height: 50px; margin: 1rem 0;">
                                    <div class="scrolling-wrapper">
                                        @foreach($bannerTextItems as $index => $item)
                                            @php
                                                $effect = $item['effect'] ?? $bannerTextAnimation;
                                            @endphp
                                            <span class="scrolling-item {{ $index === 0 ? 'active' : '' }} effect-{{ $effect }}"
                                                  style="color: {{ $bannerTextColor }}; {{ $index === 0 ? 'opacity: 1;' : 'opacity: 0;' }}"
                                                  data-effect="{{ $effect }}">
                                                {{ $item['text'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif($store->description)
                                <div class="banner-scrolling-text" data-default-animation="scroll" data-speed="{{ $bannerTextSpeed }}">
                                    <div class="scrolling-wrapper">
                                        <span class="scrolling-item active effect-scroll"
                                              style="color: {{ $bannerTextColor }};"
                                              data-effect="scroll">{{ $store->description }}</span>
                                    </div>
                                </div>
                            @endif
                            <a href="#product" class="banner-cta" style="background: {{ $bannerButtonBgColor }}; color: {{ $bannerButtonTextColor }};">
                                {{ $bannerButtonText }}
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        @if(count($bannerImages) > 1)
            <div class="banner-indicators">
                @foreach($bannerImages as $index => $image)
                    <span class="indicator {{ $index === 0 ? 'active' : '' }}" onclick="goToBannerSlide({{ $index }})"></span>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Section Produit - Affichage en carte professionnelle -->
    <section id="product" class="product-section">
        <div class="container">
            @if($products->count() > 0)
                @if($store->allow_multiple_products && !isset($product))
                    {{-- MODE MULTI-PRODUITS : Affichage en grille avec 4 produits par ligne --}}
                    <h2 class="section-title" style="text-align: center; margin-bottom: 2.5rem; font-size: 2rem; color: {{ $primary }}; font-weight: 600;">
                        Nos Produits
                    </h2>
                    <div class="products-grid vibrant-grid">
                        @foreach($products as $productItem)
                            @php
                                $productImages = $productItem->images ?? [];
                                if (!is_array($productImages)) {
                                    $productImages = [];
                                }
                                $productImages = array_values(array_filter($productImages, function($img) {
                                    return !empty($img) && is_string($img);
                                }));
                                
                                // Si le produit a des images depuis productImages (relation) et qu'il n'y a pas d'images dans l'attribut images
                                if (empty($productImages) && $productItem->relationLoaded('productImages') && $productItem->productImages->count() > 0) {
                                    $productImages = $productItem->productImages->pluck('image_url')->toArray();
                                }
                                
                                // Toutes les images pour le défilement automatique
                                $allImages = $productImages;
                                $firstImage = !empty($productImages) ? $productImages[0] : null;
                                $otherImages = count($productImages) > 1 ? array_slice($productImages, 1, 4) : []; // Max 4 images secondaires
                                
                                $variants = $productItem->variants ?? [];
                                $hasPromotion = $productItem->has_promotion && 
                                              $productItem->promo_start_date && 
                                              $productItem->promo_end_date && 
                                              now()->between($productItem->promo_start_date, $productItem->promo_end_date);
                                $currentPrice = $hasPromotion ? $productItem->promo_price : $productItem->selling_price;
                                $originalPrice = $hasPromotion ? $productItem->selling_price : null;
                                $promoPercentage = $hasPromotion && $originalPrice ? round((($originalPrice - $currentPrice) / $originalPrice) * 100) : 0;
                            @endphp
                            <div class="product-card vibrant-card" data-product-id="{{ $productItem->id }}">
                                <div class="product-card-image-wrapper">
                                    <div class="product-card-main-image" id="vibrantImageSlider_{{ $productItem->id }}">
                                        @if(!empty($allImages))
                                            @foreach($allImages as $index => $image)
                                                <div class="product-slide {{ $index === 0 ? 'active' : '' }}" data-image-index="{{ $index }}">
                                                    <img src="{{ $image }}" alt="{{ $productItem->name }}" class="main-product-image">
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="product-placeholder">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                        @if($hasPromotion && $promoPercentage > 0)
                                            <span class="promo-badge-card">-{{ $promoPercentage }}%</span>
                                        @endif
                                    </div>
                                    @if(count($otherImages) > 0)
                                        <div class="product-card-thumbnails" id="vibrantThumbnails_{{ $productItem->id }}">
                                            @foreach($otherImages as $index => $thumbImage)
                                                <div class="thumbnail-item {{ $index === 0 ? 'active' : '' }}"
                                                     onclick="changeVibrantProductImage({{ $productItem->id }}, {{ $index + 1 }})">
                                                    <img src="{{ $thumbImage }}" alt="{{ $productItem->name }} - Image {{ $index + 2 }}">
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="product-card-info">
                                    <a href="{{ route('store.product.show', ['slug' => $store->slug, 'id' => $productItem->id]) }}" class="product-card-link">
                                        <h3 class="product-card-name">{{ $productItem->name }}</h3>
                                    </a>
                                    <div class="product-card-price">
                                        @if($hasPromotion && $originalPrice)
                                            <span class="price-old">{{ number_format($originalPrice, 2) }} {{ $store->currency ?? 'USD' }}</span>
                                            <span class="price-new">{{ number_format($currentPrice, 2) }} {{ $store->currency ?? 'USD' }}</span>
                                        @else
                                            <span class="price">{{ number_format($currentPrice, 2) }} {{ $store->currency ?? 'USD' }}</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('store.product.show', ['slug' => $store->slug, 'id' => $productItem->id]) }}" class="btn-card-action{{ $btnClass }}" style="background: {{ $btnBgColor }}; color: {{ $btnTextColor }}; text-decoration: none; display: block; text-align: center;">
                                        {{ $btnText }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Pagination si nécessaire --}}
                    @if(method_exists($products, 'links'))
                        <div class="products-pagination" style="margin-top: 3rem; display: flex; justify-content: center;">
                            {{ $products->links() }}
                        </div>
                    @endif
                @else
                    {{-- MODE 1 PRODUIT OU PAGE DÉTAILS : Affichage détaillé --}}
                    @php
                        // Si $product n'est pas déjà défini (page de détails), prendre le premier de la collection
                        if (!isset($product)) {
                            $product = $products->first(); // Un seul produit
                        }
                        
                        // Récupérer les images du produit
                        // Priorité 1: Attribut images (tableau PostgreSQL TEXT[])
                        $productImages = $product->images ?? [];
                        if (!is_array($productImages)) {
                            $productImages = [];
                        }
                        $productImages = array_filter($productImages, function($img) {
                            return !empty($img) && is_string($img);
                        });
                        $productImages = array_values($productImages);
                        
                        // Priorité 2: Relation productImages si l'attribut images est vide
                        if (empty($productImages) && $product->relationLoaded('productImages') && $product->productImages->count() > 0) {
                            $productImages = $product->productImages->pluck('image_url')->toArray();
                        }
                    @endphp
                
                <div class="product-card-layout">
                    <!-- Images du produit -->
                    <div class="product-images-card">
                        @if(!empty($productImages) && count($productImages) > 0)
                            <div class="main-product-image">
                                <img src="{{ $productImages[0] }}" alt="{{ $product->name }}" id="mainProductImg" onclick="openImageModal('{{ $productImages[0] }}')">
                            </div>
                            @if(count($productImages) > 1)
                                <div class="product-thumbnails">
                                    @foreach($productImages as $index => $image)
                                        <div class="thumb-item {{ $index === 0 ? 'active' : '' }}" onclick="changeProductImage('{{ $image }}', {{ $index }})">
                                            <img src="{{ $image }}" alt="Thumb {{ $index + 1 }}">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="product-placeholder">
                                <i class="fas fa-image"></i>
                                <p>Aucune image</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Informations du produit -->
                    <div class="product-info-card">
                        @php
                            $variants = $product->variants ?? [];
                            $hasPromotion = $product->has_promotion && 
                                            $product->promo_start_date && 
                                            $product->promo_end_date && 
                                            now()->between($product->promo_start_date, $product->promo_end_date);
                            $currentPrice = $hasPromotion ? $product->promo_price : $product->selling_price;
                            $originalPrice = $hasPromotion ? $product->selling_price : null;
                            $promoPercentage = $hasPromotion && $originalPrice ? round((($originalPrice - $currentPrice) / $originalPrice) * 100) : 0;
                        @endphp

                        <!-- Nom du produit -->
                        <h1 class="product-name">{{ $product->name }}</h1>

                        <!-- Bloc prix avec promotion -->
                        <div class="product-price-section">
                            @if($hasPromotion && $originalPrice)
                                <div class="price-original">
                                    <span class="price-amount-old">{{ number_format($originalPrice, 2) }}</span>
                                    <span class="price-currency">{{ $store->currency ?? 'USD' }}</span>
                                </div>
                                <div class="price-promo">
                                    <span class="price-amount">{{ number_format($currentPrice, 2) }}</span>
                                    <span class="price-currency">{{ $store->currency ?? 'USD' }}</span>
                                    <span class="promo-badge-pill">-{{ $promoPercentage }}%</span>
                                </div>
                            @else
                                <span class="price-amount">{{ number_format($currentPrice, 2) }}</span>
                                <span class="price-currency">{{ $store->currency ?? 'USD' }}</span>
                            @endif
                        </div>

                        @if($hasPromotion && $product->promo_end_date)
                            <div class="promo-countdown-badge" id="promoCountdown">
                                <i class="fas fa-clock"></i>
                                <div class="promo-countdown-text">
                                    <span class="promo-label">Promotion se termine dans</span>
                                    <div class="promo-countdown-values">
                                        <span class="promo-unit"><span id="countdown-days">0</span>j</span>
                                        <span class="promo-unit"><span id="countdown-hours">0</span>h</span>
                                        <span class="promo-unit"><span id="countdown-minutes">0</span>m</span>
                                        <span class="promo-unit"><span id="countdown-seconds">0</span>s</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Description -->
                        @if($product->description)
                            <div class="product-description-section">
                                <h3>Description</h3>
                                <div class="description-text">
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                            </div>
                        @endif

                        <!-- Variantes (taille, couleur, longueur) -->
                        @if(!empty($variants))
                            <div class="product-variants vibrant-variants">
                                @if(isset($variants['sizes']) && !empty($variants['sizes']))
                                    <div class="variant-group">
                                        <label class="variant-label">
                                            <i class="fas fa-ruler"></i>
                                            Taille
                                        </label>
                                        <div class="variant-options">
                                            @foreach($variants['sizes'] as $size)
                                                <label class="variant-option">
                                                    <input type="radio" name="variant_size" value="{{ trim($size) }}" {{ $loop->first ? 'checked' : '' }}>
                                                    <span>{{ trim($size) }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(isset($variants['colors']) && !empty($variants['colors']))
                                    <div class="variant-group">
                                        <label class="variant-label">
                                            <i class="fas fa-paint-brush"></i>
                                            Couleur
                                        </label>
                                        <div class="variant-options">
                                            @foreach($variants['colors'] as $color)
                                                <label class="variant-option">
                                                    <input type="radio" name="variant_color" value="{{ trim($color) }}" {{ $loop->first ? 'checked' : '' }}>
                                                    <span>{{ trim($color) }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(isset($variants['lengths']) && !empty($variants['lengths']))
                                    <div class="variant-group">
                                        <label class="variant-label">
                                            <i class="fas fa-ruler-vertical"></i>
                                            Longueur / Mesure
                                        </label>
                                        <div class="variant-options">
                                            @foreach($variants['lengths'] as $length)
                                                <label class="variant-option">
                                                    <input type="radio" name="variant_length" value="{{ trim($length) }}" {{ $loop->first ? 'checked' : '' }}>
                                                    <span>{{ trim($length) }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Quantité + total -->
                        <div class="quantity-selector-wrapper vibrant-quantity">
                            <label for="product_quantity" class="quantity-label">
                                <i class="fas fa-sort-numeric-up"></i>
                                Quantité
                            </label>
                            <div class="quantity-controls">
                                <button type="button" class="quantity-btn" onclick="changeQuantity(-1)">-</button>
                                <input type="number"
                                       id="product_quantity"
                                       name="quantity"
                                       value="1"
                                       min="1"
                                       class="quantity-input">
                                <button type="button" class="quantity-btn" onclick="changeQuantity(1)">+</button>
                            </div>
                            <div class="total-price-display">
                                <span class="total-label">Total:</span>
                                <span class="total-amount" id="totalPrice">{{ number_format($currentPrice, 2) }}</span>
                                <span class="total-currency">{{ $store->currency ?? 'USD' }}</span>
                            </div>
                        </div>

                        @if($product->status === 'active')
                            <div class="product-actions-section">
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="buy-now-form" onsubmit="event.preventDefault(); addToCartAndCheckout(this);">
                                    @csrf
                                    <input type="hidden" name="quantity" id="buy_quantity" value="1">
                                    <input type="hidden" name="variant_size" id="buy_variant_size" value="">
                                    <input type="hidden" name="variant_color" id="buy_variant_color" value="">
                                    <input type="hidden" name="variant_length" id="buy_variant_length" value="">
                                    <button type="submit" class="btn-primary-action{{ $btnClass }}">
                                        <i class="fas fa-bolt"></i>
                                        {{ $btnText }}
                                    </button>
                                </form>
                                
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="add-to-cart-form" onsubmit="updateHiddenQuantityFields(); updateVariantFields(); return true;">
                                    @csrf
                                    <input type="hidden" name="quantity" id="cart_quantity" value="1">
                                    <input type="hidden" name="variant_size" id="cart_variant_size" value="">
                                    <input type="hidden" name="variant_color" id="cart_variant_color" value="">
                                    <input type="hidden" name="variant_length" id="cart_variant_length" value="">
                                    <button type="submit" class="btn-secondary-action">
                                        <i class="fas fa-shopping-cart"></i>
                                        Ajouter au panier
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="product-unavailable">
                                <i class="fas fa-info-circle"></i>
                                <span>Ce produit n'est actuellement pas disponible</span>
                            </div>
                        @endif
                    </div>
                </div>
                @endif {{-- Fin du @if($store->allow_multiple_products && !isset($product)) / @else --}}
            @else
                <div class="empty-products">
                    <i class="fas fa-box-open"></i>
                    <p>Aucun produit disponible</p>
                </div>
            @endif {{-- Fin du @if($products->count() > 0) --}}
        </div>
    </section>

    <!-- Section À propos -->
    @if($showAboutSection)
    <section id="about" class="about-section">
        <div class="container">
            <h2 class="section-title">À propos de {{ $store->name }}</h2>
            <div class="about-content">
                <p>{{ $store->description ?? 'Votre boutique de confiance pour des produits de qualité.' }}</p>
            </div>
        </div>
    </section>
    @endif

    <!-- FAQ -->
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
                @foreach($faqs as $index => $faq)
                    <div class="faq-item" id="vibrantFaqItem{{ $index }}">
                        <button type="button" class="faq-toggle" onclick="toggleVibrantFaq({{ $index }})">
                            <span>{{ $faq['q'] }}</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer-wrapper" id="vibrantFaq{{ $index }}">
                            <p class="faq-answer">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Section Avis clients -->
    @if($product)
    <section class="reviews-section">
        <div class="container">
            <div class="reviews-header">
                <h2 class="section-title">Avis clients</h2>
                <div class="title-decoration"></div>
            </div>

            <!-- Liste des avis -->
            <div class="reviews-list">
                @forelse($productReviews as $review)
                    <div class="review-item">
                        <div class="review-header">
                            <div class="review-author">
                                <div class="author-avatar">{{ substr($review->customer_name, 0, 1) }}</div>
                                <div class="author-info">
                                    <h4>{{ $review->customer_name }}</h4>
                                    <div class="review-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $review->rating ? 'active' : '' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <span class="review-date">{{ $review->created_at->format('d/m/Y') }}</span>
                        </div>
                        <p class="review-comment">{{ $review->comment }}</p>

                        @if(!empty($review->images) && is_array($review->images))
                            <div class="review-images">
                                @foreach($review->images as $img)
                                    <img src="{{ $img }}" alt="Avis photo" onclick="openImageModal('{{ $img }}')">
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="no-reviews">
                        <i class="fas fa-comment-dots"></i>
                        <p>Aucun avis pour le moment. Soyez le premier à donner votre avis !</p>
                    </div>
                @endforelse
            </div>

            <!-- Formulaire d'avis -->
            <div class="review-form-container">
                <h3>Donner votre avis</h3>
                <form action="{{ route('product.review.submit', ['slug' => $store->slug, 'id' => $product->id]) }}" method="POST" enctype="multipart/form-data" class="review-form">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label>Votre nom *</label>
                            <input type="text" name="customer_name" required>
                        </div>
                        <div class="form-group">
                            <label>Email (optionnel)</label>
                            <input type="email" name="customer_email">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Note</label>
                        <div class="rating-input">
                            @for($i = 1; $i <= 5; $i++)
                                <label>
                                    <input type="radio" name="rating" value="{{ $i }}" {{ $i === 5 ? 'checked' : '' }}>
                                    <i class="fas fa-star"></i>
                                </label>
                            @endfor
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Votre avis</label>
                        <textarea name="comment" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Photos (max 5)</label>
                        <input type="file" name="images[]" multiple accept="image/*">
                        <small class="form-hint">Vous pouvez télécharger jusqu'à 5 photos</small>
                    </div>
                    <button type="submit" class="btn-primary-action">
                        <i class="fas fa-paper-plane"></i>
                        Envoyer l'avis
                    </button>
                </form>
            </div>
        </div>
    </section>
    @endif

    <!-- Footer -->
    <footer class="vibrant-footer" style="background: {{ $footerBgColor }}; color: {{ $footerTextColor }};">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section footer-brand">
                    <div class="footer-socials">
                        @if($store->facebook_url)
                            <a href="{{ $store->facebook_url }}" target="_blank" aria-label="Facebook" class="social-link">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        @endif
                        @if($store->instagram_url)
                            <a href="{{ $store->instagram_url }}" target="_blank" aria-label="Instagram" class="social-link">
                                <i class="fab fa-instagram"></i>
                            </a>
                        @endif
                        @if($store->twitter_url)
                            <a href="{{ $store->twitter_url }}" target="_blank" aria-label="Twitter" class="social-link">
                                <i class="fab fa-x-twitter"></i>
                            </a>
                        @endif
                        @if($store->youtube_url)
                            <a href="{{ $store->youtube_url }}" target="_blank" aria-label="YouTube" class="social-link">
                                <i class="fab fa-youtube"></i>
                            </a>
                        @endif
                    </div>
                    <h4 style="color: {{ $footerTitleColor }};">{{ $store->name }}</h4>
                </div>
                <div class="footer-section">
                    <h4 style="color: {{ $footerTitleColor }};">Navigation</h4>
                    <ul>
                        @if($showHome)<li><a href="{{ $homeUrl }}" style="color: {{ $footerLinkColor }};">Accueil</a></li>@endif
                        @if($showProduct)<li><a href="#product" style="color: {{ $footerLinkColor }};">Produit</a></li>@endif
                        @if($showAboutNav)<li><a href="#about" style="color: {{ $footerLinkColor }};">À propos</a></li>@endif
                        @if($showFaqNav)<li><a href="#faq" style="color: {{ $footerLinkColor }};">FAQ</a></li>@endif
                        @if($showTrackOrder)<li><a href="{{ route('track.order') }}" style="color: {{ $footerLinkColor }};">Suivre ma commande</a></li>@endif
                    </ul>
                </div>
                <div class="footer-section">
                    <h4 style="color: {{ $footerTitleColor }};">Moyens de paiement</h4>
                    <div class="payment-icons payment-icons-vibrant">
                        <div class="payment-icon" title="Visa">
                            <i class="fab fa-cc-visa"></i>
                        </div>
                        <div class="payment-icon" title="Mastercard">
                            <i class="fab fa-cc-mastercard"></i>
                        </div>
                        <div class="payment-icon" title="Google Pay">
                            <i class="fab fa-google-pay"></i>
                        </div>
                    </div>
                </div>
                <div class="footer-section">
                    <h4 style="color: {{ $footerTitleColor }};">Contact</h4>
                    <p style="color: {{ $footerTextColor }};">Email: {{ $footerEmail }}</p>
                </div>
            </div>
            <div class="footer-bottom" style="color: {{ $footerTextColor }};">
                <p>&copy; {{ date('Y') }} {{ $store->name }}. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</div>

<!-- Modal pour zoomer les images -->
<div id="imageModal" class="image-modal" onclick="closeImageModal()">
    <span class="modal-close">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<style>
:root {
    --primary: {{ $primary }};
    --secondary: {{ $secondary }};
    --accent: {{ $accent }};
}

.store-public-page { background: {{ $pageBgColor }}; }
body:has(.store-public-page) .topbar { display: none !important; }
body:has(.store-public-page) .main { padding-top: 0 !important; }

/* Header */
.vibrant-header {
    background: {{ $headerBgColor }};
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    position: sticky;
    top: 0;
    z-index: 100;
    padding: 1rem 0;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
}

.brand-section {
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

.logo-placeholder {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.store-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.main-nav {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.nav-item {
    color: #4b5563;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
    position: relative;
}

.nav-item:hover {
    color: {{ $headerNavHoverColor }};
}

.cart-icon {
    position: relative;
    font-size: 1.25rem;
}

.cart-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: var(--accent);
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

/* Menu hamburger pour mobile */
.mobile-menu-toggle {
    display: none;
    flex-direction: column;
    gap: 5px;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0.75rem;
    z-index: 1001;
    position: relative;
    color: {{ $headerTextColor }};
    min-width: 44px;
    min-height: 44px;
    justify-content: center;
    align-items: center;
    margin-left: auto;
}

.mobile-menu-toggle span {
    width: 28px;
    height: 3px;
    background: {{ $headerTextColor }};
    border-radius: 3px;
    transition: all 0.3s ease;
    display: block;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.mobile-menu-toggle.active span:nth-child(1) {
    transform: rotate(45deg) translate(8px, 8px);
}

.mobile-menu-toggle.active span:nth-child(2) {
    opacity: 0;
}

.mobile-menu-toggle.active span:nth-child(3) {
    transform: rotate(-45deg) translate(7px, -7px);
}

/* Bannière avec carousel */
.hero-banner {
    position: relative;
    height: 500px;
    overflow: hidden;
}

.banner-carousel {
    position: relative;
    width: 100%;
    height: 100%;
}

.banner-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    opacity: 0;
    transition: opacity 0.8s ease-in-out;
}

.banner-slide.active {
    opacity: 1;
    z-index: 1;
}

.banner-overlay {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,0.2);
}

.banner-content {
    text-align: center;
    color: white;
    max-width: 700px;
    padding: 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
}

.banner-title {
    font-size: 3rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
    line-height: 1.2;
}

.banner-scrolling-text {
    height: 50px;
    min-height: 50px;
    overflow: visible;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 1rem 0;
    z-index: 10;
}

.scrolling-wrapper {
    position: relative;
    height: 100%;
    width: 100%;
    min-height: 40px;
}

.scrolling-item {
    position: absolute;
    width: 100%;
    font-size: 1.1rem;
    font-weight: 500;
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease, transform 0.6s ease;
    text-align: center;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    z-index: 1;
    pointer-events: none;
    white-space: nowrap;
    overflow: visible;
    text-overflow: ellipsis;
    padding: 0 1rem;
    max-width: 90%;
    box-sizing: border-box;
}

.scrolling-item.active {
    opacity: 1;
    transform: translate(-50%, -50%);
    z-index: 2;
}

/* Effets supplémentaires pour les textes de bannière */
.scrolling-item.effect-scroll {
    transform: translate(-50%, calc(-50% + 30px));
}

.scrolling-item.effect-scroll.active {
    animation: vibrantBannerScrollIn 0.6s ease-out forwards;
}

.scrolling-item.effect-fade.active {
    animation: vibrantBannerFade 3s ease-in-out infinite;
}

.scrolling-item.effect-bounce.active {
    animation: vibrantBannerBounce 2s ease-in-out infinite;
}

.scrolling-item.effect-slide.active {
    animation: vibrantBannerSlide 0.8s ease-out;
}

@keyframes vibrantBannerScrollIn {
    from {
        opacity: 0;
        transform: translate(-50%, calc(-50% + 30px));
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%);
    }
}

@keyframes vibrantBannerFade {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

@keyframes vibrantBannerBounce {
    0%, 100% { transform: translate(-50%, -50%); }
    50% { transform: translate(-50%, calc(-50% - 10px)); }
}

@keyframes vibrantBannerSlide {
    0% { transform: translate(-150%, -50%); opacity: 0; }
    100% { transform: translate(-50%, -50%); opacity: 1; }
}

@keyframes blinkCursor {
    0%, 50% { opacity: 1; }
    51%, 100% { opacity: 0; }
}

.typewriter-cursor {
    display: inline-block;
    margin-left: 2px;
    font-weight: 300;
    color: inherit;
}

/* Animations pour le titre de la bannière */
.banner-title-scroll {
    animation: scrollText 15s linear infinite;
    display: inline-block;
}

.banner-title-fade {
    animation: fadeInOut 3s ease-in-out infinite;
}

.banner-title-bounce {
    animation: bounceTitle 2s ease-in-out infinite;
}

.banner-title-glow {
    animation: glowTitle 2s ease-in-out infinite;
    text-shadow: 0 0 10px currentColor, 0 0 20px currentColor, 0 0 30px currentColor;
}

.banner-title-slide {
    animation: slideInTitle 1s ease-out;
}

@keyframes scrollText {
    0% { transform: translateX(100%); }
    100% { transform: translateX(-100%); }
}

@keyframes fadeInOut {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

@keyframes bounceTitle {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

@keyframes glowTitle {
    0%, 100% { text-shadow: 0 0 10px currentColor, 0 0 20px currentColor; }
    50% { text-shadow: 0 0 20px currentColor, 0 0 30px currentColor, 0 0 40px currentColor; }
}

@keyframes slideInTitle {
    0% { transform: translateX(-100%); opacity: 0; }
    100% { transform: translateX(0); opacity: 1; }
}

.banner-cta {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.75rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    border: none;
    white-space: nowrap;
}

.banner-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.3);
    opacity: 0.9;
}

.banner-indicators {
    position: absolute;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 0.75rem;
    z-index: 10;
}

.banner-indicators .indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
    cursor: pointer;
    transition: all 0.3s;
}

.banner-indicators .indicator.active {
    background: white;
    width: 30px;
    border-radius: 6px;
}

/* Section Produit */
.product-section {
    padding: 4rem 0;
    background: {{ $pageBgColor }};
}

.product-card-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    max-width: 1000px;
    margin: 0 auto;
    background: var(--product-card-bg, #ffffff);
    border-radius: var(--product-card-radius, 16px);
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.product-images-card {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.main-product-image {
    width: 100%;
    max-width: 500px;
    min-width: 400px;
    aspect-ratio: 1;
    border-radius: 12px;
    overflow: hidden;
    background: #f3f4f6;
    position: relative;
}

.main-product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: zoom-in;
    transition: transform 0.3s;
    display: block;
    position: absolute;
    top: 0;
    left: 0;
}

.main-product-image img:hover {
    transform: scale(1.05);
}

.product-thumbnails {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0.75rem;
    margin-top: 1rem;
}

.thumb-item {
    width: 100%;
    aspect-ratio: 1;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.3s;
}

.thumb-item.active {
    border-color: var(--primary);
    border-width: 3px;
}

.thumb-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-placeholder {
    width: 100%;
    max-width: 500px;
    aspect-ratio: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
    border-radius: 12px;
    color: #9ca3af;
}

.product-placeholder i {
    font-size: 3rem;
    margin-bottom: 0.5rem;
}

.product-info-card {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.product-name {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.product-price-section {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
}

.price-main {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary);
}

.price-currency {
    font-size: 1.5rem;
    color: #6b7280;
}

.promo-badge-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    background: linear-gradient(135deg, #ef4444, #f97316);
    color: white;
    font-size: 0.85rem;
    font-weight: 600;
}

.promo-countdown-badge {
    margin-top: 0.75rem;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.6rem 0.9rem;
    border-radius: 999px;
    background: linear-gradient(135deg, rgba(239,68,68,0.08), rgba(245,158,11,0.08));
    border: 1px solid rgba(239,68,68,0.25);
}

.promo-countdown-badge i {
    color: #ef4444;
}

.promo-countdown-text {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

.promo-label {
    font-size: 0.8rem;
    color: #4b5563;
    font-weight: 500;
}

.promo-countdown-values {
    display: flex;
    gap: 0.35rem;
    font-size: 0.8rem;
    color: #111827;
}

.promo-unit {
    background: white;
    border-radius: 999px;
    padding: 0.15rem 0.5rem;
    border: 1px solid #e5e7eb;
}

.product-description-section h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 0.75rem 0;
}

.description-text {
    color: #4b5563;
    line-height: 1.7;
    font-size: 0.95rem;
}

.product-actions-section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* Variantes & quantité (Vibrant) */
.vibrant-variants {
    margin-top: 1rem;
    padding: 1rem 1.25rem;
    background: #f3f4f6;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.vibrant-variants .variant-group {
    margin-bottom: 1rem;
}

.vibrant-variants .variant-group:last-child {
    margin-bottom: 0;
}

.vibrant-variants .variant-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.5rem;
}

.vibrant-variants .variant-options {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.vibrant-variants .variant-option {
    position: relative;
}

.vibrant-variants .variant-option input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.vibrant-variants .variant-option span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.4rem 0.9rem;
    border-radius: 999px;
    border: 1px solid #d1d5db;
    font-size: 0.85rem;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s;
    background: white;
}

.vibrant-variants .variant-option input:checked + span {
    border-color: {{ $accent }};
    background: {{ $accent }};
    color: #020617;
    box-shadow: 0 0 0 1px rgba(15,23,42,0.06);
}

.vibrant-quantity {
    margin-top: 1.25rem;
}

.vibrant-quantity .quantity-label {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    color: #111827;
    font-weight: 500;
    margin-bottom: 0.4rem;
}

.vibrant-quantity .quantity-controls {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
}

.vibrant-quantity .quantity-btn {
    background: transparent;
    border: none;
    color: #111827;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.vibrant-quantity .quantity-input {
    width: 60px;
    text-align: center;
    border: none;
    background: transparent;
    color: #111827;
}

.vibrant-quantity .total-price-display {
    margin-top: 0.4rem;
    font-size: 0.9rem;
    color: #374151;
}

.buy-now-form,
.add-to-cart-form {
    width: 100%;
}

.quantity-control {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.quantity-control label {
    font-weight: 500;
    color: #374151;
}

.quantity-input {
    width: 80px;
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 1rem;
}

.btn-primary-action,
.btn-secondary-action {
    width: 100%;
    padding: 1rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s;
    border: none;
}

.btn-primary-action {
    background: var(--primary);
    color: white;
}

.btn-primary-action:hover {
    background: var(--secondary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-secondary-action {
    background: white;
    color: var(--primary);
    border: 2px solid var(--primary);
}

.btn-secondary-action:hover {
    background: var(--primary);
    color: white;
}

.product-unavailable {
    padding: 1rem;
    background: #fef3c7;
    color: #92400e;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Grille produits (si plusieurs produits) */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
}

/* Styles pour la grille multiproduit Vibrant */
.products-grid.vibrant-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
    margin-top: 2rem;
    justify-items: start;
    align-items: start;
}

.product-card.vibrant-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
    display: flex;
    flex-direction: column;
    height: 100%;
    align-items: stretch;
}

.product-card.vibrant-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

.product-card-image-wrapper {
    position: relative;
    width: 100%;
    background: #f3f4f6;
}

.product-card-main-image {
    position: relative;
    width: 100%;
    height: 280px;
    overflow: hidden;
}

.product-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
    z-index: 1;
}

.product-slide.active {
    opacity: 1;
    z-index: 2;
}

.main-product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.product-card.vibrant-card:hover .main-product-image {
    transform: scale(1.05);
}

.product-card-thumbnails {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.5rem;
    padding: 0.75rem;
    background: white;
}

.thumbnail-item {
    width: 100%;
    aspect-ratio: 1;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.3s;
    opacity: 0.6;
}

.thumbnail-item.active {
    border-color: var(--primary);
    opacity: 1;
}

.thumbnail-item:hover {
    opacity: 1;
    transform: scale(1.1);
}

.thumbnail-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.promo-badge-card {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #ef4444;
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.875rem;
    z-index: 10;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.product-card-info {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.product-card-link {
    text-decoration: none;
    color: inherit;
    display: block;
    cursor: pointer;
    position: relative;
    z-index: 10;
}

.product-card-name {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.75rem;
    line-height: 1.4;
    transition: color 0.2s;
    cursor: pointer;
}

.product-card-link:hover .product-card-name {
    color: var(--primary);
}

.product-card-price {
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.product-card-price .price {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary);
}

.product-card-price .price-old {
    font-size: 0.875rem;
    color: #9ca3af;
    text-decoration: line-through;
}

.product-card-price .price-new {
    font-size: 1.5rem;
    font-weight: 700;
    color: #ef4444;
}

.btn-card-action {
    width: 100%;
    padding: 0.875rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    border: none;
    transition: all 0.3s;
    margin-top: auto;
}

.btn-card-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
}

.product-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 3rem;
}

.product-placeholder i {
    margin-bottom: 0.5rem;
}

/* Responsive : 3 produits par ligne sur tablette */
@media (max-width: 1024px) {
    .products-grid.vibrant-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        justify-items: start;
        align-items: start;
    }
}

/* Responsive : 2 produits par ligne sur tablette moyenne */
@media (max-width: 900px) {
    .products-grid.vibrant-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        justify-items: start;
        align-items: start;
    }
}

/* Responsive : 1 produit par ligne sur mobile */
@media (max-width: 768px) {
    .products-grid.vibrant-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
        justify-items: start;
        align-items: start;
    }
    
    .product-card-main-image {
        height: 250px;
    }
    
    .product-card-info {
        padding: 1.25rem;
    }
    
    .product-card-thumbnails {
        grid-template-columns: repeat(4, 1fr);
    }
    
    .thumbnail-item {
        width: 100%;
        aspect-ratio: 1;
    }
}

.product-item {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.product-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.product-item-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
}

.product-item-placeholder {
    width: 100%;
    height: 250px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 2rem;
}

.product-item-info {
    padding: 1.5rem;
}

.product-item-info h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 0.5rem 0;
}

.product-item-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary);
    margin: 0 0 1rem 0;
}

.product-item-link {
    display: inline-block;
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
}

.product-item-link:hover {
    color: var(--secondary);
}

.empty-products {
    text-align: center;
    padding: 4rem 2rem;
    color: #6b7280;
}

.empty-products i {
    font-size: 4rem;
    margin-bottom: 1rem;
    color: #9ca3af;
}

/* Sections */
.about-section,
.faq-section {
    padding: 4rem 0;
}

.about-section {
    background: {{ $pageBgColor }};
}

.faq-section {
    background: {{ $pageBgColor }};
}

.section-title {
    display: block;
    width: 100%;
    font-size: 2rem;
    font-weight: 700;
    color: {{ $headerNameColor }};
    text-align: center;
    margin: 0 0 2rem 0;
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

.faq-list {
    max-width: 800px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.faq-item {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    overflow: hidden;
    border: 1px solid #e5e7eb;
    transition: box-shadow 0.2s, transform 0.2s;
}

.faq-item.open {
    box-shadow: 0 10px 25px rgba(15,23,42,0.12);
    transform: translateY(-2px);
}

.faq-toggle {
    width: 100%;
    background: transparent;
    border: none;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
}

.faq-toggle i {
    transition: transform 0.2s;
}

.faq-toggle i.rotated {
    transform: rotate(180deg);
}

.faq-answer-wrapper {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.25s ease;
    padding: 0 1.25rem;
}

.faq-answer-wrapper.open {
    padding-bottom: 1rem;
    max-height: 200px;
}

.faq-answer {
    color: #4b5563;
    line-height: 1.6;
    margin: 0.5rem 0 0 0;
}

/* Footer */
.vibrant-footer {
    background: #020617;
    color: white;
    padding: 3rem 0 1rem;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 2.5rem;
    margin-bottom: 2rem;
}

.footer-section h4 {
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0 0 1rem 0;
}

.footer-section p {
    color: #d1d5db;
    line-height: 1.7;
    font-size: 0.9rem;
}

.footer-socials {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.footer-socials .social-link {
    color: {{ $footerTitleColor }};
    font-size: 1.25rem;
    transition: color 0.3s, transform 0.2s;
}

.footer-socials .social-link:hover {
    color: {{ $footerLinkColor }};
    transform: translateY(-2px);
}

.payment-icons-vibrant {
    display: flex;
    justify-content: flex-start;
    gap: 1.25rem;
    flex-wrap: wrap;
}

.payment-icons-vibrant .payment-icon {
    font-size: 2rem;
    color: {{ $footerTitleColor }};
    transition: transform 0.2s, color 0.2s;
    cursor: pointer;
}

.payment-icons-vibrant .payment-icon:hover {
    transform: scale(1.08);
    color: {{ $footerLinkColor }};
}

.footer-section ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-section ul li {
    margin-bottom: 0.5rem;
}

.footer-section ul li a {
    color: #d1d5db;
    text-decoration: none;
    transition: color 0.3s;
}

.footer-section ul li a:hover {
    color: white;
}

.footer-bottom {
    text-align: center;
    padding-top: 2rem;
    border-top: 1px solid #374151;
    color: #9ca3af;
}

/* Avis clients */
.reviews-section {
    padding: 4rem 0;
    background: {{ $pageBgColor }};
}

.reviews-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.reviews-header .section-title {
    margin-bottom: 0.5rem;
    text-align: center;
}

.reviews-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    margin-bottom: 2.5rem;
}

.review-item {
    background: #ffffff;
    border-radius: 16px;
    padding: 1.75rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 8px rgba(15,23,42,0.06);
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.review-author {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.author-avatar {
    width: 40px;
    height: 40px;
    border-radius: 999px;
    background: linear-gradient(135deg, {{ $primary }}, {{ $accent }});
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1rem;
}

.author-info h4 {
    margin: 0;
    font-size: 1rem;
    color: #111827;
}

.review-rating {
    display: flex;
    gap: 0.15rem;
    margin-top: 0.15rem;
}

.review-rating i {
    color: #4b5563;
}

.review-rating i.active {
    color: #facc15;
}

.review-date {
    font-size: 0.85rem;
    color: #9ca3af;
}

.review-comment {
    margin: 0.75rem 0 0.5rem 0;
    color: #4b5563;
    line-height: 1.6;
    font-size: 0.95rem;
}

.review-images {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.75rem;
}

.review-images img {
    width: 70px;
    height: 70px;
    border-radius: 8px;
    object-fit: cover;
    cursor: pointer;
    border: 1px solid #1f2937;
}

.no-reviews {
    text-align: center;
    padding: 2.5rem 1.5rem;
    color: #6b7280;
}

.no-reviews i {
    font-size: 3rem;
    margin-bottom: 0.75rem;
    display: block;
    color: {{ $accent }};
}

.review-form-container {
    background: var(--review-card-bg, #ffffff);
    border-radius: var(--review-card-radius, 16px);
    padding: 1.75rem;
    border: 1px solid #1e293b;
    box-shadow: none;
}

.review-form-container h3 {
    margin: 0 0 1.5rem 0;
    font-size: 1.4rem;
    color: #111827;
}

.review-form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.review-form .form-row {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
}

.review-form .form-group label {
    display: block;
    margin-bottom: 0.35rem;
    font-size: 0.9rem;
    color: #4b5563;
}

.review-form input[type="text"],
.review-form input[type="email"],
.review-form textarea,
.review-form input[type="file"] {
    width: 100%;
    background: #f9fafb;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    padding: 0.6rem 0.8rem;
    color: #e5e7eb;
    font-size: 0.95rem;
}

.review-form textarea {
    min-height: 110px;
    resize: vertical;
}

.rating-input {
    display: flex;
    gap: 0.25rem;
}

.rating-input input {
    display: none;
}

.rating-input i {
    color: #4b5563;
    cursor: pointer;
    font-size: 1.2rem;
}

.rating-input input:checked + i,
.rating-input label:hover i,
.rating-input label:hover ~ label i {
    color: #facc15;
}

.review-form .form-hint {
    font-size: 0.8rem;
    color: #6b7280;
}

.reviews-section .btn-primary-action {
    margin-top: 0.5rem;
    background: {{ $primary }};
    color: white;
    border: none;
    border-radius: 999px;
    padding: 0.7rem 1.5rem;
    font-size: 0.95rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    cursor: pointer;
    box-shadow: 0 10px 25px rgba(37,99,235,0.4);
}

.reviews-section .btn-primary-action i {
    font-size: 0.95rem;
}

@media (max-width: 768px) {
    .review-form .form-row {
        grid-template-columns: 1fr;
    }

    .review-item {
        padding: 1.25rem;
    }

    .review-form-container {
        padding: 1.5rem;
    }
}

/* Modal */
.image-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    cursor: pointer;
}

.modal-content {
    margin: auto;
    display: block;
    width: 90%;
    max-width: 900px;
    max-height: 90vh;
    object-fit: contain;
    animation: zoom 0.3s;
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

@keyframes zoom {
    from {transform: scale(0)}
    to {transform: scale(1)}
}

/* Animations boutons */
@keyframes bounceBtn { 0%,20%,50%,80%,100%{transform:translateY(0);}40%{transform:translateY(-6px);}60%{transform:translateY(-3px);} }
@keyframes pulseBtn { 0%{box-shadow:0 0 0 0 rgba(59,130,246,0.4);}70%{box-shadow:0 0 0 10px rgba(59,130,246,0);}100%{box-shadow:0 0 0 0 rgba(59,130,246,0);} }
@keyframes shakeBtn { 0%,100%{transform:translateX(0);}20%{transform:translateX(-4px);}40%{transform:translateX(4px);}60%{transform:translateX(-3px);}80%{transform:translateX(3px);} }
@keyframes glowBtn { 0%{box-shadow:0 0 10px rgba(59,130,246,0.6);}50%{box-shadow:0 0 22px rgba(59,130,246,0.9);}100%{box-shadow:0 0 10px rgba(59,130,246,0.6);} }
.btn-anim-bounce { animation: bounceBtn 1.2s infinite; }
.btn-anim-pulse { animation: pulseBtn 1.6s infinite; }
.btn-anim-shake { animation: shakeBtn 0.9s infinite; }
.btn-anim-glow { animation: glowBtn 1.6s infinite; }

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

/* Responsive Mobile */
@media (max-width: 768px) {
    /* Header Mobile */
    .mobile-menu-toggle {
        display: flex !important;
    }
    
    .main-nav {
        position: fixed;
        top: 70px;
        left: 0;
        right: 0;
        background: {{ $headerBgColor }};
        flex-direction: column;
        align-items: flex-start;
        padding: 1.5rem;
        gap: 1rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        z-index: 999;
        max-height: calc(100vh - 70px);
        overflow-y: auto;
        display: none;
    }
    
    .main-nav.active {
        transform: translateX(0);
        display: flex;
    }
    
    .main-nav .nav-item {
        width: 100%;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }
    
    .main-nav .nav-item:last-child {
        border-bottom: none;
    }
    
    .header-content {
        position: relative;
    }
    
    .store-name {
        font-size: 1.25rem;
    }
    
    .store-logo, .logo-placeholder {
        width: 40px;
        height: 40px;
    }
    
    /* Bannière Mobile */
    .hero-banner {
        height: 350px;
    }
    
    .banner-title {
        font-size: 2rem;
    }
    
    .banner-scrolling-text {
        min-height: 40px;
        margin: 0.5rem 0;
    }
    
    .scrolling-item {
        font-size: 1rem;
    }
    
    .banner-content {
        padding: 1.5rem;
    }
    
    .banner-button {
        padding: 0.75rem 1.5rem;
        font-size: 0.9rem;
    }
    
    /* Produit Mobile */
    .product-card-layout {
        grid-template-columns: 1fr;
        padding: 1.5rem;
        gap: 1.5rem;
    }
    
    .main-product-image {
        max-width: 100%;
        width: 100%;
        min-width: 100%;
        aspect-ratio: 1;
        position: relative;
    }
    
    .main-product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        position: absolute;
        top: 0;
        left: 0;
    }
    
    .product-thumbnails {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.5rem;
        margin-top: 1rem;
    }
    
    .thumb-item {
        width: 100%;
        aspect-ratio: 1;
        min-width: 0;
    }
    
    .product-info-wrapper {
        padding: 1.5rem;
    }
    
    .product-title {
        font-size: 1.75rem;
    }
    
    .product-price {
        font-size: 1.5rem;
    }
    
    /* Sections Mobile */
    .about-section, .faq-section, .reviews-section {
        padding: 2rem 1rem;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .faq-item {
        padding: 1rem;
    }
    
    .review-item {
        padding: 1.25rem;
    }
    
    .review-form-container {
        padding: 1.5rem;
    }
    
    /* Footer Mobile */
    .footer-content {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .footer-section {
        text-align: center;
    }
    
    .container {
        padding: 0 1rem;
    }
    
    /* Carousel bannière mobile */
    .banner-indicators {
        bottom: 10px;
    }
    
    .banner-indicators .indicator {
        width: 8px;
        height: 8px;
    }
    
    /* Boutons d'action mobile */
    .product-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn-primary-action, .btn-secondary-action {
        width: 100%;
        padding: 1rem;
        font-size: 1rem;
    }
}

/* Responsive Tablet */
@media (max-width: 1024px) and (min-width: 769px) {
    .product-card-layout {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .footer-content {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<script>
// Carousel bannière
let currentBannerIndex = 0;
const bannerSlides = document.querySelectorAll('.banner-slide');
const bannerIndicators = document.querySelectorAll('.banner-indicators .indicator');

function showBannerSlide(index) {
    bannerSlides[currentBannerIndex].classList.remove('active');
    if (bannerIndicators[currentBannerIndex]) {
        bannerIndicators[currentBannerIndex].classList.remove('active');
    }
    
    currentBannerIndex = index;
    
    bannerSlides[currentBannerIndex].classList.add('active');
    if (bannerIndicators[currentBannerIndex]) {
        bannerIndicators[currentBannerIndex].classList.add('active');
    }
    
    // Réinitialiser les textes de bannière pour le nouveau slide
    const newSlideContainer = bannerSlides[currentBannerIndex].querySelector('.banner-scrolling-text');
    if (newSlideContainer) {
        const newItems = Array.from(newSlideContainer.querySelectorAll('.scrolling-item'));
        if (newItems.length > 0) {
            // Réinitialiser tous les items
            newItems.forEach((item, idx) => {
                item.classList.remove('active');
                if (item.dataset.fullText) {
                    item.textContent = item.dataset.fullText;
                }
            });
            // Activer le premier
            newItems[0].classList.add('active');
            // Relancer l'animation si nécessaire
            setTimeout(() => {
                if (window.initVibrantBannerTexts) {
                    window.initVibrantBannerTexts();
                }
            }, 100);
        }
    }
}

function goToBannerSlide(index) {
    showBannerSlide(index);
    // Réinitialiser l'auto-scroll
    if (bannerAutoScroll) {
        clearInterval(bannerAutoScroll);
    }
    if (bannerSlides.length > 1) {
        bannerAutoScroll = setInterval(() => {
            showBannerSlide((currentBannerIndex + 1) % bannerSlides.length);
        }, 5000);
    }
}

// Auto-scroll bannière
let bannerAutoScroll;
if (bannerSlides.length > 1) {
    bannerAutoScroll = setInterval(() => {
        showBannerSlide((currentBannerIndex + 1) % bannerSlides.length);
    }, 5000);
    
    // Pause au survol
    const bannerCarousel = document.getElementById('bannerCarousel');
    if (bannerCarousel) {
        bannerCarousel.addEventListener('mouseenter', () => {
            if (bannerAutoScroll) clearInterval(bannerAutoScroll);
        });
        bannerCarousel.addEventListener('mouseleave', () => {
            if (bannerSlides.length > 1) {
                bannerAutoScroll = setInterval(() => {
                    showBannerSlide((currentBannerIndex + 1) % bannerSlides.length);
                }, 5000);
            }
        });
    }
}

// Textes de bannière (défilement / effets par texte)
window.initVibrantBannerTexts = function() {
    // Chercher dans le slide actif d'abord, sinon dans tous les slides
    let container = document.querySelector('.banner-slide.active .banner-scrolling-text');
    if (!container) {
        container = document.querySelector('.hero-banner .banner-scrolling-text');
    }
    if (!container) {
        return;
    }

    const items = Array.from(container.querySelectorAll('.scrolling-item'));
    if (!items.length) {
        return;
    }
    
    // Nettoyer les timers précédents s'ils existent
    if (window.vibrantBannerTextTimer) {
        clearTimeout(window.vibrantBannerTextTimer);
        window.vibrantBannerTextTimer = null;
    }

    const defaultAnimation = container.dataset.defaultAnimation || 'scroll';
    const speed = parseInt(container.dataset.speed) || 5; // 1-10, 5 = normal
    // Calculer les durées basées sur la vitesse (1 = très lent, 10 = très rapide)
    const speedMultiplier = (11 - speed) / 5; // Inverse: 1 = 2x, 5 = 1x, 10 = 0.2x
    const typingSpeed = Math.max(30, 100 * speedMultiplier); // Vitesse machine à écrire
    const displayDuration = Math.max(2000, 5000 * speedMultiplier); // Durée d'affichage
    const pauseAfterTyping = Math.max(1000, 3000 * speedMultiplier); // Pause après machine à écrire
    const transitionDuration = Math.max(400, 800 * speedMultiplier); // Durée des transitions

    items.forEach(item => {
        if (!item.dataset.fullText) {
            item.dataset.fullText = item.textContent;
        }
    });

    let currentIndex = 0;
    let timer = null;
    
    // Stocker l'index actuel dans le container pour éviter les conflits
    if (!container.dataset.currentIndex) {
        container.dataset.currentIndex = '0';
    } else {
        currentIndex = parseInt(container.dataset.currentIndex) || 0;
    }

    function showItem(index) {
        if (timer) {
            clearTimeout(timer);
            timer = null;
        }
        
        if (index >= items.length) {
            index = 0;
        }
        currentIndex = index;
        container.dataset.currentIndex = index.toString();

        const item = items[index];
        const effect = item.dataset.effect || defaultAnimation;

        items.forEach(el => {
            el.classList.remove('active');
            el.style.opacity = '0';
            el.style.transform = 'translate(-50%, -50%)';
            const full = el.dataset.fullText || el.textContent;
            el.textContent = full;
            // Retirer les curseurs s'ils existent
            const cursors = el.querySelectorAll('.typewriter-cursor');
            cursors.forEach(c => c.remove());
        });

        item.classList.add('active');
        item.style.opacity = '1';
        item.style.transform = 'translate(-50%, -50%)';

        if (effect === 'typewriter') {
            let charIndex = 0;
            const fullText = item.dataset.fullText || '';
            item.textContent = '';
            item.style.whiteSpace = 'nowrap';
            item.style.overflow = 'visible';
            
            // Ajouter un curseur clignotant
            const cursor = document.createElement('span');
            cursor.className = 'typewriter-cursor';
            cursor.textContent = '|';
            cursor.style.opacity = '1';
            cursor.style.animation = 'blinkCursor 1s infinite';
            item.appendChild(cursor);

            function typeChar() {
                if (charIndex < fullText.length) {
                    // Retirer le curseur s'il existe (avec try-catch pour éviter les erreurs)
                    try {
                        if (item.contains(cursor)) {
                            item.removeChild(cursor);
                        }
                    } catch (e) {
                        // Le curseur a peut-être déjà été supprimé, continuer
                    }
                    item.textContent = fullText.substring(0, charIndex + 1);
                    item.appendChild(cursor);
                    charIndex++;
                    timer = setTimeout(typeChar, typingSpeed);
                } else {
                    // Texte complet écrit, retirer le curseur et attendre
                    setTimeout(() => {
                        try {
                            if (item.contains(cursor)) {
                                item.removeChild(cursor);
                            }
                        } catch (e) {
                            // Le curseur a peut-être déjà été supprimé, continuer
                        }
                        timer = setTimeout(() => {
                            // Fade out
                            item.style.opacity = '0';
                            setTimeout(() => {
                                currentIndex = (currentIndex + 1) % items.length;
                                showItem(currentIndex);
                            }, transitionDuration);
                        }, pauseAfterTyping);
                    }, 500);
                }
            }

            typeChar();
        } else {
            // Effets scroll / fade / bounce / slide : gérés par CSS avec la classe .active
            timer = setTimeout(() => {
                // Fade out avant de passer au suivant
                item.style.opacity = '0';
                setTimeout(() => {
                    currentIndex = (currentIndex + 1) % items.length;
                    showItem(currentIndex);
                }, transitionDuration);
            }, displayDuration);
            window.vibrantBannerTextTimer = timer;
        }
    }

    if (items.length === 1) {
        const single = items[0];
        const effect = single.dataset.effect || defaultAnimation;
        single.classList.add('active');
        single.style.opacity = '1';

        if (effect === 'typewriter') {
            let charIndex = 0;
            const fullText = single.dataset.fullText || single.textContent;
            single.textContent = '';
            single.style.whiteSpace = 'nowrap';
            single.style.overflow = 'visible';

            function typeCharSingle() {
                if (charIndex < fullText.length) {
                    single.textContent = fullText.substring(0, charIndex + 1);
                    charIndex++;
                    setTimeout(typeCharSingle, 50);
                }
            }

            typeCharSingle();
        }
    } else {
        // Plusieurs textes : afficher le premier immédiatement
        if (items.length > 0) {
            items[0].classList.add('active');
            items[0].style.opacity = '1';
        }
        // Démarrer le défilement après un court délai
        setTimeout(() => {
            showItem(currentIndex);
        }, 500);
    }
};

// Initialiser au chargement
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.initVibrantBannerTexts);
} else {
    window.initVibrantBannerTexts();
}

// Changement d'image produit
function changeProductImage(imageSrc, index) {
    document.getElementById('mainProductImg').src = imageSrc;
    document.querySelectorAll('.thumb-item').forEach(item => {
        item.classList.remove('active');
    });
    document.querySelectorAll('.thumb-item')[index].classList.add('active');
}

// Modal images
function openImageModal(imageSrc) {
    document.getElementById('imageModal').style.display = 'block';
    document.getElementById('modalImage').src = imageSrc;
}

function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
}

// Gestion quantité & variantes (inspiré du thème minimal)
function changeQuantity(delta) {
    const quantityInput = document.getElementById('product_quantity');
    if (!quantityInput) return;
    
    let currentValue = parseInt(quantityInput.value) || 1;
    let newValue = currentValue + delta;
    if (newValue < 1) newValue = 1;
    quantityInput.value = newValue;
    updateTotalPrice();
    updateHiddenQuantityFields();
}

function updateTotalPrice() {
    const quantityInput = document.getElementById('product_quantity');
    const totalPriceElement = document.getElementById('totalPrice');
    if (!quantityInput || !totalPriceElement) return;
    
    const quantity = parseInt(quantityInput.value) || 1;
    const unitPrice = window.productUnitPrice || 0;
    const total = (unitPrice * quantity).toFixed(2);
    totalPriceElement.textContent = total;
}

function updateHiddenQuantityFields() {
    const quantityInput = document.getElementById('product_quantity');
    const quantity = quantityInput ? quantityInput.value : 1;

    const buyQuantity = document.getElementById('buy_quantity');
    const cartQuantity = document.getElementById('cart_quantity');
    if (buyQuantity) buyQuantity.value = quantity;
    if (cartQuantity) cartQuantity.value = quantity;
}

function updateVariantFields() {
    const sizeInput = document.querySelector('input[name="variant_size"]:checked');
    const colorInput = document.querySelector('input[name="variant_color"]:checked');
    const lengthInput = document.querySelector('input[name="variant_length"]:checked');

    const size = sizeInput ? sizeInput.value : '';
    const color = colorInput ? colorInput.value : '';
    const length = lengthInput ? lengthInput.value : '';

    const buySize = document.getElementById('buy_variant_size');
    const buyColor = document.getElementById('buy_variant_color');
    const buyLength = document.getElementById('buy_variant_length');
    const cartSize = document.getElementById('cart_variant_size');
    const cartColor = document.getElementById('cart_variant_color');
    const cartLength = document.getElementById('cart_variant_length');

    if (buySize) buySize.value = size;
    if (buyColor) buyColor.value = color;
    if (buyLength) buyLength.value = length;
    if (cartSize) cartSize.value = size;
    if (cartColor) cartColor.value = color;
    if (cartLength) cartLength.value = length;
}

// Ajouter au panier et checkout
function addToCartAndCheckout(form) {
    // S'assurer que les valeurs sont à jour
    updateHiddenQuantityFields();
    updateVariantFields();
    
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

@if(isset($product) && $product)
document.addEventListener('DOMContentLoaded', () => {
    // Initialiser le slide actif
    if (bannerSlides.length > 0) {
        showBannerSlide(0);
    }

    // Initialiser le prix unitaire pour le total
    @if(isset($currentPrice))
        window.productUnitPrice = {{ number_format($currentPrice, 2, '.', '') }};
    @else
        window.productUnitPrice = {{ number_format($product->selling_price ?? 0, 2, '.', '') }};
    @endif

    // Écouter les changements de quantité
    const quantityInput = document.getElementById('product_quantity');
    if (quantityInput) {
        quantityInput.addEventListener('change', function() {
            updateTotalPrice();
            updateHiddenQuantityFields();
        });
    }

    // Écouter les changements de variantes
    const variantInputs = document.querySelectorAll('input[name="variant_size"], input[name="variant_color"], input[name="variant_length"]');
    variantInputs.forEach(input => {
        input.addEventListener('change', updateVariantFields);
    });

    // Initialiser les valeurs
    updateVariantFields();
    updateHiddenQuantityFields();
    updateTotalPrice();

    // Compte à rebours de promotion
    @if($product->has_promotion && $product->promo_end_date)
        const promoEndDate = new Date('{{ $product->promo_end_date->format('Y-m-d H:i:s') }}');

        function updatePromoCountdown() {
            const countdownElement = document.getElementById('promoCountdown');
            if (!countdownElement) return;

            const now = new Date();
            const diffTime = promoEndDate - now;

            if (diffTime > 0) {
                const days = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diffTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diffTime % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diffTime % (1000 * 60)) / 1000);

                const daysEl = document.getElementById('countdown-days');
                const hoursEl = document.getElementById('countdown-hours');
                const minutesEl = document.getElementById('countdown-minutes');
                const secondsEl = document.getElementById('countdown-seconds');

                if (daysEl) daysEl.textContent = days;
                if (hoursEl) hoursEl.textContent = hours.toString().padStart(2, '0');
                if (minutesEl) minutesEl.textContent = minutes.toString().padStart(2, '0');
                if (secondsEl) secondsEl.textContent = seconds.toString().padStart(2, '0');
            } else {
                countdownElement.style.display = 'none';
            }
        }

        updatePromoCountdown();
        setInterval(updatePromoCountdown, 1000);
    @endif
}); // Fin du document.addEventListener('DOMContentLoaded')
@endif

// FAQ Vibrant - accordéon
function toggleVibrantFaq(index) {
    const answer = document.getElementById('vibrantFaq' + index);
    const item = document.getElementById('vibrantFaqItem' + index);
    const icon = item ? item.querySelector('.faq-toggle i') : null;
    if (!answer || !item) return;

    const isOpen = answer.classList.contains('open');

    // Fermer tous les items
    document.querySelectorAll('.faq-answer-wrapper').forEach(el => el.classList.remove('open'));
    document.querySelectorAll('.faq-item').forEach(el => el.classList.remove('open'));
    document.querySelectorAll('.faq-toggle i').forEach(i => i.classList.remove('rotated'));

    // Ouvrir seulement celui cliqué s'il n'était pas déjà ouvert
    if (!isOpen) {
        answer.classList.add('open');
        item.classList.add('open');
        if (icon) icon.classList.add('rotated');
    }
}
// Menu mobile pour Vibrant
function toggleVibrantMobileMenu() {
    const nav = document.getElementById('vibrantMainNav');
    const toggle = document.querySelector('.mobile-menu-toggle');
    if (nav && toggle) {
        nav.classList.toggle('active');
        toggle.classList.toggle('active');
        // Empêcher le scroll du body quand le menu est ouvert
        if (nav.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }
}

function closeVibrantMobileMenu() {
    const nav = document.getElementById('vibrantMainNav');
    const toggle = document.querySelector('.mobile-menu-toggle');
    if (nav && toggle) {
        nav.classList.remove('active');
        toggle.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Fermer le menu au clic en dehors
document.addEventListener('click', function(e) {
    const nav = document.getElementById('vibrantMainNav');
    const toggle = document.querySelector('.mobile-menu-toggle');
    
    if (nav && toggle && nav.classList.contains('active')) {
        if (!nav.contains(e.target) && !toggle.contains(e.target)) {
            closeVibrantMobileMenu();
        }
    }
});

// Fermer le menu lors du scroll
window.addEventListener('scroll', function() {
    closeVibrantMobileMenu();
});

// Fonction pour changer l'image d'un produit dans la grille (mode multiproduit)
function changeVibrantProductImage(productId, imageIndex) {
    const slider = document.getElementById('vibrantImageSlider_' + productId);
    if (slider) {
        const slides = slider.querySelectorAll('.product-slide');
        const thumbnails = document.getElementById('vibrantThumbnails_' + productId);
        const thumbItems = thumbnails ? thumbnails.querySelectorAll('.thumbnail-item') : [];
        
        // Activer la slide correspondante
        slides.forEach((slide, index) => {
            if (index === imageIndex) {
                slide.classList.add('active');
            } else {
                slide.classList.remove('active');
            }
        });
        
        // Activer la miniature correspondante
        thumbItems.forEach((thumb, index) => {
            if (index === imageIndex - 1) {
                thumb.classList.add('active');
            } else {
                thumb.classList.remove('active');
            }
        });
    }
}

// Auto-défilement des images pour chaque produit dans la grille
@if($store->allow_multiple_products)
document.addEventListener('DOMContentLoaded', function() {
    const productCards = document.querySelectorAll('.product-card.vibrant-card');
    
    productCards.forEach(card => {
        const productId = card.dataset.productId;
        const slider = document.getElementById('vibrantImageSlider_' + productId);
        if (!slider) return;
        
        const slides = slider.querySelectorAll('.product-slide');
        if (slides.length <= 1) {
            if (slides.length === 1) {
                slides[0].classList.add('active');
            }
            return;
        }
        
        // S'assurer que la première slide est active au départ
        slides.forEach((slide, index) => {
            if (index === 0) {
                slide.classList.add('active');
            } else {
                slide.classList.remove('active');
            }
        });
        
        let currentSlide = 0;
        let autoScrollInterval;
        
        function showNextSlide() {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
            
            // Mettre à jour les miniatures
            const thumbnails = document.getElementById('vibrantThumbnails_' + productId);
            if (thumbnails) {
                const thumbItems = thumbnails.querySelectorAll('.thumbnail-item');
                thumbItems.forEach((thumb, index) => {
                    if (index === currentSlide - 1) {
                        thumb.classList.add('active');
                    } else {
                        thumb.classList.remove('active');
                    }
                });
            }
        }
        
        // Démarrer l'auto-scroll
        autoScrollInterval = setInterval(showNextSlide, 4000);
        
        // Pause au survol
        card.addEventListener('mouseenter', () => {
            if (autoScrollInterval) {
                clearInterval(autoScrollInterval);
            }
        });
        
        card.addEventListener('mouseleave', () => {
            if (slides.length > 1) {
                autoScrollInterval = setInterval(showNextSlide, 4000);
            }
        });
    });
});
@endif
</script>
@endsection
