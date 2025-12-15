@extends('layouts.app')

@section('title', 'Personnaliser ma boutique - Dropshipping Platform')

@section('content')
<div class="customize-page-wrapper">
    <!-- Header avec bouton prévisualisation -->
    <div class="customize-header">
        <div class="header-content">
            <h1 class="customize-title">
                <i class="fas fa-palette icon-inline"></i>
                Personnaliser ma boutique
            </h1>
            <div class="header-actions">
                <button type="button" class="btn-preview" onclick="openPreviewModal()">
                    <i class="fas fa-eye icon-inline"></i>
                    Prévisualiser
                </button>
                <a href="{{ $store->publicUrl }}" target="_blank" class="btn-secondary">
                    <i class="fas fa-external-link-alt icon-inline"></i>
                    Ouvrir dans un nouvel onglet
                </a>
            </div>
        </div>
    </div>

    <div class="customize-layout">
        <!-- Sidebar de navigation -->
        <aside class="customize-sidebar">
            <nav class="sidebar-nav">
                <!-- Option 1: Personnaliser ma boutique (expandable) -->
                <div class="nav-group">
                    <div class="nav-group-header active" onclick="toggleNavGroup(this)">
                        <i class="fas fa-palette icon-inline"></i>
                        <span>Personnaliser ma boutique</span>
                        <i class="fas fa-chevron-down nav-arrow"></i>
                    </div>
                    <div class="nav-group-content expanded">
                        <a href="#identity" class="nav-item active" data-section="identity">
                            <i class="fas fa-id-card icon-inline"></i>
                            <span>Identité</span>
                        </a>
                        <a href="#appearance" class="nav-item" data-section="appearance">
                            <i class="fas fa-palette icon-inline"></i>
                            <span>Apparence & Thème</span>
                        </a>
                        <a href="#buttons" class="nav-item" data-section="buttons">
                            <i class="fas fa-hand-pointer icon-inline"></i>
                            <span>Boutons d'action</span>
                        </a>
                        <a href="#layout" class="nav-item" data-section="layout">
                            <i class="fas fa-th-list icon-inline"></i>
                            <span>Navigation & Sections</span>
                        </a>
                        <a href="#domain" class="nav-item" data-section="domain">
                            <i class="fas fa-globe icon-inline"></i>
                            <span>Nom de domaine</span>
                        </a>
                    </div>
                </div>

                <!-- Option 2: Gestion et opération -->
                <div class="nav-group">
                    <div class="nav-group-header" onclick="toggleNavGroup(this)">
                        <i class="fas fa-cogs icon-inline"></i>
                        <span>Gestion et opération</span>
                        <i class="fas fa-chevron-down nav-arrow"></i>
                    </div>
                    <div class="nav-group-content">
                        <a href="#operations" class="nav-item" data-section="operations">
                            <i class="fas fa-tasks icon-inline"></i>
                            <span>Opérations</span>
                        </a>
                    </div>
                </div>

                <!-- Option 3: Gestion du compte -->
                <div class="nav-group">
                    <div class="nav-group-header" onclick="toggleNavGroup(this)">
                        <i class="fas fa-user-cog icon-inline"></i>
                        <span>Gestion du compte</span>
                        <i class="fas fa-chevron-down nav-arrow"></i>
                    </div>
                    <div class="nav-group-content">
                        <a href="#account" class="nav-item" data-section="account">
                            <i class="fas fa-user icon-inline"></i>
                            <span>Mon compte</span>
                        </a>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Contenu principal -->
        <main class="customize-main">
            <form action="{{ route('merchant.store.update') }}" method="POST" id="customizeForm">
                @csrf
                
                <!-- Section 1: Identité de la boutique -->
                <section id="identity" class="customize-section active">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-id-card icon-inline"></i>
                            Identité de la boutique
                        </h2>
                        <p class="section-description">Personnalisez le nom, le logo et la description de votre boutique</p>
                    </div>

                    <div class="section-content">
                        <!-- Nom de la boutique -->
                        <div class="form-group">
                            <label for="name" class="form-label">
                                Nom de la boutique <span class="required">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   class="form-input @error('name') error @enderror" 
                                   value="{{ old('name', $store->name) }}" 
                                   required
                                   placeholder="Ex: Ma Super Boutique">
                            @error('name')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Logo -->
                        <div class="form-group">
                            <label for="logo" class="form-label">
                                <i class="fas fa-image icon-inline"></i>
                                Logo de la boutique
                            </label>
                            <div class="logo-upload-wrapper">
                                <div class="logo-upload-section">
                                    <input type="url" 
                                           id="logo" 
                                           name="logo" 
                                           class="form-input @error('logo') error @enderror" 
                                           value="{{ old('logo', $store->logo) }}" 
                                           placeholder="https://exemple.com/logo.png">
                                    <button type="button" class="btn-secondary btn-upload" onclick="document.getElementById('logo_file').click()">
                                        <i class="fas fa-upload icon-inline"></i>
                                        Uploader
                                    </button>
                                    <input type="file" 
                                           id="logo_file" 
                                           name="logo_file" 
                                           accept="image/*" 
                                           style="display: none;"
                                           onchange="handleLogoUpload(this)">
                                </div>
                                @error('logo')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                                <div id="logo_preview" class="logo-preview">
                                    @if($store->logo)
                                        <div class="preview-wrapper">
                                            <img src="{{ $store->logo }}" alt="Logo" class="preview-image">
                                            <button type="button" class="btn-remove" onclick="removeLogo()">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description" class="form-label">
                                Description de la boutique
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      class="form-textarea @error('description') error @enderror" 
                                      rows="5"
                                      placeholder="Décrivez votre boutique en quelques lignes...">{{ old('description', $store->description) }}</textarea>
                            @error('description')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Réseaux sociaux -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-share-alt icon-inline"></i>
                                Réseaux sociaux
                            </label>
                            <p class="form-hint">Ajoutez les liens de vos réseaux sociaux pour qu'ils apparaissent sur votre boutique</p>
                            
                            <div class="social-inputs">
                                <div class="social-input-group">
                                    <label for="facebook_url" class="social-label">
                                        <i class="fab fa-facebook-f"></i>
                                        Facebook
                                    </label>
                                    <input type="url" 
                                           id="facebook_url" 
                                           name="facebook_url" 
                                           class="form-input" 
                                           value="{{ old('facebook_url', $store->facebook_url ?? '') }}" 
                                           placeholder="https://facebook.com/votre-page">
                                </div>

                                <div class="social-input-group">
                                    <label for="instagram_url" class="social-label">
                                        <i class="fab fa-instagram"></i>
                                        Instagram
                                    </label>
                                    <input type="url" 
                                           id="instagram_url" 
                                           name="instagram_url" 
                                           class="form-input" 
                                           value="{{ old('instagram_url', $store->instagram_url ?? '') }}" 
                                           placeholder="https://instagram.com/votre-compte">
                                </div>

                                <div class="social-input-group">
                                    <label for="twitter_url" class="social-label">
                                        <i class="fab fa-twitter"></i>
                                        Twitter
                                    </label>
                                    <input type="url" 
                                           id="twitter_url" 
                                           name="twitter_url" 
                                           class="form-input" 
                                           value="{{ old('twitter_url', $store->twitter_url ?? '') }}" 
                                           placeholder="https://twitter.com/votre-compte">
                                </div>

                                <div class="social-input-group">
                                    <label for="youtube_url" class="social-label">
                                        <i class="fab fa-youtube"></i>
                                        YouTube
                                    </label>
                                    <input type="url" 
                                           id="youtube_url" 
                                           name="youtube_url" 
                                           class="form-input" 
                                           value="{{ old('youtube_url', $store->youtube_url ?? '') }}" 
                                           placeholder="https://youtube.com/votre-chaine">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 2: Apparence et Thème -->
                <section id="appearance" class="customize-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-palette icon-inline"></i>
                            Apparence et Thème
                        </h2>
                        <p class="section-description">Personnalisez le thème, les couleurs et la typographie de votre boutique</p>
                    </div>

                    <div class="section-content">
                        <!-- Sélection du thème -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-palette icon-inline"></i>
                                Thème de la boutique
                            </label>
                            <p class="form-hint">Choisissez un thème pour votre boutique. Les changements sont visibles en temps réel.</p>
                            
                            <div class="templates-grid">
                                @foreach($templates as $template)
                                    <label class="template-card {{ old('template_id', $store->template_id) == $template->id ? 'selected' : '' }}">
                                        <input type="radio" 
                                               name="template_id" 
                                               value="{{ $template->id }}" 
                                               {{ old('template_id', $store->template_id) == $template->id ? 'checked' : '' }}
                                               onchange="updatePreview()">
                                        @php
                                            $previewBg = match($template->slug) {
                                                'classic' => 'linear-gradient(135deg, #667eea, #764ba2)',
                                                'modern' => 'linear-gradient(135deg, #6366f1, #8b5cf6)',
                                                'premium' => 'linear-gradient(135deg, #1a1a2e, #16213e)',
                                                'minimal' => 'linear-gradient(135deg, #0ea5e9, #a855f7)',
                                                'vibrant' => 'linear-gradient(135deg, #f97316, #6366f1)',
                                                'elegant' => 'linear-gradient(135deg, #111827, #1f2937)',
                                                'aurora' => 'linear-gradient(135deg, #0ea5e9, #8b5cf6)',
                                                default => 'linear-gradient(135deg, #0ea5e9, #a855f7)',
                                            };
                                        @endphp
                                        <div class="template-preview">
                                            <div class="template-preview-header" style="background: {{ $previewBg }}">
                                                <div class="template-preview-dots">
                                                    <span></span><span></span><span></span>
                                                </div>
                                            </div>
                                            <div class="template-preview-body">
                                                <div class="template-preview-bar"></div>
                                                <div class="template-preview-bars">
                                                    <div class="preview-bar"></div>
                                                    <div class="preview-bar short"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="template-info">
                                            <div class="template-name">{{ $template->name }}</div>
                                            <div class="template-desc">{{ Str::limit($template->description, 60) }}</div>
                                        </div>
                                        <div class="template-check">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('template_id')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Couleurs -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-fill-drip icon-inline"></i>
                                Couleurs de la boutique
                            </label>
                            <p class="form-hint">Sélectionnez des couleurs prédéfinies ou personnalisez-les</p>
                            
                            <!-- Couleurs prédéfinies -->
                            <div class="preset-colors">
                                <div class="preset-colors-label">Couleurs prédéfinies :</div>
                                <div class="preset-colors-grid">
                                    @php
                                        $presetColors = [
                                            ['name' => 'Bleu & Blanc', 'primary' => '#0ea5e9', 'secondary' => '#ffffff', 'accent' => '#0284c7'],
                                            ['name' => 'Violet & Rose', 'primary' => '#a855f7', 'secondary' => '#ec4899', 'accent' => '#f97316'],
                                            ['name' => 'Vert & Nature', 'primary' => '#10b981', 'secondary' => '#34d399', 'accent' => '#f59e0b'],
                                            ['name' => 'Rouge & Orange', 'primary' => '#ef4444', 'secondary' => '#f97316', 'accent' => '#fbbf24'],
                                            ['name' => 'Sombre & Élégant', 'primary' => '#1f2937', 'secondary' => '#374151', 'accent' => '#6366f1'],
                                            ['name' => 'Rose & Pêche', 'primary' => '#f43f5e', 'secondary' => '#fb7185', 'accent' => '#fbbf24'],
                                        ];
                                    @endphp
                                    @foreach($presetColors as $preset)
                                        <div class="preset-color-item" onclick="applyPresetColors('{{ $preset['primary'] }}', '{{ $preset['secondary'] }}', '{{ $preset['accent'] }}')">
                                            <div class="preset-colors-preview">
                                                <div class="preset-color-box" style="background: {{ $preset['primary'] }}"></div>
                                                <div class="preset-color-box" style="background: {{ $preset['secondary'] }}"></div>
                                                <div class="preset-color-box" style="background: {{ $preset['accent'] }}"></div>
                                            </div>
                                            <div class="preset-color-name">{{ $preset['name'] }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Personnalisation des couleurs -->
                            <div class="custom-colors">
                                <div class="custom-colors-label">Personnaliser :</div>
                                <div class="color-picker-group">
                                    <div class="color-picker-item">
                                        <label for="primary_color" class="color-label">
                                            Couleur principale
                                            <button type="button" class="btn-edit-color" onclick="editColor('primary_color')" title="Modifier">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                        </label>
                                        <div class="color-input-wrapper">
                                            <input type="color" 
                                                   id="primary_color" 
                                                   name="primary_color" 
                                                   class="color-picker" 
                                                   value="{{ old('primary_color', $store->primary_color ?? '#0ea5e9') }}"
                                                   onchange="updateColorPreview()">
                                            <input type="text" 
                                                   id="primary_color_text" 
                                                   class="color-text-input" 
                                                   value="{{ old('primary_color', $store->primary_color ?? '#0ea5e9') }}"
                                                   onchange="updateColorFromText('primary_color', this.value)">
                                        </div>
                                    </div>

                                    <div class="color-picker-item">
                                        <label for="secondary_color" class="color-label">
                                            Couleur secondaire
                                            <button type="button" class="btn-edit-color" onclick="editColor('secondary_color')" title="Modifier">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                        </label>
                                        <div class="color-input-wrapper">
                                            <input type="color" 
                                                   id="secondary_color" 
                                                   name="secondary_color" 
                                                   class="color-picker" 
                                                   value="{{ old('secondary_color', $store->secondary_color ?? '#a855f7') }}"
                                                   onchange="updateColorPreview()">
                                            <input type="text" 
                                                   id="secondary_color_text" 
                                                   class="color-text-input" 
                                                   value="{{ old('secondary_color', $store->secondary_color ?? '#a855f7') }}"
                                                   onchange="updateColorFromText('secondary_color', this.value)">
                                        </div>
                                    </div>

                                    <div class="color-picker-item">
                                        <label for="accent_color" class="color-label">
                                            Couleur accent
                                            <button type="button" class="btn-edit-color" onclick="editColor('accent_color')" title="Modifier">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                        </label>
                                        <div class="color-input-wrapper">
                                            <input type="color" 
                                                   id="accent_color" 
                                                   name="accent_color" 
                                                   class="color-picker" 
                                                   value="{{ old('accent_color', $store->accent_color ?? '#f97316') }}"
                                                   onchange="updateColorPreview()">
                                            <input type="text" 
                                                   id="accent_color_text" 
                                                   class="color-text-input" 
                                                   value="{{ old('accent_color', $store->accent_color ?? '#f97316') }}"
                                                   onchange="updateColorFromText('accent_color', this.value)">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aperçu des couleurs -->
                            <div class="color-preview-card">
                                <div class="preview-card-header" id="colorPreviewHeader">
                                    <h3>Aperçu</h3>
                                    <p>Votre boutique avec ces couleurs</p>
                                </div>
                            </div>
                        </div>

                        <!-- Typographie -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-font icon-inline"></i>
                                Typographie
                            </label>
                            <p class="form-hint">Choisissez les polices pour les titres et le contenu de votre boutique</p>
                            
                            <div class="typography-group">
                                <div class="typography-item">
                                    <label for="heading_font" class="typography-label">
                                        Police des titres
                                    </label>
                                    <select id="heading_font" name="heading_font" class="form-select" onchange="updateFontPreview()">
                                        @php
                                            $fonts = [
                                                'Inter' => 'Inter (Moderne)',
                                                'Roboto' => 'Roboto (Lisible)',
                                                'Poppins' => 'Poppins (Élégant)',
                                                'Montserrat' => 'Montserrat (Bold)',
                                                'Open Sans' => 'Open Sans (Classique)',
                                                'Lato' => 'Lato (Professionnel)',
                                                'Raleway' => 'Raleway (Stylé)',
                                                'Playfair Display' => 'Playfair Display (Élégant)',
                                            ];
                                        @endphp
                                        @foreach($fonts as $fontValue => $fontName)
                                            <option value="{{ $fontValue }}" {{ old('heading_font', $store->heading_font ?? 'Inter') == $fontValue ? 'selected' : '' }}>
                                                {{ $fontName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="font-preview" id="headingFontPreview" style="font-family: '{{ old('heading_font', $store->heading_font ?? 'Inter') }}', sans-serif;">
                                        Exemple de titre
                                    </div>
                                </div>

                                <div class="typography-item">
                                    <label for="body_font" class="typography-label">
                                        Police du contenu
                                    </label>
                                    <select id="body_font" name="body_font" class="form-select" onchange="updateFontPreview()">
                                        @foreach($fonts as $fontValue => $fontName)
                                            <option value="{{ $fontValue }}" {{ old('body_font', $store->body_font ?? 'Inter') == $fontValue ? 'selected' : '' }}>
                                                {{ $fontName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="font-preview" id="bodyFontPreview" style="font-family: '{{ old('body_font', $store->body_font ?? 'Inter') }}', sans-serif;">
                                        Exemple de texte de contenu. Cette police sera utilisée pour le corps du texte de votre boutique.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 3: Boutons d'action -->
                <section id="buttons" class="customize-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-hand-pointer icon-inline"></i>
                            Boutons d'action
                        </h2>
                        <p class="section-description">Personnalisez le texte et l'animation des boutons d'achat.</p>
                    </div>

                    <div class="section-content">
                        <div class="form-group">
                            <label class="form-label">
                                Texte du bouton d'achat
                            </label>
                            <input type="text"
                                   name="button_text"
                                   class="form-input"
                                   maxlength="50"
                                   value="{{ old('button_text', $store->button_text ?? 'Acheter maintenant') }}"
                                   placeholder="Acheter maintenant">
                            <p class="form-hint">50 caractères max.</p>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Animation du bouton
                            </label>
                            @php
                                $animations = [
                                    'none' => 'Aucune',
                                    'bounce' => 'Rebondir',
                                    'pulse' => 'Pulsation',
                                    'shake' => 'Secouer',
                                    'glow' => 'Briller'
                                ];
                            @endphp
                            <select name="button_animation" class="form-select">
                                @foreach($animations as $key => $label)
                                    <option value="{{ $key }}" {{ old('button_animation', $store->button_animation ?? 'none') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Bouton sur les cartes produit (listes)
                            </label>
                            <label class="checkbox-item" style="width: fit-content;">
                                <input type="checkbox" name="show_buy_button_on_card" value="1" {{ old('show_buy_button_on_card', $store->show_buy_button_on_card ?? true) ? 'checked' : '' }}>
                                Afficher un bouton d'action sur les cartes produit (si le thème le supporte)
                            </label>
                        </div>
                    </div>
                </section>

                <!-- Section 3: Navigation, Sections, FAQ, Pied de page -->
                <section id="layout" class="customize-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-th-list icon-inline"></i>
                            Navigation & Sections
                        </h2>
                        <p class="section-description">Choisissez quels éléments afficher dans le menu et sur la page.</p>
                    </div>

                    <div class="section-content">
                        <!-- Navigation -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-bars icon-inline"></i>
                                Menu principal
                            </label>
                            <div class="checkbox-grid">
                                @php
                                    $s = $store->settings ?? [];
                                @endphp
                                <label class="checkbox-item">
                                    <input type="checkbox" name="settings[nav_home]" value="1" {{ ($s['nav_home'] ?? true) ? 'checked' : '' }}>
                                    Accueil
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="settings[nav_product]" value="1" {{ ($s['nav_product'] ?? true) ? 'checked' : '' }}>
                                    Produit
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="settings[nav_about]" value="1" {{ ($s['nav_about'] ?? true) ? 'checked' : '' }}>
                                    À propos
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="settings[nav_faq]" value="1" {{ ($s['nav_faq'] ?? true) ? 'checked' : '' }}>
                                    FAQ
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="settings[nav_cart]" value="1" {{ ($s['nav_cart'] ?? true) ? 'checked' : '' }}>
                                    Panier
                                </label>
                            </div>
                        </div>

                        <!-- Sections -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-layer-group icon-inline"></i>
                                Sections de la page
                            </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-item">
                                    <input type="checkbox" name="settings[section_about]" value="1" {{ ($s['section_about'] ?? true) ? 'checked' : '' }}>
                                    Afficher la section À propos
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="settings[section_faq]" value="1" {{ ($s['section_faq'] ?? true) ? 'checked' : '' }}>
                                    Afficher la section FAQ
                                </label>
                            </div>
                        </div>

                        <!-- FAQ personnalisable -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-question-circle icon-inline"></i>
                                Questions fréquentes (3 entrées)
                            </label>
                            @for($i=1; $i<=3; $i++)
                                <div class="faq-item-inputs">
                                    <input type="text" name="settings[faq][{{$i}}][q]" class="form-input" placeholder="Question {{$i}}" value="{{ $s['faq'][$i]['q'] ?? '' }}">
                                    <textarea name="settings[faq][{{$i}}][a]" class="form-textarea" rows="2" placeholder="Réponse {{$i}}">{{ $s['faq'][$i]['a'] ?? '' }}</textarea>
                                </div>
                            @endfor
                        </div>

                        <!-- Pied de page -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-envelope icon-inline"></i>
                                Email du pied de page
                            </label>
                            <input type="email" name="settings[footer_email]" class="form-input" placeholder="email@boutique.com" value="{{ $s['footer_email'] ?? ($store->user->email ?? '') }}">
                            <p class="form-hint">Par défaut : l'email du compte, mais vous pouvez le remplacer ici.</p>
                        </div>
                    </div>
                </section>

                <!-- Sections suivantes seront ajoutées progressivement -->
                <div class="form-actions">
                    <button type="submit" class="btn-primary btn-large">
                        <i class="fas fa-save icon-inline"></i>
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </main>
    </div>
</div>

<!-- Modal de prévisualisation -->
<div id="previewModal" class="preview-modal" onclick="if(event.target.id === 'previewModal') closePreviewModal()">
    <div class="preview-modal-content">
        <div class="preview-modal-header">
            <h3>Prévisualisation de votre boutique</h3>
            <div class="preview-actions">
                <button type="button" class="btn-mobile-toggle" onclick="toggleMobilePreview()">
                    <i class="fas fa-mobile-alt"></i>
                </button>
                <button type="button" class="btn-close-modal" onclick="closePreviewModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="preview-iframe-wrapper">
            <iframe id="previewIframe" src="{{ $store->publicUrl }}" frameborder="0"></iframe>
        </div>
    </div>
</div>

<style>
.customize-page-wrapper {
    min-height: 100vh;
    background: #f9fafb;
}

.customize-header {
    background: white;
    border-bottom: 1px solid #e5e7eb;
    padding: 1.5rem 0;
    position: sticky;
    top: 72px; /* Hauteur du header de la plateforme */
    z-index: 99; /* En dessous du header de la plateforme (z-index: 100) */
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.header-content {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.customize-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.header-actions {
    display: flex;
    gap: 1rem;
}

.btn-preview, .btn-mobile-preview {
    padding: 0.625rem 1.25rem;
    border-radius: 8px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
}

.btn-preview {
    background: #0ea5e9;
    color: white;
}

.btn-preview:hover {
    background: #0284c7;
}

.btn-mobile-preview {
    background: #f3f4f6;
    color: #374151;
}

.btn-mobile-preview:hover {
    background: #e5e7eb;
}

.customize-layout {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 2rem;
}

.customize-sidebar {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    height: fit-content;
    position: sticky;
    top: 172px; /* Header plateforme (72px) + Header personnalisation (100px) */
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.nav-group {
    margin-bottom: 0.5rem;
}

.nav-group-header {
    padding: 0.875rem 1rem;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 600;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
}

.nav-group-header:hover {
    background: #f3f4f6;
}

.nav-group-header.active {
    background: #eff6ff;
    color: #0ea5e9;
    border-color: #bfdbfe;
}

.nav-group-header i:first-child {
    width: 20px;
    text-align: center;
}

.nav-arrow {
    transition: transform 0.3s;
    font-size: 0.75rem;
    color: #6b7280;
}

.nav-group-header.active .nav-arrow {
    transform: rotate(180deg);
    color: #0ea5e9;
}

.nav-group-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    margin-top: 0.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.nav-group-content.expanded {
    max-height: 500px;
}

.nav-item {
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: #6b7280;
    text-decoration: none;
    transition: all 0.2s;
    font-weight: 500;
    font-size: 0.9rem;
}

.nav-item:hover {
    background: #f3f4f6;
    color: #1f2937;
}

.nav-item.active {
    background: #eff6ff;
    color: #0ea5e9;
    font-weight: 600;
}

.customize-main {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.customize-section {
    display: none;
}

.customize-section.active {
    display: block;
}

.section-header {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.section-description {
    color: #6b7280;
    margin: 0;
    font-size: 0.95rem;
}

.section-content {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-label {
    font-weight: 600;
    color: #374151;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.required {
    color: #ef4444;
}

.form-hint {
    color: #6b7280;
    font-size: 0.875rem;
    margin: -0.25rem 0 0.5rem 0;
}

.form-input, .form-textarea {
    padding: 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.2s;
    width: 100%;
}

.form-input:focus, .form-textarea:focus {
    outline: none;
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
}

.form-input.error, .form-textarea.error {
    border-color: #ef4444;
}

.form-error {
    color: #ef4444;
    font-size: 0.875rem;
}

.logo-upload-wrapper {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.logo-upload-section {
    display: flex;
    gap: 0.75rem;
}

.logo-upload-section .form-input {
    flex: 1;
}

.btn-upload {
    padding: 0.75rem 1.5rem;
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-upload:hover {
    background: #e5e7eb;
}

.logo-preview {
    margin-top: 0.5rem;
}

.preview-wrapper {
    position: relative;
    display: inline-block;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.preview-image {
    max-width: 200px;
    max-height: 100px;
    object-fit: contain;
}

.btn-remove {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #ef4444;
    color: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-remove:hover {
    background: #dc2626;
}

.social-inputs {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.social-input-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.social-label {
    font-weight: 500;
    color: #374151;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.social-label i {
    width: 20px;
    text-align: center;
}

.form-actions {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
}

.btn-primary {
    padding: 0.875rem 2rem;
    background: #0ea5e9;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
}

.btn-primary:hover {
    background: #0284c7;
}

.icon-inline {
    display: inline-block;
}
</style>

<script>
// Toggle navigation groups
function toggleNavGroup(header) {
    const group = header.closest('.nav-group');
    const content = group.querySelector('.nav-group-content');
    const isExpanded = content.classList.contains('expanded');
    
    // Fermer tous les autres groupes
    document.querySelectorAll('.nav-group-header').forEach(h => {
        if (h !== header) {
            h.classList.remove('active');
            h.closest('.nav-group').querySelector('.nav-group-content').classList.remove('expanded');
        }
    });
    
    // Toggle le groupe actuel
    if (isExpanded) {
        header.classList.remove('active');
        content.classList.remove('expanded');
    } else {
        header.classList.add('active');
        content.classList.add('expanded');
    }
}

// Navigation entre les sections
document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        const sectionId = this.dataset.section;
        
        // Mettre à jour la navigation
        document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
        this.classList.add('active');
        
        // Afficher la section correspondante
        document.querySelectorAll('.customize-section').forEach(section => {
            section.classList.remove('active');
        });
        const targetSection = document.getElementById(sectionId);
        if (targetSection) {
            targetSection.classList.add('active');
        }
    });
});

// Upload logo
function handleLogoUpload(input) {
    if (input.files && input.files[0]) {
        const formData = new FormData();
        formData.append('image', input.files[0]);
        formData.append('_token', document.querySelector('meta[name=csrf-token]').content);

        fetch('{{ route("merchant.products.upload-image") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.url) {
                document.getElementById('logo').value = data.url;
                const preview = document.getElementById('logo_preview');
                preview.innerHTML = `
                    <div class="preview-wrapper">
                        <img src="${data.url}" alt="Logo" class="preview-image">
                        <button type="button" class="btn-remove" onclick="removeLogo()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de l\'upload de l\'image');
        });
    }
}

function removeLogo() {
    document.getElementById('logo').value = '';
    document.getElementById('logo_preview').innerHTML = '';
}

// Prévisualisation en temps réel
function openPreviewModal() {
    const modal = document.getElementById('previewModal');
    if (modal) {
        modal.style.display = 'flex';
        updatePreviewIframe();
    }
}

function closePreviewModal() {
    const modal = document.getElementById('previewModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function updatePreviewIframe() {
    const iframe = document.getElementById('previewIframe');
    if (iframe) {
        const storeUrl = iframe.src.split('?')[0];
        iframe.src = storeUrl + '?preview=' + Date.now();
    }
}

function toggleMobilePreview() {
    const iframe = document.getElementById('previewIframe');
    if (iframe) {
        const isMobile = iframe.classList.toggle('mobile-view');
        if (isMobile) {
            iframe.style.width = '375px';
            iframe.style.height = '667px';
        } else {
            iframe.style.width = '100%';
            iframe.style.height = '100%';
        }
    }
}

// Mettre à jour la prévisualisation automatiquement
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.color-picker, input[name="template_id"]').forEach(element => {
        element.addEventListener('change', function() {
            if (this.classList.contains('color-picker')) {
                updateColorPreview();
            }
            if (this.name === 'template_id') {
                updatePreview();
            }
            const modal = document.getElementById('previewModal');
            if (modal && modal.style.display === 'flex') {
                setTimeout(updatePreviewIframe, 500);
            }
        });
    });
});

// Fonctions pour Apparence et Thème
function updatePreview() {
    // Mise à jour de la prévisualisation du thème
    const selectedTemplate = document.querySelector('input[name="template_id"]:checked');
    if (selectedTemplate) {
        // Animation de sélection
        document.querySelectorAll('.template-card').forEach(card => {
            card.classList.remove('selected');
        });
        selectedTemplate.closest('.template-card').classList.add('selected');
    }
}

function applyPresetColors(primary, secondary, accent) {
    document.getElementById('primary_color').value = primary;
    document.getElementById('primary_color_text').value = primary;
    document.getElementById('secondary_color').value = secondary;
    document.getElementById('secondary_color_text').value = secondary;
    document.getElementById('accent_color').value = accent;
    document.getElementById('accent_color_text').value = accent;
    updateColorPreview();
}

function updateColorPreview() {
    const primary = document.getElementById('primary_color').value;
    const secondary = document.getElementById('secondary_color').value;
    const accent = document.getElementById('accent_color').value;
    
    // Mettre à jour les inputs texte
    document.getElementById('primary_color_text').value = primary;
    document.getElementById('secondary_color_text').value = secondary;
    document.getElementById('accent_color_text').value = accent;
    
    // Mettre à jour l'aperçu
    const previewHeader = document.getElementById('colorPreviewHeader');
    if (previewHeader) {
        previewHeader.style.background = `linear-gradient(135deg, ${primary}, ${secondary})`;
    }
}

function updateColorFromText(colorId, value) {
    // Valider le format hex
    if (/^#[0-9A-F]{6}$/i.test(value)) {
        document.getElementById(colorId).value = value;
        updateColorPreview();
    } else {
        alert('Format de couleur invalide. Utilisez le format #RRGGBB (ex: #0ea5e9)');
    }
}

function editColor(colorId) {
    document.getElementById(colorId).click();
}

function updateFontPreview() {
    const headingFont = document.getElementById('heading_font').value;
    const bodyFont = document.getElementById('body_font').value;
    
    // Charger les polices Google Fonts si nécessaire
    loadGoogleFont(headingFont);
    loadGoogleFont(bodyFont);
    
    // Mettre à jour les aperçus
    const headingPreview = document.getElementById('headingFontPreview');
    const bodyPreview = document.getElementById('bodyFontPreview');
    
    if (headingPreview) {
        headingPreview.style.fontFamily = `'${headingFont}', sans-serif`;
    }
    if (bodyPreview) {
        bodyPreview.style.fontFamily = `'${bodyFont}', sans-serif`;
    }
}

function loadGoogleFont(fontName) {
    // Vérifier si la police est déjà chargée
    if (document.querySelector(`link[href*="${fontName}"]`)) {
        return;
    }
    
    // Créer un lien pour charger la police Google Fonts
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = `https://fonts.googleapis.com/css2?family=${fontName.replace(' ', '+')}:wght@400;500;600;700&display=swap`;
    document.head.appendChild(link);
}

// Initialiser les aperçus au chargement
document.addEventListener('DOMContentLoaded', function() {
    updateColorPreview();
    updateFontPreview();
});
</script>

<!-- Styles pour Apparence et Thème -->
<style>
/* Templates Grid */
.templates-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-top: 1rem;
}

@media (max-width: 1200px) {
    .templates-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .templates-grid {
        grid-template-columns: 1fr;
    }
}

.template-card {
    position: relative;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 1rem;
    cursor: pointer;
    transition: all 0.3s;
    background: white;
}

.template-card:hover {
    border-color: #0ea5e9;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.template-card.selected {
    border-color: #0ea5e9;
    background: #eff6ff;
    box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2);
}

.template-card input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.template-preview {
    margin-bottom: 0.75rem;
    border-radius: 8px;
    overflow: hidden;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    aspect-ratio: 1;
    display: flex;
    flex-direction: column;
}

.template-preview-header {
    height: 60px;
    padding: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.template-preview-dots {
    display: flex;
    gap: 0.25rem;
}

.template-preview-dots span {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
}

.template-preview-body {
    padding: 0.75rem;
    background: white;
}

.template-preview-bar {
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    margin-bottom: 0.5rem;
}

.template-preview-bar.short {
    width: 60%;
}

.template-info {
    text-align: center;
}

.template-name {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.template-desc {
    font-size: 0.875rem;
    color: #6b7280;
}

.template-check {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    width: 24px;
    height: 24px;
    background: #0ea5e9;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.75rem;
}

.template-card.selected .template-check {
    display: flex;
}

.checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 0.75rem;
}

.checkbox-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 0.9rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #f9fafb;
}

.faq-item-inputs {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding: 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #f9fafb;
    margin-bottom: 0.75rem;
}

/* Preset Colors */
.preset-colors {
    margin: 1.5rem 0;
}

.preset-colors-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 1rem;
    font-size: 0.95rem;
}

.preset-colors-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
}

.preset-color-item {
    cursor: pointer;
    padding: 0.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.2s;
    background: white;
}

.preset-color-item:hover {
    border-color: #0ea5e9;
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.preset-colors-preview {
    display: flex;
    gap: 0.25rem;
    margin-bottom: 0.5rem;
}

.preset-color-box {
    flex: 1;
    height: 40px;
    border-radius: 6px;
    border: 1px solid #e5e7eb;
}

.preset-color-name {
    font-size: 0.875rem;
    color: #6b7280;
    text-align: center;
    font-weight: 500;
}

/* Custom Colors */
.custom-colors {
    margin-top: 2rem;
}

.custom-colors-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 1rem;
    font-size: 0.95rem;
}

.color-picker-group {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.color-picker-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.color-label {
    font-weight: 500;
    color: #374151;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.95rem;
}

.btn-edit-color {
    background: #f3f4f6;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 0.375rem 0.625rem;
    cursor: pointer;
    color: #6b7280;
    font-size: 0.75rem;
    transition: all 0.2s;
}

.btn-edit-color:hover {
    background: #e5e7eb;
    color: #374151;
}

.color-input-wrapper {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.color-picker {
    width: 60px;
    height: 40px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    cursor: pointer;
}

.color-text-input {
    flex: 1;
    padding: 0.625rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-family: monospace;
    font-size: 0.9rem;
}

.color-preview-card {
    margin-top: 1.5rem;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

.preview-card-header {
    padding: 2rem;
    color: white;
    text-align: center;
    background: linear-gradient(135deg, #0ea5e9, #a855f7);
}

.preview-card-header h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
    font-weight: 700;
}

.preview-card-header p {
    margin: 0;
    opacity: 0.9;
}

/* Typography */
.typography-group {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    margin-top: 1rem;
}

.typography-item {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.typography-label {
    font-weight: 500;
    color: #374151;
    font-size: 0.95rem;
}

.form-select {
    padding: 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.95rem;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
}

.form-select:focus {
    outline: none;
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
}

.font-preview {
    padding: 1rem;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    margin-top: 0.5rem;
}

#headingFontPreview {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
}

#bodyFontPreview {
    font-size: 1rem;
    line-height: 1.6;
    color: #374151;
}

/* Modal de prévisualisation */
.preview-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    align-items: center;
    justify-content: center;
}

.preview-modal-content {
    background: white;
    border-radius: 12px;
    width: 95%;
    height: 90%;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.preview-modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.preview-modal-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
}

.preview-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.btn-mobile-toggle, .btn-close-modal {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    background: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    transition: all 0.2s;
}

.btn-mobile-toggle:hover, .btn-close-modal:hover {
    background: #f3f4f6;
    color: #1f2937;
}

.preview-iframe-wrapper {
    flex: 1;
    position: relative;
    overflow: hidden;
}

#previewIframe {
    width: 100%;
    height: 100%;
    border: none;
    transition: all 0.3s;
}

#previewIframe.mobile-view {
    border: 8px solid #1f2937;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}

.btn-secondary {
    padding: 0.625rem 1.25rem;
    border-radius: 8px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
    border: 1px solid #d1d5db;
    background: white;
    color: #374151;
    text-decoration: none;
    cursor: pointer;
}

.btn-secondary:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

/* Modal de prévisualisation */
.preview-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    align-items: center;
    justify-content: center;
}

.preview-modal-content {
    background: white;
    border-radius: 12px;
    width: 95%;
    height: 90%;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.preview-modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.preview-modal-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
}

.preview-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.btn-mobile-toggle, .btn-close-modal {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    background: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    transition: all 0.2s;
}

.btn-mobile-toggle:hover, .btn-close-modal:hover {
    background: #f3f4f6;
    color: #1f2937;
}

.preview-iframe-wrapper {
    flex: 1;
    position: relative;
    overflow: hidden;
}

#previewIframe {
    width: 100%;
    height: 100%;
    border: none;
    transition: all 0.3s;
}

#previewIframe.mobile-view {
    width: 375px;
    height: 667px;
    border: 8px solid #1f2937;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}
</style>

<!-- Modal de prévisualisation -->
<div id="previewModal" class="preview-modal" onclick="if(event.target.id === 'previewModal') closePreviewModal()">
    <div class="preview-modal-content" onclick="event.stopPropagation()">
        <div class="preview-modal-header">
            <h3>Prévisualisation de votre boutique</h3>
            <div class="preview-actions">
                <button type="button" class="btn-mobile-toggle" onclick="toggleMobilePreview()" title="Vue mobile">
                    <i class="fas fa-mobile-alt"></i>
                </button>
                <button type="button" class="btn-close-modal" onclick="closePreviewModal()" title="Fermer">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="preview-iframe-wrapper">
            <iframe id="previewIframe" src="{{ $store->publicUrl }}" frameborder="0"></iframe>
        </div>
    </div>
</div>
@endsection
