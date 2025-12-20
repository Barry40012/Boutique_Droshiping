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
    $btnBgColor = $store->button_bg_color ?? ($store->primary_color ?? '#111827');
    $btnTextColor = $store->button_text_color ?? '#ffffff';
    
    // Détecter si on est sur une page de détails produit
    $isProductDetailPage = request()->routeIs('store.product.show');
    $homeUrl = $isProductDetailPage ? route('store.public', $store->slug) : '#product';
    
    // Couleurs principales
    $primary = $store->primary_color ?? '#111827';
    $secondary = $store->secondary_color ?? '#1f2937';
    $accent = $store->accent_color ?? '#f59e0b';
    
    // Personnalisation header
    $headerBgColor = $store->header_bg_color ?? '#ffffff';
    $headerTextColor = $store->header_text_color ?? '#4b5563';
    $headerNameColor = $store->header_name_color ?? '#1f2937';
    $headerNameFont = $store->header_name_font ?? 'inherit';
    $headerNavHoverColor = $store->header_nav_hover_color ?? ($store->primary_color ?? '#0ea5e9');
    
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
    $bannerTextSize = $store->banner_text_size ?? '1.25rem';
    $bannerTitleSize = $store->banner_title_size ?? '3rem';
    $bannerTextSpeed = $store->banner_text_speed ?? 5; // 1-10, 5 = normal

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
    
    // Nouvelles fonctionnalités
    $language = $store->language ?? 'fr';
    $whyChooseProduct = $store->why_choose_product ?? '';
    $productBannerSteps = $store->product_banner_steps ?? [];
    $productReviews = $reviews ?? collect([]);
    
    // Traductions
    $translations = [
        'fr' => [
            'home' => 'Accueil',
            'product' => 'Produit',
            'about' => 'À propos',
            'faq' => 'FAQ',
            'cart' => 'Panier',
            'track_order' => 'Suivre ma commande',
            'about_title' => 'À propos de',
            'faq_title' => 'Questions fréquentes',
            'quick_links' => 'Liens rapides',
            'contact' => 'Contact',
            'payment_methods' => 'Moyens de paiement acceptés',
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
<div class="store-public-page minimal-template"
     style="--product-card-bg: {{ $productSectionBgColor }};
            --product-card-radius: {{ $productSectionRounded ? '16px' : '0px' }};
            --review-card-bg: {{ $reviewsSectionBgColor }};
            --review-card-radius: {{ $reviewsSectionRounded ? '12px' : '0px' }};">
    <!-- Header de la boutique -->
    <header class="store-header minimal-header" style="background: {{ $headerBgColor }};">
        <div class="container">
            <div class="store-header-content">
                <a href="{{ $homeUrl }}" class="store-brand" style="text-decoration: none; display: flex; align-items: center; gap: 1rem; cursor: pointer;">
                    @if($store->logo)
                        <img src="{{ $store->logo }}" alt="{{ $store->name }}" class="store-logo">
                    @else
                        <i class="fas fa-store store-icon"></i>
                    @endif
                    <h1 class="store-name" style="color: {{ $headerNameColor }}; font-family: {{ $headerNameFont }};">{{ $store->name }}</h1>
                </a>
                
                <!-- Menu hamburger pour mobile -->
                <button class="mobile-menu-toggle" onclick="toggleMobileMenu()" aria-label="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                
                <nav class="store-nav" id="mainNav">
                    @if($showHome)<a href="{{ $homeUrl }}" class="nav-link" style="color: {{ $headerTextColor }};" onclick="closeMobileMenu()">{{ $t['home'] }}</a>@endif
                    @if($showProduct)<a href="#product" class="nav-link" style="color: {{ $headerTextColor }};" onclick="closeMobileMenu()">{{ $t['product'] }}</a>@endif
                    @if($showAboutNav)<a href="#about" class="nav-link" style="color: {{ $headerTextColor }};" onclick="closeMobileMenu()">{{ $t['about'] }}</a>@endif
                    @if($showFaqNav)<a href="#faq" class="nav-link" style="color: {{ $headerTextColor }};" onclick="closeMobileMenu()">{{ $t['faq'] }}</a>@endif
                    @php $showTrackOrder = $settings['nav_track_order'] ?? true; @endphp
                    @if($showTrackOrder)
                    <a href="{{ route('track.order') }}" class="nav-link" style="color: {{ $headerTextColor }};" onclick="closeMobileMenu()">
                        <i class="fas fa-truck"></i>
                        {{ $t['track_order'] }}
                    </a>
                    @endif
                    @if($showCartNav)
                    <a href="{{ route('cart') }}" class="nav-link cart-link" style="color: {{ $headerTextColor }};" onclick="closeMobileMenu()">
                        <i class="fas fa-shopping-cart"></i>
                        {{ $t['cart'] }}
                        <span class="cart-count" id="cartCount">{{ session('cart') ? count(session('cart')) : 0 }}</span>
                    </a>
                    @endif
                    <!-- Sélecteur de langue -->
                    <div class="language-selector">
                        <select id="languageSelect" class="lang-select" style="color: {{ $headerTextColor }}; background: {{ $headerBgColor }};">
                            <option value="fr" {{ $language === 'fr' ? 'selected' : '' }}>FR</option>
                            <option value="en" {{ $language === 'en' ? 'selected' : '' }}>EN</option>
                        </select>
                    </div>
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
            <h2 class="banner-title" style="color: {{ $store->banner_title_color ?? '#ffffff' }}; font-size: {{ $bannerTitleSize }};">{{ $store->name }}</h2>
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
        </div>
    </section>

    <!-- Section Produit(s) -->
    <section id="product" class="product-section minimal-product">
        <div class="container" style="max-width: 1100px;">
            @if($products->count() > 0)
                @if($store->allow_multiple_products && !isset($product))
                    {{-- MODE MULTI-PRODUITS : Affichage en grille avec 3 produits par ligne --}}
                    <h2 class="section-title" style="text-align: center; margin-bottom: 2.5rem; font-size: 2rem; color: {{ $primary }}; font-weight: 600;">
                        {{ $t['product'] ?? 'Nos Produits' }}
                    </h2>
                    <div class="products-grid minimal-grid">
                        @foreach($products as $productItem)
                            @php
                                $productImages = $productItem->images ?? [];
                                if (!is_array($productImages)) {
                                    $productImages = [];
                                }
                                $productImages = array_values(array_filter($productImages, function($img) {
                                    return !empty($img) && is_string($img);
                                }));
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
                            <div class="product-card minimal-card" data-product-id="{{ $productItem->id }}">
                                <div class="product-card-image-wrapper">
                                    <div class="product-card-main-image" id="imageSlider_{{ $productItem->id }}">
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
                                        <div class="product-card-thumbnails" id="thumbnails_{{ $productItem->id }}">
                                            @foreach($otherImages as $index => $thumbImage)
                                                <div class="thumbnail-item {{ $index === 0 ? 'active' : '' }}" 
                                                     onclick="changeProductImage({{ $productItem->id }}, {{ $index + 1 }})">
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
                    // Utiliser directement l'accessor du modèle qui gère PostgreSQL TEXT[]
                    $productImages = $product->images ?? [];
                    
                    // Debug: logger les valeurs pour diagnostic
                    \Log::info('Product Images Debug', [
                        'product_id' => $product->id ?? 'N/A',
                        'raw_attributes' => $product->getAttributes()['images'] ?? 'NOT SET',
                        'via_accessor' => $productImages,
                        'is_array' => is_array($productImages),
                        'count' => is_array($productImages) ? count($productImages) : 0,
                    ]);
                    
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
                    <!-- Images du produit avec navigation (à gauche) -->
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
                                    @if(config('app.debug'))
                                        <small style="color: #666; font-size: 0.8rem; margin-top: 0.5rem; display: block;">
                                            Debug: {{ count($productImages) }} image(s) trouvée(s)
                                            @if(!empty($product->getAttributes()['images'] ?? null))
                                                | Raw: {{ substr($product->getAttributes()['images'], 0, 100) }}
                                            @endif
                                        </small>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Informations du produit (à droite) -->
                    <div class="product-info-wrapper">
                        @php
                            $variants = $product->variants ?? [];
                            $hasPromotion = $product->has_promotion && 
                                          $product->promo_start_date && 
                                          $product->promo_end_date && 
                                          now()->between($product->promo_start_date, $product->promo_end_date);
                            $currentPrice = $hasPromotion ? $product->promo_price : $product->selling_price;
                            $originalPrice = $hasPromotion ? $product->selling_price : null;
                            $promoPercentage = $hasPromotion && $originalPrice ? round((($originalPrice - $currentPrice) / $originalPrice) * 100) : 0;
                            $daysRemaining = $hasPromotion && $product->promo_end_date ? max(0, now()->diffInDays($product->promo_end_date, false)) : 0;
                        @endphp
                        
                        <!-- 1. Nom du produit -->
                        <h1 class="product-title">{{ $product->name }}</h1>
                        
                        <!-- 2. Description -->
                        <div class="product-description">
                            <p>{{ $product->description ?? 'Aucune description disponible.' }}</p>
                        </div>
                        
                        <!-- 3. Prix -->
                        <div class="product-price">
                            @if($hasPromotion && $originalPrice)
                                <div class="price-original">
                                    <span class="price-amount-old">{{ number_format($originalPrice, 2) }}</span>
                                    <span class="price-currency">{{ $store->currency ?? 'USD' }}</span>
                                </div>
                                <div class="price-promo">
                                    <span class="price-amount">{{ number_format($currentPrice, 2) }}</span>
                                    <span class="price-currency">{{ $store->currency ?? 'USD' }}</span>
                                    <span class="promo-badge">-{{ $promoPercentage }}%</span>
                                </div>
                            @else
                                <span class="price-amount">{{ number_format($currentPrice, 2) }}</span>
                                <span class="price-currency">{{ $store->currency ?? 'USD' }}</span>
                            @endif
                        </div>

                        @if($hasPromotion && $product->promo_end_date)
                            <div class="promo-countdown" id="promoCountdown">
                                <i class="fas fa-clock"></i>
                                <span class="countdown-text">
                                    <span id="countdown-days">0</span>j 
                                    <span id="countdown-hours">0</span>h 
                                    <span id="countdown-minutes">0</span>m 
                                    <span id="countdown-seconds">0</span>s
                                </span>
                            </div>
                        @endif

                        <!-- 4. Variantes du produit (Taille, Couleur) -->
                        @if(!empty($variants))
                            <div class="product-variants">
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
                                            Longueur/Mesure
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

                        <!-- 5. Sélecteur de quantité (avec espacement modéré) -->
                        <div class="quantity-selector-wrapper">
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
                                       class="quantity-input"
                                       onchange="updateTotalPrice()">
                                <button type="button" class="quantity-btn" onclick="changeQuantity(1)">+</button>
                            </div>
                            <div class="total-price-display">
                                <span class="total-label">Total:</span>
                                <span class="total-amount" id="totalPrice">{{ number_format($currentPrice, 2) }}</span>
                                <span class="total-currency">{{ $store->currency ?? 'USD' }}</span>
                            </div>
                        </div>

                        @if($product->status === 'active')
                            <!-- Boutons d'action personnalisables -->
                            <div class="product-actions">
                                <!-- Bouton Acheter maintenant (personnalisable) -->
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="buy-now-form" onsubmit="event.preventDefault(); addToCartAndCheckout(this);">
                                    @csrf
                                    <input type="hidden" name="quantity" id="buy_quantity" value="1">
                                    <input type="hidden" name="variant_size" id="buy_variant_size" value="">
                                    <input type="hidden" name="variant_color" id="buy_variant_color" value="">
                                    <input type="hidden" name="variant_length" id="buy_variant_length" value="">
                                    <button type="submit" class="btn-buy btn-primary-large{{ $btnClass }}" style="background: {{ $btnBgColor }}; color: {{ $btnTextColor }};">
                                        <i class="fas fa-bolt icon-inline"></i>
                                        {{ $btnText }}
                                    </button>
                                </form>
                                
                                <!-- Bouton Ajouter au panier -->
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="add-to-cart-form" onsubmit="updateHiddenQuantityFields(); updateVariantFields(); return true;">
                                    @csrf
                                    <input type="hidden" name="quantity" id="cart_quantity" value="1">
                                    <input type="hidden" name="variant_size" id="cart_variant_size" value="">
                                    <input type="hidden" name="variant_color" id="cart_variant_color" value="">
                                    <input type="hidden" name="variant_length" id="cart_variant_length" value="">
                                    <button type="submit" class="btn-add-cart btn-secondary-large">
                                        <i class="fas fa-shopping-cart icon-inline"></i>
                                        Ajouter au panier
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Message produit indisponible (en bas) -->
                    @if($product->status !== 'active')
                        <div class="product-inactive-notice-bottom">
                            <i class="fas fa-info-circle"></i> 
                            <span>Ce produit n'est actuellement pas disponible à la vente.</span>
                        </div>
                    @endif
                </div>
                @endif {{-- Fin du @if($store->allow_multiple_products) --}}
            @else
                <div class="empty-products">
                    <i class="fas fa-box-open empty-icon"></i>
                    <p>Aucun produit disponible pour le moment.</p>
                </div>
            @endif
        </div>
    </section>
    
    {{-- Styles et JavaScript pour la grille produits (mode multi-produits) --}}
    @if($store->allow_multiple_products)
    <style>
        .products-grid.minimal-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 3 produits par ligne */
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .product-card.minimal-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
        }
        
        .product-card.minimal-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
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
        
        .product-card.minimal-card:hover .main-product-image {
            transform: scale(1.05);
        }
        
        .product-card-thumbnails {
            display: flex;
            gap: 0.5rem;
            padding: 0.75rem;
            background: white;
            border-top: 1px solid #e5e7eb;
            overflow-x: auto;
            scrollbar-width: thin;
        }
        
        .product-card-thumbnails::-webkit-scrollbar {
            height: 4px;
        }
        
        .product-card-thumbnails::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }
        
        .thumbnail-item {
            flex-shrink: 0;
            width: 60px;
            height: 60px;
            border-radius: 6px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s;
        }
        
        .thumbnail-item:hover {
            border-color: {{ $primary }};
            transform: scale(1.05);
        }
        
        .thumbnail-item.active {
            border-color: {{ $primary }};
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }
        
        .thumbnail-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .promo-badge-card {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            background: #ef4444;
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 700;
            z-index: 2;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .product-card-info {
            padding: 1.5rem;
            position: relative;
            z-index: 1;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
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
            color: {{ $primary }};
        }
        
        .product-card-link:active .product-card-name {
            color: {{ $primary }};
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
            color: {{ $primary }};
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
        
        /* Responsive : 2 produits par ligne sur tablette */
        @media (max-width: 1024px) {
            .products-grid.minimal-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
            }
        }
        
        /* Responsive : 1 produit par ligne sur mobile */
        @media (max-width: 768px) {
            .products-grid.minimal-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .product-card-main-image {
                height: 250px;
            }
            
            .product-card-info {
                padding: 1.25rem;
            }
            
            .thumbnail-item {
                width: 50px;
                height: 50px;
            }
        }
    </style>
    
    <script>
        // Fonction pour changer l'image principale au clic sur une miniature
        function changeProductImage(productId, imageIndex) {
            const slider = document.getElementById('imageSlider_' + productId);
            if (slider) {
                const slides = slider.querySelectorAll('.product-slide');
                slides.forEach((slide, index) => {
                    if (index === imageIndex) {
                        slide.classList.add('active');
                    } else {
                        slide.classList.remove('active');
                    }
                });
            }
            
            // Mettre à jour l'état actif des miniatures
            const thumbnails = document.getElementById('thumbnails_' + productId);
            if (thumbnails) {
                const thumbnailItems = thumbnails.querySelectorAll('.thumbnail-item');
                thumbnailItems.forEach((item, index) => {
                    if (index === imageIndex - 1) { // -1 car la première image n'est pas dans les thumbnails
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
                    }
                });
            }
        }
        
        // Défilement automatique des images pour chaque produit
        document.addEventListener('DOMContentLoaded', function() {
            const productCards = document.querySelectorAll('.product-card.minimal-card');
            
            productCards.forEach(function(card) {
                const productId = card.getAttribute('data-product-id');
                const slider = document.getElementById('imageSlider_' + productId);
                
                if (!slider) return;
                
                const slides = slider.querySelectorAll('.product-slide');
                if (slides.length <= 1) {
                    // S'assurer que la première slide est active même s'il n'y a qu'une image
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
                
                let currentIndex = 0;
                let autoSlideInterval;
                
                // Fonction pour passer à l'image suivante
                function nextImage() {
                    if (slides.length === 0) return;
                    
                    slides[currentIndex].classList.remove('active');
                    currentIndex = (currentIndex + 1) % slides.length;
                    slides[currentIndex].classList.add('active');
                    
                    // Mettre à jour les thumbnails si elles existent
                    const thumbnails = document.getElementById('thumbnails_' + productId);
                    if (thumbnails) {
                        const thumbnailItems = thumbnails.querySelectorAll('.thumbnail-item');
                        thumbnailItems.forEach((item, index) => {
                            if (currentIndex === index + 1) { // +1 car la première image n'est pas dans les thumbnails
                                item.classList.add('active');
                            } else {
                                item.classList.remove('active');
                            }
                        });
                    }
                }
                
                // Démarrer le défilement automatique (toutes les 3 secondes)
                function startAutoSlide() {
                    if (autoSlideInterval) {
                        clearInterval(autoSlideInterval);
                    }
                    autoSlideInterval = setInterval(nextImage, 3000);
                }
                
                // Arrêter le défilement au survol
                function stopAutoSlide() {
                    if (autoSlideInterval) {
                        clearInterval(autoSlideInterval);
                    }
                }
                
                // Reprendre le défilement quand on quitte le survol
                function resumeAutoSlide() {
                    startAutoSlide();
                }
                
                // Démarrer le défilement automatique
                startAutoSlide();
                
                // Gérer le survol
                card.addEventListener('mouseenter', stopAutoSlide);
                card.addEventListener('mouseleave', resumeAutoSlide);
                
                // Permettre le clic sur les thumbnails pour changer manuellement
                const thumbnails = document.getElementById('thumbnails_' + productId);
                if (thumbnails) {
                    const thumbnailItems = thumbnails.querySelectorAll('.thumbnail-item');
                    thumbnailItems.forEach((item, index) => {
                        item.addEventListener('click', function() {
                            stopAutoSlide();
                            // Changer l'image immédiatement
                            slides[currentIndex].classList.remove('active');
                            currentIndex = index + 1; // +1 car la première image n'est pas dans les thumbnails
                            slides[currentIndex].classList.add('active');
                            // Mettre à jour les thumbnails
                            thumbnailItems.forEach((thumbItem, thumbIndex) => {
                                if (thumbIndex === index) {
                                    thumbItem.classList.add('active');
                                } else {
                                    thumbItem.classList.remove('active');
                                }
                            });
                            // Reprendre après 5 secondes
                            setTimeout(resumeAutoSlide, 5000);
                        });
                    });
                }
            });
        });
    </script>
    @endif
    
    {{-- Sections spécifiques au mode 1 produit (bannière, pourquoi choisir, avis) --}}
    @if(isset($product) || ($products->count() > 0 && !$store->allow_multiple_products))
        @if(!isset($product) && $products->count() > 0)
            @php $product = $products->first(); @endphp
        @endif
        
        <!-- Bannière produit avec étapes -->
        @if(!empty($productBannerSteps))
        <section class="product-banner-steps">
            <div class="container">
                <h2 class="steps-title">{{ $t['product_journey'] }}</h2>
                <div class="steps-container">
                    @foreach($productBannerSteps as $index => $step)
                        <div class="step-item">
                            <div class="step-number">{{ $index + 1 }}</div>
                            <div class="step-content">
                                <h3>{{ $step['title'] ?? 'Étape ' . ($index + 1) }}</h3>
                                <p>{{ $step['description'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- Section "Pourquoi choisir ce produit" -->
        @if(!empty($whyChooseProduct))
        <section class="why-choose-section">
            <div class="container">
                <h2 class="section-title">{{ $t['why_choose'] }}</h2>
                <div class="why-choose-content">
                    {!! nl2br(e($whyChooseProduct)) !!}
                </div>
            </div>
        </section>
        @endif

        <!-- Section Avis clients -->
        <section class="reviews-section">
            <div class="container">
                <div class="reviews-header">
                    <h2 class="section-title">{{ $t['customer_reviews'] }}</h2>
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
                                        <img src="{{ $img }}" alt="Avis photo" onclick="openReviewImageModal('{{ $img }}')">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="no-reviews">
                            <i class="fas fa-comment-dots"></i>
                            <p>{{ $t['no_reviews'] }}</p>
                        </div>
                    @endforelse
                </div>

                <!-- Formulaire d'avis -->
                <div class="review-form-container">
                    <h3>{{ $t['leave_review'] }}</h3>
                    <form action="{{ route('product.review.submit', ['slug' => $store->slug, 'id' => $product->id]) }}" method="POST" enctype="multipart/form-data" class="review-form">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label>{{ $t['your_name'] }} *</label>
                                <input type="text" name="customer_name" required>
                            </div>
                            <div class="form-group">
                                <label>{{ $t['email_optional'] }}</label>
                                <input type="email" name="customer_email">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ $t['rating'] }} *</label>
                            <div class="rating-input">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" name="rating" value="{{ $i }}" id="rating{{ $i }}" required>
                                    <label for="rating{{ $i }}" class="rating-star">
                                        <i class="fas fa-star"></i>
                                    </label>
                                @endfor
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ $t['your_review'] }} *</label>
                            <textarea name="comment" rows="4" required minlength="10"></textarea>
                        </div>
                        <div class="form-group">
                            <label>{{ $t['photos_max'] }}</label>
                            <input type="file" name="images[]" multiple accept="image/*" max="5">
                            <small>{{ $t['upload_photos'] }}</small>
                        </div>
                        <button type="submit" class="submit-review-btn">{{ $t['submit_review'] }}</button>
                    </form>
                </div>
            </div>
        </section>
    @endif {{-- Fin du @if($products->count() > 0 && !$store->allow_multiple_products) --}}

    <!-- Section À propos -->
    @if($showAboutSection)
    <section id="about" class="about-section minimal-about">
        <div class="container">
            <div class="about-wrapper">
                <div class="about-header">
                    <h2 class="section-title">{{ $t['about_title'] }} {{ $store->name }}</h2>
                    <div class="title-decoration"></div>
                </div>
                @if($store->description)
                    <div class="about-content">
                        <div class="about-text">
                            <p>{{ $store->description }}</p>
                        </div>
                    </div>
                @endif
                
                <!-- Réseaux sociaux -->
                @if($store->facebook_url || $store->instagram_url || $store->twitter_url || $store->youtube_url)
                    <div class="social-links">
                        <h3 class="social-title">{{ $language === 'en' ? 'Follow Us' : 'Suivez-nous' }}</h3>
                        <div class="social-icons">
                            @if($store->facebook_url)
                                <a href="{{ $store->facebook_url }}" target="_blank" class="social-link" title="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            @endif
                            @if($store->instagram_url)
                                <a href="{{ $store->instagram_url }}" target="_blank" class="social-link" title="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            @endif
                            @if($store->twitter_url)
                                <a href="{{ $store->twitter_url }}" target="_blank" class="social-link" title="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            @endif
                            @if($store->youtube_url)
                                <a href="{{ $store->youtube_url }}" target="_blank" class="social-link" title="YouTube">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    <!-- Section FAQ -->
    @if($showFaqSection)
    <section id="faq" class="faq-section minimal-faq">
        <div class="container">
            <div class="faq-header">
                <h2 class="section-title">{{ $t['faq_title'] }}</h2>
                <div class="title-decoration"></div>
            </div>
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
                    <div class="faq-item" data-index="{{ $index }}">
                        <div class="faq-question-wrapper" onclick="toggleFaq({{ $index }})">
                            <h3 class="faq-question">{{ $faq['q'] }}</h3>
                            <i class="fas fa-chevron-down faq-icon"></i>
                        </div>
                        <div class="faq-answer-wrapper">
                            <p class="faq-answer">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Footer -->
    <footer class="store-footer minimal-footer" style="background: {{ $footerBgColor }}; color: {{ $footerTextColor }};">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3 style="color: {{ $footerTitleColor }};">{{ $store->name }}</h3>
                </div>
                <div class="footer-section">
                    <h4 style="color: {{ $footerTitleColor }};">{{ $t['quick_links'] }}</h4>
                    <ul>
                        @if($showHome)<li><a href="{{ $homeUrl }}" style="color: {{ $footerLinkColor }};">{{ $t['home'] }}</a></li>@endif
                        @if($showProduct)<li><a href="#product" style="color: {{ $footerLinkColor }};">{{ $t['product'] }}</a></li>@endif
                        @if($showAboutSection)<li><a href="#about" style="color: {{ $footerLinkColor }};">{{ $t['about'] }}</a></li>@endif
                        @if($showFaqSection)<li><a href="#faq" style="color: {{ $footerLinkColor }};">{{ $t['faq'] }}</a></li>@endif
                    </ul>
                </div>
                <div class="footer-section">
                    <h4 style="color: {{ $footerTitleColor }};">{{ $t['contact'] }}</h4>
                    <p style="color: {{ $footerTextColor }};">Email: {{ $footerEmail }}</p>
                </div>
            </div>
            
            <!-- Moyens de paiement -->
            <div class="payment-methods">
                <h4 style="color: {{ $footerTitleColor }};">{{ $t['payment_methods'] }}</h4>
                <div class="payment-icons">
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
            
            <div class="footer-bottom" style="color: {{ $footerTextColor }};">
                <p>&copy; {{ date('Y') }} {{ $store->name }}. {{ $t['rights_reserved'] }}.</p>
            </div>
        </div>
    </footer>
</div>

<!-- Modal pour zoomer l'image -->
<div id="imageModal" class="image-modal" onclick="closeImageModal()">
    <span class="modal-close">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<!-- Modal pour images d'avis -->
<div id="reviewImageModal" class="image-modal" onclick="closeReviewImageModal()">
    <span class="modal-close">&times;</span>
    <img class="modal-content" id="reviewModalImage">
</div>

<style>
:root {
    --header-bg: {{ $headerBgColor }};
    --header-text: {{ $headerTextColor }};
}

.store-public-page {
    min-height: 100vh;
    background: {{ $pageBgColor }};
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

/* Menu hamburger pour mobile */
.mobile-menu-toggle {
    display: none;
    flex-direction: column;
    gap: 5px;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
    z-index: 1001;
    color: {{ $headerTextColor }};
}

.mobile-menu-toggle span {
    width: 25px;
    height: 3px;
    background: currentColor;
    border-radius: 2px;
    transition: all 0.3s;
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

.nav-link {
    color: #6b7280;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
    white-space: nowrap;
}

.nav-link:hover {
    color: {{ $headerNavHoverColor }};
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
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
}

.banner-title {
    font-size: 3rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    text-align: center;
    width: 100%;
}

.banner-scrolling-text {
    height: 60px;
    min-height: 60px;
    overflow: visible;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0.5rem 0 1rem 0;
    z-index: 10;
    flex: 0 0 auto;
    width: 100%;
    max-width: 100%;
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
    font-size: 1.25rem;
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
    animation: minimalBannerScrollIn 0.6s ease-out forwards;
}

.scrolling-item.effect-fade.active {
    animation: minimalBannerFade 3s ease-in-out infinite;
}

.scrolling-item.effect-bounce.active {
    animation: minimalBannerBounce 2s ease-in-out infinite;
}

.scrolling-item.effect-slide.active {
    animation: minimalBannerSlide 0.8s ease-out;
}

@keyframes minimalBannerScrollIn {
    from {
        opacity: 0;
        transform: translate(-50%, calc(-50% + 30px));
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%);
    }
}

@keyframes minimalBannerFade {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

@keyframes minimalBannerBounce {
    0%, 100% { transform: translate(-50%, -50%); }
    50% { transform: translate(-50%, calc(-50% - 8px)); }
}

@keyframes minimalBannerSlide {
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

/* Section Produit */
.minimal-product {
    padding: 2.5rem 0;
    background: {{ $pageBgColor }};
}

.product-display {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    max-width: 1100px;
    margin: 0 auto;
    padding: 2rem 0;
}

/* Images à gauche, informations à droite */
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
    background: var(--product-card-bg, #ffffff);
    border-radius: var(--product-card-radius, 16px);
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
    height: 100%;
    min-height: 500px;
    display: flex;
    align-items: center;
    justify-content: center;
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
    gap: 1.25rem;
    padding: 0;
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
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.price-original {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
}

.price-amount-old {
    font-size: 1.25rem;
    font-weight: 500;
    color: #9ca3af;
    text-decoration: line-through;
}

.price-promo {
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

.promo-badge {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 600;
    margin-left: 0.5rem;
}

.promo-countdown {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    padding: 1rem 1.25rem;
    border-radius: 10px;
    font-size: 1rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    animation: pulse 2s infinite;
}

.promo-countdown i {
    font-size: 1.25rem;
}

.countdown-text {
    font-weight: 600;
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.countdown-text span {
    font-size: 1.1rem;
    font-weight: 700;
    background: rgba(255, 255, 255, 0.2);
    padding: 0.25rem 0.5rem;
    border-radius: 5px;
    min-width: 2.5rem;
    text-align: center;
}

/* Variantes */
.product-variants {
    margin: 1rem 0;
    padding: 1.25rem;
    background: #f9fafb;
    border-radius: 12px;
}

.variant-group {
    margin-bottom: 1.5rem;
}

.variant-group:last-child {
    margin-bottom: 0;
}

.variant-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.75rem;
    font-size: 1rem;
}

.variant-options {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.variant-option {
    position: relative;
    cursor: pointer;
}

.variant-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.variant-option span {
    display: inline-block;
    padding: 0.625rem 1.25rem;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 500;
    color: #374151;
    transition: all 0.2s;
    min-width: 60px;
    text-align: center;
}

.variant-option input[type="radio"]:checked + span {
    background: {{ $primary }};
    color: white;
    border-color: {{ $primary }};
}

.variant-option:hover span {
    border-color: {{ $primary }};
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Sélecteur de quantité */
.quantity-selector-wrapper {
    margin: 1.25rem 0;
    padding: 1.25rem;
    background: #f9fafb;
    border-radius: 12px;
}

.quantity-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 1rem;
    font-size: 1rem;
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.quantity-btn {
    width: 40px;
    height: 40px;
    border: 2px solid #e5e7eb;
    background: white;
    border-radius: 8px;
    font-size: 1.25rem;
    font-weight: 600;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.quantity-btn:hover {
    background: {{ $primary }};
    color: white;
    border-color: {{ $primary }};
}

.quantity-input {
    width: 80px;
    padding: 0.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    text-align: center;
    background: white;
}

.quantity-input:focus {
    outline: none;
    border-color: {{ $primary }};
}

.total-price-display {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
    padding-top: 1rem;
    border-top: 2px solid #e5e7eb;
}

.total-label {
    font-weight: 600;
    color: #6b7280;
    font-size: 1rem;
}

.total-amount {
    font-size: 1.75rem;
    font-weight: 700;
    color: {{ $primary }};
}

.total-currency {
    font-size: 1.1rem;
    color: #6b7280;
}

.product-description {
    margin: 0;
}

.product-description h3 {
    display: none;
}

.product-description p {
    font-size: 1rem;
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
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-buy:hover {
    opacity: 0.9;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
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
    background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
}

.minimal-faq {
    background: #ffffff;
}

.about-wrapper, .faq-header {
    max-width: 900px;
    margin: 0 auto;
}

.about-header, .faq-header {
    text-align: center;
    margin-bottom: 3rem;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 1rem 0;
    position: relative;
    display: inline-block;
}

.title-decoration {
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, {{ $primary }}, {{ $secondary }});
    margin: 1rem auto 0;
    border-radius: 2px;
    animation: slideIn 0.6s ease-out;
}

@keyframes slideIn {
    from {
        width: 0;
        opacity: 0;
    }
    to {
        width: 80px;
        opacity: 1;
    }
}

.about-content {
    margin-bottom: 3rem;
}

.about-text {
    background: white;
    padding: 2.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    animation: fadeInUp 0.6s ease-out;
}

.about-text p {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #4b5563;
    margin: 0;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.social-links {
    text-align: center;
    animation: fadeInUp 0.8s ease-out;
}

.social-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 1.5rem 0;
}

.social-icons {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.social-link {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 50%;
    color: #4b5563;
    font-size: 1.5rem;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: all 0.3s;
}

.social-link:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    color: {{ $primary }};
}

.social-link i.fa-facebook-f:hover {
    color: #1877f2;
}

.social-link i.fa-instagram:hover {
    color: #e4405f;
}

.social-link i.fa-twitter:hover {
    color: #1da1f2;
}

.social-link i.fa-youtube:hover {
    color: #ff0000;
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
    max-width: 900px;
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
    transition: all 0.3s ease;
    animation: fadeInUp 0.6s ease-out;
    animation-fill-mode: both;
}

.faq-item:nth-child(1) { animation-delay: 0.1s; }
.faq-item:nth-child(2) { animation-delay: 0.2s; }
.faq-item:nth-child(3) { animation-delay: 0.3s; }

.faq-item:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.faq-item.active {
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}

.faq-question-wrapper {
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: background 0.3s;
    user-select: none;
}

.faq-question-wrapper:hover {
    background: #f9fafb;
}

.faq-item.active .faq-question-wrapper {
    background: linear-gradient(135deg, {{ $primary }}15, {{ $secondary }}15);
}

.faq-question {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
    flex: 1;
    transition: color 0.3s;
}

.faq-item.active .faq-question {
    color: {{ $primary }};
}

.faq-icon {
    font-size: 1rem;
    color: #6b7280;
    transition: all 0.3s;
    margin-left: 1rem;
}

.faq-item.active .faq-icon {
    transform: rotate(180deg);
    color: {{ $primary }};
}

.faq-answer-wrapper {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease-out, padding 0.4s ease-out;
    padding: 0 2rem;
}

.faq-item.active .faq-answer-wrapper {
    max-height: 500px;
    padding: 0 2rem 1.5rem;
}

.faq-answer {
    font-size: 1rem;
    line-height: 1.8;
    color: #4b5563;
    margin: 0;
    animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Footer */
.minimal-footer {
    background: #1f2937;
    color: white;
    padding: 2.5rem 0 1rem;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
    margin-bottom: 1.5rem;
}

.footer-section h3, .footer-section h4 {
    margin: 0 0 0.75rem 0;
    font-size: 1.1rem;
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
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.7);
    font-size: 0.9rem;
}

/* Sélecteur de langue */
.language-selector {
    margin-left: 1rem;
}

.lang-select {
    padding: 0.5rem 1rem;
    border: 1px solid rgba(0,0,0,0.1);
    border-radius: 6px;
    font-size: 0.9rem;
    cursor: pointer;
    outline: none;
}

/* Bannière produit avec étapes */
.product-banner-steps {
    padding: 4rem 0;
    background: {{ $pageBgColor }};
}

.steps-title {
    text-align: center;
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 3rem;
    color: #111827;
}

.steps-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    max-width: 1000px;
    margin: 0 auto;
}

.step-item {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    display: flex;
    gap: 1.5rem;
    align-items: start;
}

.step-number {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    flex-shrink: 0;
}

.step-content h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
    color: #111827;
}

.step-content p {
    margin: 0;
    color: #6b7280;
    line-height: 1.6;
}

/* Section "Pourquoi choisir ce produit" */
.why-choose-section {
    padding: 4rem 0;
    background: white;
}

.why-choose-content {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
    font-size: 1.1rem;
    line-height: 1.8;
    color: #4b5563;
}

/* Section Avis clients */
.reviews-section {
    padding: 4rem 0;
    background: {{ $pageBgColor }};
}

.reviews-header {
    text-align: center;
    margin-bottom: 3rem;
}

.reviews-header .section-title {
    text-align: center;
}

.reviews-list {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    margin-bottom: 3rem;
}

.review-item {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 1rem;
}

.review-author {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.author-avatar {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    font-weight: 700;
}

.author-info h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
    color: #111827;
}

.review-rating {
    display: flex;
    gap: 0.25rem;
}

.review-rating .fa-star {
    color: #d1d5db;
    font-size: 0.9rem;
}

.review-rating .fa-star.active {
    color: #fbbf24;
}

.review-date {
    color: #9ca3af;
    font-size: 0.9rem;
}

.review-comment {
    color: #4b5563;
    line-height: 1.7;
    margin: 0 0 1rem 0;
}

.review-images {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-top: 1rem;
}

.review-images img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.2s;
}

.review-images img:hover {
    transform: scale(1.1);
}

.no-reviews {
    text-align: center;
    padding: 3rem 2rem;
    color: #9ca3af;
}

.no-reviews i {
    font-size: 4rem;
    margin-bottom: 1rem;
    display: block;
}

.review-form-container {
    background: var(--review-card-bg, #ffffff);
    padding: 2.5rem;
    border-radius: var(--review-card-radius, 12px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    max-width: 800px;
    margin: 0 auto;
}

.review-form-container h3 {
    margin: 0 0 2rem 0;
    font-size: 1.5rem;
    color: #111827;
}

.review-form .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.review-form .form-group {
    margin-bottom: 1.5rem;
}

.review-form label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #374151;
}

.review-form input[type="text"],
.review-form input[type="email"],
.review-form textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 1rem;
}

.review-form input[type="file"] {
    width: 100%;
    padding: 0.5rem;
}

.rating-input {
    display: flex;
    gap: 0.5rem;
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.rating-input input[type="radio"] {
    display: none;
}

.rating-input label {
    cursor: pointer;
    font-size: 2rem;
    color: #d1d5db;
    transition: color 0.2s;
}

.rating-input input[type="radio"]:checked ~ label,
.rating-input label:hover,
.rating-input label:hover ~ label {
    color: #fbbf24;
}

.submit-review-btn {
    width: 100%;
    padding: 1rem 2rem;
    background: {{ $primary }};
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s;
}

.submit-review-btn:hover {
    opacity: 0.9;
}

/* Moyens de paiement */
.payment-methods {
    margin: 1.5rem 0;
    padding: 1.5rem 0;
    border-top: 1px solid rgba(255,255,255,0.1);
    border-bottom: 1px solid rgba(255,255,255,0.1);
    text-align: center;
}

.payment-methods h4 {
    margin: 0 0 1rem 0;
    font-size: 1rem;
}

.payment-icons {
    display: flex;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
}

.payment-icon {
    font-size: 3rem;
    color: rgba(255,255,255,0.8);
    transition: transform 0.2s, color 0.2s;
    cursor: pointer;
}

.payment-icon:hover {
    transform: scale(1.1);
    color: white;
}

/* Modal pour images d'avis */
#reviewImageModal {
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

#reviewImageModal.active {
    display: flex;
}

#reviewImageModal img {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
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

/* Responsive Mobile */
@media (max-width: 768px) {
    /* Header Mobile */
    .store-header-content {
        flex-wrap: nowrap;
        position: relative;
        gap: 1rem;
    }
    
    .store-brand {
        flex: 1;
        min-width: 0;
        gap: 0.75rem;
    }
    
    .store-logo {
        width: 40px;
        height: 40px;
    }
    
    .store-name {
        font-size: 1rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin: 0;
    }
    
    .mobile-menu-toggle {
        display: flex;
        margin-left: auto;
        flex-shrink: 0;
    }
    
    .store-nav {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: {{ $headerBgColor }};
        flex-direction: column;
        align-items: stretch;
        padding: 0;
        gap: 0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        z-index: 1000;
        overflow-y: auto;
    }
    
    .store-nav.active {
        transform: translateX(0);
    }
    
    .nav-link {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
        font-size: 1rem;
    }
    
    .nav-link:last-child {
        border-bottom: none;
    }
    
    .nav-link:hover {
        background: rgba(0,0,0,0.03);
        color: {{ $primary }};
    }
    
    .language-selector {
        margin: 1rem 1.5rem;
        width: auto;
    }
    
    .lang-select {
        width: 100%;
    }
    
    /* Bannière Mobile */
    .minimal-banner {
        min-height: 300px;
    }
    
    .banner-title {
        font-size: 1.75rem;
    }
    
    .banner-scrolling-text {
        height: 50px;
    }
    
    .scrolling-item {
        font-size: 1rem;
    }
    
    /* Produit Mobile */
    .product-display {
        grid-template-columns: 1fr;
        gap: 2rem;
        padding: 1rem 0;
    }
    
    .product-images-wrapper {
        order: 1;
    }
    
    .product-info-wrapper {
        order: 2;
    }
    
    .image-slider {
        height: 350px;
    }
    
    .product-title {
        font-size: 1.5rem;
    }
    
    .price-amount {
        font-size: 1.75rem;
    }
    
    .product-actions {
        flex-direction: column;
    }
    
    .btn-buy, .btn-add-cart {
        width: 100%;
        padding: 1rem 1.5rem;
        font-size: 1.1rem;
    }
    
    /* Sections Mobile */
    .minimal-about, .minimal-faq {
        padding: 2.5rem 0;
    }
    
    .section-title {
        font-size: 1.75rem;
    }
    
    .about-text {
        padding: 1.5rem;
    }
    
    .about-text p {
        font-size: 1rem;
    }
    
    .social-icons {
        gap: 1rem;
    }
    
    .social-link {
        width: 45px;
        height: 45px;
        font-size: 1.25rem;
    }
    
    /* FAQ Mobile */
    .faq-list {
        gap: 0.75rem;
    }
    
    .faq-item {
        padding: 1.25rem;
    }
    
    .faq-question {
        font-size: 1.1rem;
    }
    
    .faq-answer {
        font-size: 0.95rem;
    }
    
    /* Avis Mobile */
    .reviews-section {
        padding: 2.5rem 0;
    }
    
    .review-item {
        padding: 1.5rem;
    }
    
    .review-form-container {
        padding: 1.5rem;
    }
    
    .review-form .form-row {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    /* Footer Mobile */
    .footer-content {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .payment-methods {
        margin: 1rem 0;
        padding: 1rem 0;
    }
    
    .payment-icons {
        gap: 1.5rem;
    }
    
    .payment-icon {
        font-size: 2.5rem;
    }
    
    /* Bannière produit avec étapes Mobile */
    .steps-container {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .step-item {
        padding: 1.5rem;
    }
    
    /* Container Mobile */
    .container {
        padding: 0 1rem;
    }
}

@media (max-width: 480px) {
    .store-name {
        font-size: 1rem;
    }
    
    .banner-title {
        font-size: 1.5rem;
    }
    
    .product-title {
        font-size: 1.25rem;
    }
    
    .section-title {
        font-size: 1.5rem;
    }
    
    .steps-title {
        font-size: 1.75rem;
    }
}
</style>

<script>
// Exposer le prix unitaire au JavaScript
@if(isset($currentPrice))
window.productUnitPrice = {{ $currentPrice }};
@elseif(isset($product))
window.productUnitPrice = {{ $product->selling_price }};
@else
window.productUnitPrice = 0;
@endif

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

// Texte défilant dans la bannière (effets par texte)
(function initMinimalBannerTexts() {
    const container = document.querySelector('.banner-scrolling-text');
    if (!container) {
        console.log('Banner text container not found (minimal)');
        return;
    }

    const items = Array.from(container.querySelectorAll('.scrolling-item'));
    if (!items.length) {
        console.log('No banner text items found (minimal)');
        return;
    }
    
    console.log('Found', items.length, 'banner text items (minimal)');

    const defaultAnimation = container.dataset.defaultAnimation || 'scroll';

    // Stocker le texte complet pour chaque élément
    items.forEach(item => {
        if (!item.dataset.fullText) {
            item.dataset.fullText = item.textContent;
        }
    });

    let currentIndex = 0;
    let timer = null;

    function showItem(index) {
        if (timer) {
            clearTimeout(timer);
            timer = null;
        }

        const item = items[index];
        const effect = item.dataset.effect || defaultAnimation;

        // Réinitialiser tous les items
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
                    item.removeChild(cursor);
                    item.textContent = fullText.substring(0, charIndex + 1);
                    item.appendChild(cursor);
                    charIndex++;
                    timer = setTimeout(typeChar, typingSpeed);
                } else {
                    // Texte complet écrit, retirer le curseur et attendre
                    setTimeout(() => {
                        if (item.contains(cursor)) {
                            item.removeChild(cursor);
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
        }
    }

    if (items.length === 1) {
        // Un seul texte : l'afficher avec son effet (typewriter ou autre)
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
            
            // Ajouter un curseur clignotant
            const cursor = document.createElement('span');
            cursor.className = 'typewriter-cursor';
            cursor.textContent = '|';
            cursor.style.opacity = '1';
            cursor.style.animation = 'blinkCursor 1s infinite';
            single.appendChild(cursor);

            function typeCharSingle() {
                if (charIndex < fullText.length) {
                    single.removeChild(cursor);
                    single.textContent = fullText.substring(0, charIndex + 1);
                    single.appendChild(cursor);
                    charIndex++;
                    setTimeout(typeCharSingle, typingSpeed);
                } else {
                    // Retirer le curseur après un court délai
                    setTimeout(() => {
                        if (single.contains(cursor)) {
                            single.removeChild(cursor);
                        }
                    }, 1000);
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
})();

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
        closeReviewImageModal();
    }
});

// Fonctions pour le modal des images d'avis
function openReviewImageModal(imageSrc) {
    const modal = document.getElementById('reviewImageModal');
    const modalImg = document.getElementById('reviewModalImage');
    modalImg.src = imageSrc;
    modal.classList.add('active');
}

function closeReviewImageModal() {
    const modal = document.getElementById('reviewImageModal');
    modal.classList.remove('active');
}

// Sélecteur de langue
document.getElementById('languageSelect')?.addEventListener('change', function(e) {
    const lang = e.target.value;
    // Envoyer une requête pour changer la langue
    fetch('{{ route("store.language.update", $store->slug) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ language: lang })
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    }).catch(() => {
        // En cas d'erreur, recharger avec paramètre URL
        const url = new URL(window.location);
        url.searchParams.set('lang', lang);
        window.location.href = url.toString();
    });
});

// Accordéon FAQ
function toggleFaq(index) {
    const faqItem = document.querySelector(`.faq-item[data-index="${index}"]`);
    if (!faqItem) {
        console.error('FAQ item not found for index:', index);
        return;
    }
    
    const isActive = faqItem.classList.contains('active');
    
    // Fermer tous les autres items
    document.querySelectorAll('.faq-item').forEach(item => {
        if (item !== faqItem) {
            item.classList.remove('active');
        }
    });
    
    // Toggle l'item actuel
    if (isActive) {
        faqItem.classList.remove('active');
    } else {
        faqItem.classList.add('active');
    }
}

// S'assurer que toggleFaq est accessible globalement
window.toggleFaq = toggleFaq;

// Menu hamburger mobile
function toggleMobileMenu() {
    const nav = document.getElementById('mainNav');
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

function closeMobileMenu() {
    const nav = document.getElementById('mainNav');
    const toggle = document.querySelector('.mobile-menu-toggle');
    if (nav && toggle) {
        nav.classList.remove('active');
        toggle.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Fermer le menu au clic en dehors
document.addEventListener('click', function(e) {
    const nav = document.getElementById('mainNav');
    const toggle = document.querySelector('.mobile-menu-toggle');
    
    if (nav && toggle && nav.classList.contains('active')) {
        if (!nav.contains(e.target) && !toggle.contains(e.target)) {
            closeMobileMenu();
        }
    }
});

// Ajouter au panier et rediriger vers checkout
// Gestion de la quantité
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
    
    // Mettre à jour les champs cachés des formulaires
    const buyQuantity = document.getElementById('buy_quantity');
    const cartQuantity = document.getElementById('cart_quantity');
    
    if (buyQuantity) buyQuantity.value = quantity;
    if (cartQuantity) cartQuantity.value = quantity;
}

// Gestion des variantes
function updateVariantFields() {
    // Récupérer les variantes sélectionnées
    const sizeInput = document.querySelector('input[name="variant_size"]:checked');
    const colorInput = document.querySelector('input[name="variant_color"]:checked');
    const lengthInput = document.querySelector('input[name="variant_length"]:checked');
    
    const size = sizeInput ? sizeInput.value : '';
    const color = colorInput ? colorInput.value : '';
    const length = lengthInput ? lengthInput.value : '';
    
    // Mettre à jour les champs cachés des formulaires
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

// Écouter les changements de variantes
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Compteur de promotion en temps réel (jours, heures, minutes, secondes)
    @if(isset($hasPromotion) && $hasPromotion && isset($product) && $product->promo_end_date)
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
                const textElement = countdownElement.querySelector('.countdown-text');
                if (textElement) {
                    textElement.innerHTML = '<strong>Promotion terminée</strong>';
                }
            }
        }
        
        // Mettre à jour immédiatement puis toutes les secondes
        updatePromoCountdown();
        setInterval(updatePromoCountdown, 1000);
    @endif
});

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
}

/* Message produit indisponible en bas */
.product-inactive-notice-bottom {
    grid-column: 1 / -1;
    background: #fef3c7;
    color: #92400e;
    padding: 1.25rem 1.5rem;
    border-radius: 12px;
    text-align: center;
    margin-top: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    font-weight: 500;
}

.product-inactive-notice-bottom i {
    font-size: 1.25rem;
}
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}
</style>
@endsection

