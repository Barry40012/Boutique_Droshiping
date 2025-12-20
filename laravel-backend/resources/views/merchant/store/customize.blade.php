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
                    <i class="fas fa-eye"></i>
                    <span>Prévisualiser</span>
                </button>
                <a href="{{ $store->publicUrl }}" target="_blank" class="btn-open-new-tab">
                    <i class="fas fa-external-link-alt"></i>
                    <span>Ouvrir dans un nouvel onglet</span>
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
                        <div class="form-group-collapsible">
                            <button type="button" class="form-group-header-btn" onclick="toggleFormGroup(this)">
                                <div class="form-group-header-content">
                                    <i class="fas fa-palette icon-inline"></i>
                                    <span class="form-group-title">Thème de la boutique</span>
                                </div>
                                <i class="fas fa-chevron-down form-group-arrow"></i>
                            </button>
                            <div class="form-group-content">
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
                        </div>

                        <!-- Couleurs -->
                        <div class="form-group-collapsible">
                            <button type="button" class="form-group-header-btn" onclick="toggleFormGroup(this)">
                                <div class="form-group-header-content">
                                    <i class="fas fa-fill-drip icon-inline"></i>
                                    <span class="form-group-title">Couleurs de la boutique</span>
                                </div>
                                <i class="fas fa-chevron-down form-group-arrow"></i>
                            </button>
                            <div class="form-group-content">
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
                                <div class="preview-card-body" id="colorPreviewBody">
                                    <div class="preview-header-mini" id="previewHeaderMini">
                                        <div class="preview-header-brand" id="previewHeaderBrand">Nom boutique</div>
                                        <div class="preview-header-nav" id="previewHeaderNav">
                                            <span>Menu 1</span>
                                            <span>Menu 2</span>
                                        </div>
                                    </div>
                                    <div class="preview-banner-mini" id="previewBannerMini">
                                        <div class="preview-banner-title" id="previewBannerTitle">Titre bannière</div>
                                        <div class="preview-banner-button" id="previewBannerButton">Bouton</div>
                                    </div>
                                    <div class="preview-footer-mini" id="previewFooterMini">
                                        <span>Footer</span>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>

                        <!-- Typographie -->
                        <div class="form-group-collapsible">
                            <button type="button" class="form-group-header-btn" onclick="toggleFormGroup(this)">
                                <div class="form-group-header-content">
                                    <i class="fas fa-font icon-inline"></i>
                                    <span class="form-group-title">Typographie</span>
                                </div>
                                <i class="fas fa-chevron-down form-group-arrow"></i>
                            </button>
                            <div class="form-group-content">
                                <p class="form-hint">Choisissez les polices pour les titres et le contenu de votre boutique</p>
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

                        <!-- Personnalisation Bannière -->
                        <div class="form-group-collapsible" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                            <button type="button" class="form-group-header-btn" onclick="toggleFormGroup(this)">
                                <div class="form-group-header-content">
                                    <i class="fas fa-image icon-inline"></i>
                                    <span class="form-group-title">Personnalisation de la Bannière</span>
                                </div>
                                <i class="fas fa-chevron-down form-group-arrow"></i>
                            </button>
                            <div class="form-group-content">
                                <p class="form-hint">Personnalisez l'apparence du nom de la boutique et du bouton sur la bannière</p>
                            
                            <div class="form-row">
                                <div class="form-col">
                                    <label for="banner_title_color" class="form-label-small">Couleur du nom de la boutique</label>
                                    <div class="color-input-wrapper">
                                        <input type="color" 
                                               id="banner_title_color" 
                                               name="banner_title_color" 
                                               class="color-picker" 
                                               value="{{ old('banner_title_color', $store->banner_title_color ?? '#ffffff') }}"
                                               onchange="updatePreview()">
                                        <input type="text" 
                                               id="banner_title_color_text" 
                                               class="color-text-input" 
                                               value="{{ old('banner_title_color', $store->banner_title_color ?? '#ffffff') }}"
                                               onchange="updateColorFromText('banner_title_color', this.value)">
                                    </div>
                                </div>
                                
                                <div class="form-col">
                                    <label for="banner_title_animation" class="form-label-small">Animation du nom</label>
                                    <select id="banner_title_animation" name="banner_title_animation" class="form-select" onchange="updatePreview()">
                                        <option value="none" {{ old('banner_title_animation', $store->banner_title_animation ?? 'none') == 'none' ? 'selected' : '' }}>Aucune</option>
                                        <option value="fade" {{ old('banner_title_animation', $store->banner_title_animation ?? 'none') == 'fade' ? 'selected' : '' }}>Fondu</option>
                                        <option value="bounce" {{ old('banner_title_animation', $store->banner_title_animation ?? 'none') == 'bounce' ? 'selected' : '' }}>Rebond</option>
                                        <option value="glow" {{ old('banner_title_animation', $store->banner_title_animation ?? 'none') == 'glow' ? 'selected' : '' }}>Lueur</option>
                                        <option value="slide" {{ old('banner_title_animation', $store->banner_title_animation ?? 'none') == 'slide' ? 'selected' : '' }}>Glissement</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row" style="margin-top: 1.5rem;">
                                <div class="form-col">
                                    <label for="banner_text_color" class="form-label-small">Couleur des textes de bannière</label>
                                    <div class="color-input-wrapper">
                                        <input type="color"
                                               id="banner_text_color"
                                               name="banner_text_color"
                                               class="color-picker"
                                               value="{{ old('banner_text_color', $store->banner_text_color ?? ($store->banner_title_color ?? '#ffffff')) }}"
                                               onchange="updatePreview()">
                                        <input type="text"
                                               id="banner_text_color_text"
                                               class="color-text-input"
                                               value="{{ old('banner_text_color', $store->banner_text_color ?? ($store->banner_title_color ?? '#ffffff')) }}"
                                               onchange="updateColorFromText('banner_text_color', this.value)">
                                    </div>
                                </div>
                                
                                <div class="form-col">
                                    <label for="banner_text_font" class="form-label-small">Police des textes de bannière</label>
                                    <select id="banner_text_font" name="banner_text_font" class="form-select" onchange="updatePreview()">
                                        @php
                                            $bannerTextFont = old('banner_text_font', $store->banner_text_font ?? 'inherit');
                                        @endphp
                                        <option value="inherit" {{ $bannerTextFont == 'inherit' ? 'selected' : '' }}>Par défaut</option>
                                        <option value="Arial, sans-serif" {{ $bannerTextFont == 'Arial, sans-serif' ? 'selected' : '' }}>Arial</option>
                                        <option value="'Helvetica Neue', Helvetica, sans-serif" {{ $bannerTextFont == "'Helvetica Neue', Helvetica, sans-serif" ? 'selected' : '' }}>Helvetica</option>
                                        <option value="Georgia, serif" {{ $bannerTextFont == 'Georgia, serif' ? 'selected' : '' }}>Georgia</option>
                                        <option value="'Times New Roman', Times, serif" {{ $bannerTextFont == "'Times New Roman', Times, serif" ? 'selected' : '' }}>Times New Roman</option>
                                        <option value="'Courier New', Courier, monospace" {{ $bannerTextFont == "'Courier New', Courier, monospace" ? 'selected' : '' }}>Courier New</option>
                                        <option value="Verdana, sans-serif" {{ $bannerTextFont == 'Verdana, sans-serif' ? 'selected' : '' }}>Verdana</option>
                                        <option value="'Trebuchet MS', sans-serif" {{ $bannerTextFont == "'Trebuchet MS', sans-serif" ? 'selected' : '' }}>Trebuchet MS</option>
                                        <option value="'Comic Sans MS', cursive" {{ $bannerTextFont == "'Comic Sans MS', cursive" ? 'selected' : '' }}>Comic Sans MS</option>
                                        <option value="Impact, sans-serif" {{ $bannerTextFont == 'Impact, sans-serif' ? 'selected' : '' }}>Impact</option>
                                        <option value="'Lucida Console', monospace" {{ $bannerTextFont == "'Lucida Console', monospace" ? 'selected' : '' }}>Lucida Console</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row" style="margin-top: 1.5rem;">
                                <div class="form-col">
                                    <label for="banner_text_size" class="form-label-small">Taille des textes de bannière</label>
                                    <select id="banner_text_size" name="banner_text_size" class="form-select" onchange="updatePreview()">
                                        @php
                                            $bannerTextSize = old('banner_text_size', $store->banner_text_size ?? '1.25rem');
                                        @endphp
                                        <option value="0.875rem" {{ $bannerTextSize == '0.875rem' ? 'selected' : '' }}>Petit (14px)</option>
                                        <option value="1rem" {{ $bannerTextSize == '1rem' ? 'selected' : '' }}>Normal (16px)</option>
                                        <option value="1.25rem" {{ $bannerTextSize == '1.25rem' ? 'selected' : '' }}>Moyen (20px)</option>
                                        <option value="1.5rem" {{ $bannerTextSize == '1.5rem' ? 'selected' : '' }}>Grand (24px)</option>
                                        <option value="1.75rem" {{ $bannerTextSize == '1.75rem' ? 'selected' : '' }}>Très grand (28px)</option>
                                        <option value="2rem" {{ $bannerTextSize == '2rem' ? 'selected' : '' }}>Extra large (32px)</option>
                                        <option value="2.5rem" {{ $bannerTextSize == '2.5rem' ? 'selected' : '' }}>Énorme (40px)</option>
                                    </select>
                                </div>
                                
                                <div class="form-col">
                                    <label for="banner_title_size" class="form-label-small">Taille du nom de la boutique</label>
                                    <select id="banner_title_size" name="banner_title_size" class="form-select" onchange="updatePreview()">
                                        @php
                                            $bannerTitleSize = old('banner_title_size', $store->banner_title_size ?? '3rem');
                                        @endphp
                                        <option value="2rem" {{ $bannerTitleSize == '2rem' ? 'selected' : '' }}>Petit (32px)</option>
                                        <option value="2.5rem" {{ $bannerTitleSize == '2.5rem' ? 'selected' : '' }}>Moyen (40px)</option>
                                        <option value="3rem" {{ $bannerTitleSize == '3rem' ? 'selected' : '' }}>Normal (48px)</option>
                                        <option value="3.5rem" {{ $bannerTitleSize == '3.5rem' ? 'selected' : '' }}>Grand (56px)</option>
                                        <option value="4rem" {{ $bannerTitleSize == '4rem' ? 'selected' : '' }}>Très grand (64px)</option>
                                        <option value="4.5rem" {{ $bannerTitleSize == '4.5rem' ? 'selected' : '' }}>Extra large (72px)</option>
                                        <option value="5rem" {{ $bannerTitleSize == '5rem' ? 'selected' : '' }}>Énorme (80px)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row" style="margin-top: 1.5rem;">
                                <div class="form-col">
                                    <label for="banner_text_speed" class="form-label-small">Vitesse des animations</label>
                                    <select id="banner_text_speed" name="banner_text_speed" class="form-select" onchange="updatePreview()">
                                        @php
                                            $bannerTextSpeed = old('banner_text_speed', $store->banner_text_speed ?? 5);
                                        @endphp
                                        <option value="1" {{ $bannerTextSpeed == 1 ? 'selected' : '' }}>Très lent</option>
                                        <option value="2" {{ $bannerTextSpeed == 2 ? 'selected' : '' }}>Lent</option>
                                        <option value="3" {{ $bannerTextSpeed == 3 ? 'selected' : '' }}>Assez lent</option>
                                        <option value="4" {{ $bannerTextSpeed == 4 ? 'selected' : '' }}>Lent-normal</option>
                                        <option value="5" {{ $bannerTextSpeed == 5 ? 'selected' : '' }}>Normal</option>
                                        <option value="6" {{ $bannerTextSpeed == 6 ? 'selected' : '' }}>Rapide-normal</option>
                                        <option value="7" {{ $bannerTextSpeed == 7 ? 'selected' : '' }}>Assez rapide</option>
                                        <option value="8" {{ $bannerTextSpeed == 8 ? 'selected' : '' }}>Rapide</option>
                                        <option value="9" {{ $bannerTextSpeed == 9 ? 'selected' : '' }}>Très rapide</option>
                                        <option value="10" {{ $bannerTextSpeed == 10 ? 'selected' : '' }}>Ultra rapide</option>
                                    </select>
                                    <p class="form-hint" style="margin-top: 0.25rem; font-size: 0.75rem; color: #6b7280;">
                                        Contrôle la vitesse de toutes les animations (machine à écrire, transitions, etc.)
                                    </p>
                                </div>
                            </div>
                            
                            <div class="form-row" style="margin-top: 1.5rem;">
                                <div class="form-col">
                                    <label for="banner_button_text" class="form-label-small">Texte du bouton bannière</label>
                                    <input type="text" 
                                           id="banner_button_text" 
                                           name="banner_button_text" 
                                           class="form-input" 
                                           value="{{ old('banner_button_text', $store->banner_button_text ?? 'Voir le produit') }}"
                                           placeholder="Voir le produit"
                                           onchange="updatePreview()">
                                </div>
                            </div>

                            @php
                                $s = $store->settings ?? [];
                                $bannerTexts = $s['banner_texts'] ?? [];
                                // S'assurer d'avoir au moins 3 entrées visibles
                                if (empty($bannerTexts)) {
                                    $bannerTexts = [
                                        1 => ['text' => null, 'effect' => null],
                                        2 => ['text' => null, 'effect' => null],
                                        3 => ['text' => null, 'effect' => null],
                                    ];
                                }
                            @endphp

                            <div class="form-row" style="margin-top: 1.5rem;">
                                <div class="form-col">
                                    <label class="form-label-small">Textes de bannière (messages défilants)</label>
                                    <p class="form-hint">
                                        Ajoutez autant de messages que vous le souhaitez et choisissez un effet pour chacun.
                                    </p>
                                    <div id="bannerTextsContainer">
                                        @foreach($bannerTexts as $index => $bannerText)
                                            @php
                                                $textValue = $bannerText['text'] ?? '';
                                                $effectValue = $bannerText['effect'] ?? ($store->banner_text_animation ?? 'scroll');
                                            @endphp
                                            <div class="banner-text-row" data-index="{{ $index }}" style="display:flex; gap:0.5rem; margin-bottom:0.5rem;">
                                                <input type="text"
                                                       name="settings[banner_texts][{{ $index }}][text]"
                                                       class="form-input"
                                                       placeholder="Texte de bannière {{ $index }}"
                                                       style="flex: 1 1 auto;"
                                                       value="{{ $textValue }}">
                                                <select name="settings[banner_texts][{{ $index }}][effect]"
                                                        class="form-select"
                                                        style="width: 180px;">
                                                    @php
                                                        $effects = [
                                                            'scroll' => 'Défilement vertical',
                                                            'typewriter' => 'Machine à écrire',
                                                            'fade' => 'Fondu',
                                                            'bounce' => 'Rebond',
                                                            'slide' => 'Glissement',
                                                        ];
                                                    @endphp
                                                    @foreach($effects as $val => $label)
                                                        <option value="{{ $val }}" {{ $effectValue === $val ? 'selected' : '' }}>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button"
                                            class="btn-secondary"
                                            style="margin-top:0.5rem;"
                                            onclick="addBannerTextRow()">
                                        <i class="fas fa-plus icon-inline"></i>
                                        Ajouter un texte
                                    </button>
                                </div>
                            </div>
                            
                            <div class="form-row" style="margin-top: 1.5rem;">
                                <div class="form-col">
                                    <label for="banner_button_bg_color" class="form-label-small">Couleur de fond du bouton</label>
                                    <div class="color-input-wrapper">
                                        <input type="color" 
                                               id="banner_button_bg_color" 
                                               name="banner_button_bg_color" 
                                               class="color-picker" 
                                               value="{{ old('banner_button_bg_color', $store->banner_button_bg_color ?? ($store->banner_button_color ?? ($store->primary_color ?? '#3b82f6'))) }}"
                                               onchange="updateColorPreview(); updatePreview();">
                                        <input type="text" 
                                               id="banner_button_bg_color_text" 
                                               class="color-text-input" 
                                               value="{{ old('banner_button_bg_color', $store->banner_button_bg_color ?? ($store->banner_button_color ?? ($store->primary_color ?? '#3b82f6'))) }}"
                                               onchange="updateColorFromText('banner_button_bg_color', this.value)">
                                    </div>
                                </div>
                                
                                <div class="form-col">
                                    <label for="banner_button_text_color" class="form-label-small">Couleur du texte du bouton</label>
                                    <div class="color-input-wrapper">
                                        <input type="color" 
                                               id="banner_button_text_color" 
                                               name="banner_button_text_color" 
                                               class="color-picker" 
                                               value="{{ old('banner_button_text_color', $store->banner_button_text_color ?? '#ffffff') }}"
                                               onchange="updateColorPreview(); updatePreview();">
                                        <input type="text" 
                                               id="banner_button_text_color_text" 
                                               class="color-text-input" 
                                               value="{{ old('banner_button_text_color', $store->banner_button_text_color ?? '#ffffff') }}"
                                               onchange="updateColorFromText('banner_button_text_color', this.value)">
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>

                        <!-- Personnalisation Header -->
                        <div class="form-group-collapsible" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                            <button type="button" class="form-group-header-btn" onclick="toggleFormGroup(this)">
                                <div class="form-group-header-content">
                                    <i class="fas fa-heading icon-inline"></i>
                                    <span class="form-group-title">Personnalisation de l'Entête</span>
                                </div>
                                <i class="fas fa-chevron-down form-group-arrow"></i>
                            </button>
                            <div class="form-group-content">
                                <p class="form-hint">Personnalisez les couleurs et la police de l'entête de votre boutique</p>
                            
                            <div class="form-row">
                                <div class="form-col">
                                    <label for="header_bg_color" class="form-label-small">Couleur de fond</label>
                                    <div class="color-input-wrapper">
                                        <input type="color" 
                                               id="header_bg_color" 
                                               name="header_bg_color" 
                                               class="color-picker" 
                                               value="{{ old('header_bg_color', $store->header_bg_color ?? '#ffffff') }}"
                                               onchange="updatePreview()">
                                        <input type="text" 
                                               id="header_bg_color_text" 
                                               class="color-text-input" 
                                               value="{{ old('header_bg_color', $store->header_bg_color ?? '#ffffff') }}"
                                               onchange="updateColorFromText('header_bg_color', this.value)">
                                    </div>
                                </div>
                                
                                <div class="form-col">
                                    <label for="header_text_color" class="form-label-small">Couleur du texte (menus)</label>
                                    <div class="color-input-wrapper">
                                        <input type="color" 
                                               id="header_text_color" 
                                               name="header_text_color" 
                                               class="color-picker" 
                                               value="{{ old('header_text_color', $store->header_text_color ?? '#4b5563') }}"
                                               onchange="updatePreview()">
                                        <input type="text" 
                                               id="header_text_color_text" 
                                               class="color-text-input" 
                                               value="{{ old('header_text_color', $store->header_text_color ?? '#4b5563') }}"
                                               onchange="updateColorFromText('header_text_color', this.value)">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-row" style="margin-top: 1.5rem;">
                                <div class="form-col">
                                    <label for="header_name_color" class="form-label-small">Couleur du nom de la boutique</label>
                                    <div class="color-input-wrapper">
                                        <input type="color" 
                                               id="header_name_color" 
                                               name="header_name_color" 
                                               class="color-picker" 
                                               value="{{ old('header_name_color', $store->header_name_color ?? '#1f2937') }}"
                                               onchange="updatePreview()">
                                        <input type="text" 
                                               id="header_name_color_text" 
                                               class="color-text-input" 
                                               value="{{ old('header_name_color', $store->header_name_color ?? '#1f2937') }}"
                                               onchange="updateColorFromText('header_name_color', this.value)">
                                    </div>
                                </div>
                                
                                <div class="form-col">
                                    <label for="header_name_font" class="form-label-small">Police du nom de la boutique</label>
                                    <select id="header_name_font" name="header_name_font" class="form-select" onchange="updatePreview()">
                                        @php
                                            $fonts = [
                                                'inherit' => 'Par défaut',
                                                'Inter' => 'Inter',
                                                'Roboto' => 'Roboto',
                                                'Poppins' => 'Poppins',
                                                'Montserrat' => 'Montserrat',
                                                'Open Sans' => 'Open Sans',
                                                'Lato' => 'Lato',
                                                'Raleway' => 'Raleway',
                                                'Playfair Display' => 'Playfair Display',
                                            ];
                                        @endphp
                                        @foreach($fonts as $fontValue => $fontName)
                                            <option value="{{ $fontValue }}" {{ old('header_name_font', $store->header_name_font ?? 'inherit') == $fontValue ? 'selected' : '' }}>
                                                {{ $fontName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-row" style="margin-top: 1.5rem;">
                                <div class="form-col">
                                    <label for="header_nav_hover_color" class="form-label-small">Couleur au survol des menus (hover)</label>
                                    <div class="color-input-wrapper">
                                        <input type="color" 
                                               id="header_nav_hover_color" 
                                               name="header_nav_hover_color" 
                                               class="color-picker" 
                                               value="{{ old('header_nav_hover_color', $store->header_nav_hover_color ?? ($store->primary_color ?? '#0ea5e9')) }}"
                                               onchange="updatePreview()">
                                        <input type="text" 
                                               id="header_nav_hover_color_text" 
                                               class="color-text-input" 
                                               value="{{ old('header_nav_hover_color', $store->header_nav_hover_color ?? ($store->primary_color ?? '#0ea5e9')) }}"
                                               onchange="updateColorFromText('header_nav_hover_color', this.value)">
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>

                        <!-- Couleur de fond de la boutique -->
                        <div class="form-group-collapsible" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                            <button type="button" class="form-group-header-btn" onclick="toggleFormGroup(this)">
                                <div class="form-group-header-content">
                                    <i class="fas fa-fill icon-inline"></i>
                                    <span class="form-group-title">Couleur de fond de la boutique</span>
                                </div>
                                <i class="fas fa-chevron-down form-group-arrow"></i>
                            </button>
                            <div class="form-group-content">
                                <p class="form-hint">
                                    Définissez la couleur d'arrière-plan principale de vos pages de boutique (en dehors de l'entête et du pied de page).
                                </p>
                            <div class="form-row">
                                <div class="form-col">
                                    <label for="page_bg_color" class="form-label-small">Fond de page</label>
                                    <div class="color-input-wrapper">
                                        <input type="color" 
                                               id="page_bg_color" 
                                               name="page_bg_color" 
                                               class="color-picker" 
                                               value="{{ old('page_bg_color', $store->page_bg_color ?? '#ffffff') }}"
                                               onchange="updateColorPreview()">
                                        <input type="text" 
                                               id="page_bg_color_text" 
                                               class="color-text-input" 
                                               value="{{ old('page_bg_color', $store->page_bg_color ?? '#ffffff') }}"
                                               onchange="updateColorFromText('page_bg_color', this.value)">
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>

                        <!-- Section Produit : carte images + détails -->
                        <div class="form-group-collapsible" style="margin-top: 2rem;">
                            <button type="button" class="form-group-header-btn" onclick="toggleFormGroup(this)">
                                <div class="form-group-header-content">
                                    <i class="fas fa-box-open icon-inline"></i>
                                    <span class="form-group-title">Section produit (carte images + détails)</span>
                                </div>
                                <i class="fas fa-chevron-down form-group-arrow"></i>
                            </button>
                            <div class="form-group-content">
                                <p class="form-hint">
                                    Personnalisez la couleur de fond et la forme (coins arrondis ou non) de la grande carte qui contient les images du produit et la description.
                                </p>
                            <div class="form-row">
                                <div class="form-col">
                                    <label for="product_section_bg_color" class="form-label-small">Fond de la carte produit</label>
                                    <div class="color-input-wrapper">
                                        <input type="color"
                                               id="product_section_bg_color"
                                               name="product_section_bg_color"
                                               class="color-picker"
                                               value="{{ old('product_section_bg_color', $store->product_section_bg_color ?? '#ffffff') }}"
                                               onchange="updateColorPreview()">
                                        <input type="text"
                                               id="product_section_bg_color_text"
                                               class="color-text-input"
                                               value="{{ old('product_section_bg_color', $store->product_section_bg_color ?? '#ffffff') }}"
                                               onchange="updateColorFromText('product_section_bg_color', this.value)">
                                    </div>
                                </div>
                                <div class="form-col">
                                    <label class="form-label-small">Coins arrondis de la carte produit</label>
                                    <div class="toggle-wrapper">
                                        <input type="hidden" name="product_section_rounded" value="0">
                                        <label class="switch">
                                            <input type="checkbox"
                                                   name="product_section_rounded"
                                                   value="1"
                                                   {{ old('product_section_rounded', $store->product_section_rounded ?? true) ? 'checked' : '' }}>
                                            <span class="slider round"></span>
                                        </label>
                                        <span class="toggle-text">Activer les coins arrondis</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section \"Donnez votre avis\" -->
                        <div class="form-group" style="margin-top: 2rem;">
                            <label class="form-label">
                                <i class="fas fa-comments icon-inline"></i>
                                Section \"Donnez votre avis\"
                            </label>
                            <p class="section-description">
                                Personnalisez le fond et les coins arrondis du bloc de formulaire où vos clients laissent leur avis.
                            </p>
                            <div class="form-row">
                                <div class="form-col">
                                    <label for="reviews_section_bg_color" class="form-label-small">Fond du bloc d'avis</label>
                                    <div class="color-input-wrapper">
                                        <input type="color"
                                               id="reviews_section_bg_color"
                                               name="reviews_section_bg_color"
                                               class="color-picker"
                                               value="{{ old('reviews_section_bg_color', $store->reviews_section_bg_color ?? '#ffffff') }}"
                                               onchange="updateColorPreview()">
                                        <input type="text"
                                               id="reviews_section_bg_color_text"
                                               class="color-text-input"
                                               value="{{ old('reviews_section_bg_color', $store->reviews_section_bg_color ?? '#ffffff') }}"
                                               onchange="updateColorFromText('reviews_section_bg_color', this.value)">
                                    </div>
                                </div>
                                <div class="form-col">
                                    <label class="form-label-small">Coins arrondis du bloc d'avis</label>
                                    <div class="toggle-wrapper">
                                        <input type="hidden" name="reviews_section_rounded" value="0">
                                        <label class="switch">
                                            <input type="checkbox"
                                                   name="reviews_section_rounded"
                                                   value="1"
                                                   {{ old('reviews_section_rounded', $store->reviews_section_rounded ?? true) ? 'checked' : '' }}>
                                            <span class="slider round"></span>
                                        </label>
                                        <span class="toggle-text">Activer les coins arrondis</span>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>

                        <!-- Personnalisation Footer -->
                        <div class="form-group-collapsible" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                            <button type="button" class="form-group-header-btn" onclick="toggleFormGroup(this)">
                                <div class="form-group-header-content">
                                    <i class="fas fa-window-minimize icon-inline"></i>
                                    <span class="form-group-title">Personnalisation du Pied de Page</span>
                                </div>
                                <i class="fas fa-chevron-down form-group-arrow"></i>
                            </button>
                            <div class="form-group-content">
                                <p class="form-hint">Personnalisez les couleurs du pied de page de votre boutique</p>
                            
                            <div class="form-row">
                                <div class="form-col">
                                    <label for="footer_bg_color" class="form-label-small">Couleur de fond</label>
                                    <div class="color-input-wrapper">
                                        <input type="color" 
                                               id="footer_bg_color" 
                                               name="footer_bg_color" 
                                               class="color-picker" 
                                               value="{{ old('footer_bg_color', $store->footer_bg_color ?? '#1f2937') }}"
                                               onchange="updatePreview()">
                                        <input type="text" 
                                               id="footer_bg_color_text" 
                                               class="color-text-input" 
                                               value="{{ old('footer_bg_color', $store->footer_bg_color ?? '#1f2937') }}"
                                               onchange="updateColorFromText('footer_bg_color', this.value)">
                                    </div>
                                </div>
                                
                                <div class="form-col">
                                    <label for="footer_text_color" class="form-label-small">Couleur du texte</label>
                                    <div class="color-input-wrapper">
                                        <input type="color" 
                                               id="footer_text_color" 
                                               name="footer_text_color" 
                                               class="color-picker" 
                                               value="{{ old('footer_text_color', $store->footer_text_color ?? '#9ca3af') }}"
                                               onchange="updatePreview()">
                                        <input type="text" 
                                               id="footer_text_color_text" 
                                               class="color-text-input" 
                                               value="{{ old('footer_text_color', $store->footer_text_color ?? '#9ca3af') }}"
                                               onchange="updateColorFromText('footer_text_color', this.value)">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-row" style="margin-top: 1.5rem;">
                                <div class="form-col">
                                    <label for="footer_link_color" class="form-label-small">Couleur des liens</label>
                                    <div class="color-input-wrapper">
                                        <input type="color" 
                                               id="footer_link_color" 
                                               name="footer_link_color" 
                                               class="color-picker" 
                                               value="{{ old('footer_link_color', $store->footer_link_color ?? '#ffffff') }}"
                                               onchange="updatePreview()">
                                        <input type="text" 
                                               id="footer_link_color_text" 
                                               class="color-text-input" 
                                               value="{{ old('footer_link_color', $store->footer_link_color ?? '#ffffff') }}"
                                               onchange="updateColorFromText('footer_link_color', this.value)">
                                    </div>
                                </div>
                                
                                <div class="form-col">
                                    <label for="footer_title_color" class="form-label-small">Couleur des titres</label>
                                    <div class="color-input-wrapper">
                                        <input type="color" 
                                               id="footer_title_color" 
                                               name="footer_title_color" 
                                               class="color-picker" 
                                               value="{{ old('footer_title_color', $store->footer_title_color ?? '#ffffff') }}"
                                               onchange="updatePreview()">
                                        <input type="text" 
                                               id="footer_title_color_text" 
                                               class="color-text-input" 
                                               value="{{ old('footer_title_color', $store->footer_title_color ?? '#ffffff') }}"
                                               onchange="updateColorFromText('footer_title_color', this.value)">
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
                                Couleurs du bouton d'action
                            </label>
                            <div class="form-row">
                                <div class="form-col">
                                    <label for="button_bg_color" class="form-label-small">Couleur de fond</label>
                                    <div class="color-input-wrapper">
                                        <input type="color" 
                                               id="button_bg_color" 
                                               name="button_bg_color" 
                                               class="color-picker" 
                                               value="{{ old('button_bg_color', $store->button_bg_color ?? ($store->primary_color ?? '#111827')) }}"
                                               onchange="updatePreview()">
                                        <input type="text" 
                                               id="button_bg_color_text" 
                                               class="color-text-input" 
                                               value="{{ old('button_bg_color', $store->button_bg_color ?? ($store->primary_color ?? '#111827')) }}"
                                               onchange="updateColorFromText('button_bg_color', this.value)">
                                    </div>
                                </div>
                                
                                <div class="form-col">
                                    <label for="button_text_color" class="form-label-small">Couleur du texte</label>
                                    <div class="color-input-wrapper">
                                        <input type="color" 
                                               id="button_text_color" 
                                               name="button_text_color" 
                                               class="color-picker" 
                                               value="{{ old('button_text_color', $store->button_text_color ?? '#ffffff') }}"
                                               onchange="updatePreview()">
                                        <input type="text" 
                                               id="button_text_color_text" 
                                               class="color-text-input" 
                                               value="{{ old('button_text_color', $store->button_text_color ?? '#ffffff') }}"
                                               onchange="updateColorFromText('button_text_color', this.value)">
                                    </div>
                                </div>
                            </div>
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
                                <label class="checkbox-item">
                                    <input type="checkbox" name="settings[nav_track_order]" value="1" {{ ($s['nav_track_order'] ?? true) ? 'checked' : '' }}>
                                    Suivre ma commande
                                </label>
                            </div>
                        </div>

                        <!-- Mode multi-produits -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-boxes icon-inline"></i>
                                Mode de boutique
                            </label>
                            <div class="info-box" style="margin-bottom: 1rem; padding: 1rem; background: #f3f4f6; border-radius: 8px; border-left: 4px solid #3b82f6;">
                                <p style="margin: 0; font-size: 0.9rem; color: #4b5563;">
                                    <strong>Mode 1 produit (par défaut) :</strong> Affichage détaillé d'un seul produit sur la page d'accueil.<br>
                                    <strong>Mode multi-produits :</strong> Affichage en grille de plusieurs produits, avec page détail pour chaque produit.
                                </p>
                            </div>
                            <label class="checkbox-item" style="width: fit-content; margin-top: 1rem;">
                                <input type="checkbox" 
                                       name="allow_multiple_products" 
                                       value="1" 
                                       id="allow_multiple_products"
                                       {{ old('allow_multiple_products', $store->allow_multiple_products ?? false) ? 'checked' : '' }}
                                       onchange="toggleMultipleProductsMode(this.checked)">
                                <strong>Activer le mode multi-produits</strong>
                            </label>
                            <div class="form-hint" id="multipleProductsHint" style="margin-top: 0.5rem; display: {{ old('allow_multiple_products', $store->allow_multiple_products ?? false) ? 'block' : 'none' }};">
                                <i class="fas fa-info-circle icon-inline"></i>
                                En mode multi-produits, vos produits seront affichés en grille sur la page d'accueil. Chaque produit aura sa propre page de détail accessible en cliquant dessus.
                            </div>
                        </div>
                        
                        <!-- Sélection du produit à afficher (uniquement en mode monoproduit) -->
                        <div class="form-group" id="featuredProductGroup" style="display: {{ old('allow_multiple_products', $store->allow_multiple_products ?? false) ? 'none' : 'block' }};">
                            <label class="form-label">
                                <i class="fas fa-star icon-inline"></i>
                                Produit à afficher
                            </label>
                            <select name="featured_product_id" id="featured_product_id" class="form-input featured-product-select">
                                <option value="">-- Sélectionner un produit --</option>
                                @foreach($products as $product)
                                    @php
                                        $productName = Str::limit($product->name, 50);
                                        $productPrice = number_format($product->selling_price, 2) . ' ' . ($store->currency ?? 'USD');
                                    @endphp
                                    <option value="{{ $product->id }}" 
                                        {{ old('featured_product_id', $store->featured_product_id) == $product->id ? 'selected' : '' }}>
                                        {{ $productName }} - {{ $productPrice }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-hint" style="margin-top: 0.5rem;">
                                <i class="fas fa-info-circle icon-inline"></i>
                                Choisissez quel produit afficher sur votre boutique en mode monoproduit. Si aucun produit n'est sélectionné, le dernier produit ajouté sera affiché par défaut.
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

                        <!-- Bannière (Thème Vibrant) -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-images icon-inline"></i>
                                Bannière (Thème Vibrant)
                            </label>
                            <p class="form-hint">
                                Configurez les images affichées dans le carrousel de la bannière pour le thème <strong>Vibrant</strong>.
                                Ces réglages n'affectent pas les images des produits eux-mêmes.
                            </p>

                            @php
                                $bannerUseProductImages = $s['vibrant_banner_use_product_images'] ?? true;
                                $bannerSelectedProductImages = $s['vibrant_banner_selected_product_images'] ?? [];
                                $bannerCustomImages = $s['vibrant_banner_custom_images'] ?? [];
                                
                                // Récupérer toutes les images de tous les produits
                                $allProductsImages = [];
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
                                        $allProductsImages[] = [
                                            'product' => $prod,
                                            'images' => $prodImages
                                        ];
                                    }
                                }
                            @endphp

                            <div class="banner-options">
                                <label class="checkbox-item" style="width: fit-content;">
                                    <input type="checkbox"
                                           name="settings[vibrant_banner_use_product_images]"
                                           value="1"
                                           {{ $bannerUseProductImages ? 'checked' : '' }}>
                                    Utiliser les images des produits dans la bannière
                                </label>
                            </div>

                            @if(!empty($allProductsImages))
                                <div class="banner-product-images">
                                    <p class="form-hint">
                                        Cochez les images des produits à afficher dans la bannière (Thème Vibrant).
                                        Vous pouvez sélectionner des images depuis différents produits.
                                        Supprimer ici ne supprime pas les images du produit, uniquement de la bannière.
                                    </p>
                                    @foreach($allProductsImages as $productData)
                                        <div class="banner-product-group" style="margin-bottom: 2rem; padding: 1rem; background: #f9fafb; border-radius: 8px;">
                                            <h4 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 600; color: #1f2937;">
                                                <i class="fas fa-box icon-inline"></i>
                                                {{ $productData['product']->name }}
                                            </h4>
                                            <div class="banner-images-grid">
                                                @foreach($productData['images'] as $imgUrl)
                                                    @php
                                                        $checked = empty($bannerSelectedProductImages) || in_array($imgUrl, $bannerSelectedProductImages);
                                                    @endphp
                                                    <label class="banner-image-item">
                                                        <input type="checkbox"
                                                               name="settings[vibrant_banner_selected_product_images][]"
                                                               value="{{ $imgUrl }}"
                                                               {{ $checked ? 'checked' : '' }}>
                                                        <span class="banner-image-thumb">
                                                            <img src="{{ $imgUrl }}" alt="Image bannière">
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info" style="padding: 1rem; background: #e0f2fe; border-radius: 8px; color: #0369a1;">
                                    <i class="fas fa-info-circle icon-inline"></i>
                                    Aucune image de produit disponible. Ajoutez des images à vos produits pour pouvoir les utiliser dans la bannière.
                                </div>
                            @endif

                            <div class="banner-custom-images">
                                <p class="form-hint">
                                    Ajoutez des images personnalisées pour la bannière (ex : affiches de promotion, visuels marketing).
                                </p>
                                <div id="bannerCustomImagesList" class="banner-images-grid">
                                    @if(!empty($bannerCustomImages) && is_array($bannerCustomImages))
                                        @foreach($bannerCustomImages as $imgUrl)
                                            <div class="banner-image-item">
                                                <input type="hidden" name="settings[vibrant_banner_custom_images][]" value="{{ $imgUrl }}">
                                                <span class="banner-image-thumb">
                                                    <img src="{{ $imgUrl }}" alt="Image bannière personnalisée">
                                                </span>
                                                <button type="button" class="btn-remove-banner-image" onclick="removeBannerCustomImage(this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="banner-upload-actions">
                                    <input type="file"
                                           id="bannerCustomImageFile"
                                           accept="image/*"
                                           style="display:none"
                                           onchange="handleBannerImageUpload(this)">
                                    <button type="button" class="btn-secondary" onclick="document.getElementById('bannerCustomImageFile').click()">
                                        <i class="fas fa-upload icon-inline"></i>
                                        Ajouter une image de bannière
                                    </button>
                                    <p class="form-hint" style="margin-top:0.5rem;">
                                        Format recommandé : environ 1600x500 px pour un rendu optimal sur la bannière.
                                    </p>
                                </div>
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

                <!-- Section 4: Nom de domaine -->
                <section id="domain" class="customize-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-globe icon-inline"></i>
                            Nom de domaine
                        </h2>
                        <p class="section-description">Personnalisez l'URL de votre boutique avec un nom unique ou connectez votre propre domaine</p>
                    </div>

                    <div class="section-content">
                        <!-- URL actuelle de la boutique -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-link icon-inline"></i>
                                URL actuelle de la boutique
                            </label>
                            <div class="current-url-display">
                                <div class="url-display-box">
                                    <span class="url-text" id="currentStoreUrl">{{ url('/store/' . $store->slug) }}</span>
                                    <button type="button" class="btn-copy-url" onclick="copyStoreUrl()" title="Copier l'URL">
                                        <i class="fas fa-copy"></i>
                                        <span>Copier</span>
                                    </button>
                                </div>
                                <p class="form-hint">URL actuelle de votre boutique. Vous pouvez la personnaliser ci-dessous.</p>
                            </div>
                        </div>

                        <!-- Personnaliser l'URL -->
                        <div class="form-group-collapsible">
                            <button type="button" class="form-group-header-btn" onclick="toggleFormGroup(this)">
                                <div class="form-group-header-content">
                                    <i class="fas fa-edit icon-inline"></i>
                                    <span class="form-group-title">Personnaliser l'URL de la boutique</span>
                                </div>
                                <i class="fas fa-chevron-down form-group-arrow"></i>
                            </button>
                            <div class="form-group-content">
                                <div class="custom-url-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <p><strong>Attention :</strong> Une fois défini, ce nom ne peut pas être modifié. Choisissez un nom unique pour votre boutique.</p>
                                </div>
                                
                                <div class="custom-url-form">
                                    <label for="custom_store_slug" class="form-label-small">Nom unique de votre boutique</label>
                                    <div class="url-input-wrapper">
                                        <input type="text" 
                                               id="custom_store_slug" 
                                               name="custom_store_slug" 
                                               class="form-input url-input" 
                                               value="{{ old('custom_store_slug', $store->slug) }}"
                                               placeholder="nom-de-votre-boutique"
                                               pattern="[a-z0-9-]+"
                                               oninput="updateUrlPreview()">
                                        <span class="url-domain-suffix">
                                            .<span id="platform-domain">{{ config('app.platform_domain', 'dropshipping.com') }}</span>
                                        </span>
                                        <input type="hidden" name="slug" id="store_slug" value="{{ $store->slug }}">
                                    </div>
                                    <div class="url-preview-box">
                                        <span class="url-preview-label">Aperçu de l'URL :</span>
                                        <span class="url-preview-text" id="urlPreview">
                                            https://<span id="previewSlug">{{ $store->slug }}</span>.<span id="previewDomain">{{ config('app.platform_domain', 'dropshipping.com') }}</span>
                                        </span>
                                    </div>
                                    <p class="form-hint">Utilisez uniquement des lettres minuscules, des chiffres et des tirets. Pas d'espaces ni de caractères spéciaux.</p>
                                    
                                    <div class="url-form-actions">
                                        <button type="button" class="btn-secondary" onclick="cancelUrlCustomization()">
                                            <i class="fas fa-times"></i>
                                            Annuler
                                        </button>
                                        <button type="button" class="btn-primary" onclick="saveUrlCustomization()">
                                            <i class="fas fa-save"></i>
                                            Enregistrer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Domaine personnalisé -->
                        <div class="form-group-collapsible" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                            <button type="button" class="form-group-header-btn" onclick="toggleFormGroup(this)">
                                <div class="form-group-header-content">
                                    <i class="fas fa-globe icon-inline"></i>
                                    <span class="form-group-title">Domaine personnalisé</span>
                                </div>
                                <i class="fas fa-chevron-down form-group-arrow"></i>
                            </button>
                            <div class="form-group-content">
                                <div class="custom-domain-info">
                                    <div class="info-card">
                                        <i class="fas fa-shield-alt"></i>
                                        <div>
                                            <h4>Certificat SSL inclus</h4>
                                            <p>Votre domaine sera sécurisé avec un certificat SSL gratuit</p>
                                        </div>
                                    </div>
                                    <div class="info-card">
                                        <i class="fas fa-cog"></i>
                                        <div>
                                            <h4>Configuration automatique</h4>
                                            <p>Configuration automatique du domaine pour votre boutique</p>
                                        </div>
                                    </div>
                                    <div class="info-card">
                                        <i class="fas fa-link"></i>
                                        <div>
                                            <h4>URL professionnel</h4>
                                            <p>Utilisez votre propre nom de domaine pour une image plus professionnelle</p>
                                        </div>
                                    </div>
                                </div>

                                @if($store->domain)
                                    <div class="current-domain-display">
                                        <label class="form-label-small">Domaine actuel</label>
                                        <div class="domain-display-box">
                                            <span class="domain-text">{{ $store->domain }}</span>
                                            <span class="domain-status active">
                                                <i class="fas fa-check-circle"></i>
                                                Connecté
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                <div class="custom-domain-options">
                                    <div class="domain-option-card">
                                        <div class="domain-option-header">
                                            <i class="fas fa-shopping-cart"></i>
                                            <h3>Obtenir un domaine</h3>
                                        </div>
                                        <p>Achetez un nouveau nom de domaine directement depuis notre plateforme</p>
                                        <button type="button" class="btn-domain-action" onclick="openGetDomainModal()">
                                            <i class="fas fa-plus"></i>
                                            Obtenir un domaine
                                        </button>
                                    </div>

                                    <div class="domain-option-card">
                                        <div class="domain-option-header">
                                            <i class="fas fa-plug"></i>
                                            <h3>Connecter un domaine</h3>
                                        </div>
                                        <p>Connectez un domaine que vous possédez déjà à votre boutique</p>
                                        <button type="button" class="btn-domain-action" onclick="openConnectDomainModal()">
                                            <i class="fas fa-link"></i>
                                            Connecter un domaine
                                        </button>
                                    </div>
                                </div>

                                @if(!$store->domain)
                                    <div class="custom-domain-form" id="connectDomainForm" style="display: none;">
                                        <label for="custom_domain" class="form-label-small">Nom de domaine</label>
                                        <input type="text" 
                                               id="custom_domain" 
                                               name="custom_domain" 
                                               class="form-input" 
                                               value="{{ old('custom_domain', $store->domain ?? '') }}"
                                               placeholder="exemple.com"
                                               pattern="[a-z0-9.-]+\.[a-z]{2,}">
                                        <p class="form-hint">Entrez votre nom de domaine (ex: monboutique.com). Ne pas inclure http:// ou https://</p>
                                        
                                        <div class="domain-form-actions">
                                            <button type="button" class="btn-secondary" onclick="cancelDomainConnection()">
                                                <i class="fas fa-times"></i>
                                                Annuler
                                            </button>
                                            <button type="button" class="btn-primary" onclick="saveDomainConnection()">
                                                <i class="fas fa-save"></i>
                                                Connecter le domaine
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 5: Gestion et opération -->
                <section id="operations" class="customize-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-cogs icon-inline"></i>
                            Gestion et opération
                        </h2>
                        <p class="section-description">Configurez les analytiques, le support client, les notifications et le SEO de votre boutique</p>
                    </div>

                    <div class="section-content">
                        <!-- Analytiques et pixels -->
                        <div class="form-group-collapsible">
                            <button type="button" class="form-group-header-btn" onclick="toggleFormGroup(this)">
                                <div class="form-group-header-content">
                                    <i class="fas fa-chart-line icon-inline"></i>
                                    <span class="form-group-title">Analytiques et pixels</span>
                                </div>
                                <i class="fas fa-chevron-down form-group-arrow"></i>
                            </button>
                            <div class="form-group-content">
                                <p class="form-hint">Connectez vos outils d'analytique et de suivi pour mesurer les performances de votre boutique</p>
                                
                                <!-- Facebook Pixel -->
                                <div class="form-group" style="margin-top: 1.5rem;">
                                    <label for="facebook_pixel_id" class="form-label-small">
                                        <i class="fab fa-facebook"></i>
                                        ID Pixel Facebook
                                    </label>
                                    <input type="text" 
                                           id="facebook_pixel_id" 
                                           name="settings[facebook_pixel_id]" 
                                           class="form-input" 
                                           value="{{ old('settings.facebook_pixel_id', $store->settings['facebook_pixel_id'] ?? '') }}"
                                           placeholder="Ex: 123456789012345">
                                    <p class="form-hint">Votre ID Pixel Facebook pour suivre les conversions et créer des audiences</p>
                                </div>

                                <!-- Google Analytics / Google Tag Manager -->
                                <div class="form-group">
                                    <label for="google_analytics_id" class="form-label-small">
                                        <i class="fab fa-google"></i>
                                        ID Google Analytics / Google Tag Manager
                                    </label>
                                    <input type="text" 
                                           id="google_analytics_id" 
                                           name="settings[google_analytics_id]" 
                                           class="form-input" 
                                           value="{{ old('settings.google_analytics_id', $store->settings['google_analytics_id'] ?? '') }}"
                                           placeholder="Ex: G-XXXXXXXXXX ou GTM-XXXXXXX">
                                    <p class="form-hint">Votre ID Google Analytics (G-XXXXXXXXXX) ou Google Tag Manager (GTM-XXXXXXX)</p>
                                </div>

                                <!-- TikTok Pixel -->
                                <div class="form-group">
                                    <label for="tiktok_pixel_id" class="form-label-small">
                                        <i class="fab fa-tiktok"></i>
                                        ID Pixel TikTok
                                    </label>
                                    <input type="text" 
                                           id="tiktok_pixel_id" 
                                           name="settings[tiktok_pixel_id]" 
                                           class="form-input" 
                                           value="{{ old('settings.tiktok_pixel_id', $store->settings['tiktok_pixel_id'] ?? '') }}"
                                           placeholder="Ex: C1234567890ABCDEF">
                                    <p class="form-hint">Votre ID Pixel TikTok pour suivre les événements et optimiser vos campagnes</p>
                                </div>

                                <!-- Code JavaScript personnalisé -->
                                <div class="form-group">
                                    <label for="custom_tracking_code" class="form-label-small">
                                        <i class="fas fa-code"></i>
                                        Code JavaScript personnalisé
                                    </label>
                                    <textarea id="custom_tracking_code" 
                                              name="settings[custom_tracking_code]" 
                                              class="form-textarea" 
                                              rows="6"
                                              placeholder="<!-- Votre code de suivi personnalisé ici -->">{{ old('settings.custom_tracking_code', $store->settings['custom_tracking_code'] ?? '') }}</textarea>
                                    <p class="form-hint">Code JavaScript personnalisé pour suivre les événements de conversion améliorés et enregistrés. Ce code sera ajouté dans le &lt;head&gt; de votre boutique.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Support client -->
                        <div class="form-group-collapsible" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                            <button type="button" class="form-group-header-btn" onclick="toggleFormGroup(this)">
                                <div class="form-group-header-content">
                                    <i class="fas fa-headset icon-inline"></i>
                                    <span class="form-group-title">Support client</span>
                                </div>
                                <i class="fas fa-chevron-down form-group-arrow"></i>
                            </button>
                            <div class="form-group-content">
                                <p class="form-hint">Configurez les informations de contact et les options de support client</p>
                                
                                <!-- Email support -->
                                <div class="form-group" style="margin-top: 1.5rem;">
                                    <label for="support_email" class="form-label-small">
                                        <i class="fas fa-envelope"></i>
                                        Email du support client
                                    </label>
                                    <input type="email" 
                                           id="support_email" 
                                           name="settings[support_email]" 
                                           class="form-input" 
                                           value="{{ old('settings.support_email', $store->settings['support_email'] ?? ($store->user->email ?? '')) }}"
                                           placeholder="support@votreboutique.com">
                                    <p class="form-hint">Email où vos clients peuvent vous contacter pour le support</p>
                                </div>

                                <!-- Téléphone support -->
                                <div class="form-group">
                                    <label for="support_phone" class="form-label-small">
                                        <i class="fas fa-phone"></i>
                                        Numéro de téléphone d'assistance
                                    </label>
                                    <input type="tel" 
                                           id="support_phone" 
                                           name="settings[support_phone]" 
                                           class="form-input" 
                                           value="{{ old('settings.support_phone', $store->settings['support_phone'] ?? '') }}"
                                           placeholder="+33 1 23 45 67 89">
                                    <p class="form-hint">Numéro de téléphone pour l'assistance client</p>
                                </div>

                                <!-- WhatsApp -->
                                <div class="form-group">
                                    <label for="support_whatsapp" class="form-label-small">
                                        <i class="fab fa-whatsapp"></i>
                                        WhatsApp d'assistance
                                    </label>
                                    <input type="text" 
                                           id="support_whatsapp" 
                                           name="settings[support_whatsapp]" 
                                           class="form-input" 
                                           value="{{ old('settings.support_whatsapp', $store->settings['support_whatsapp'] ?? '') }}"
                                           placeholder="+33 6 12 34 56 78">
                                    <p class="form-hint">Numéro WhatsApp pour le support client (format international avec +)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Notifications -->
                        <div class="form-group-collapsible" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                            <button type="button" class="form-group-header-btn" onclick="toggleFormGroup(this)">
                                <div class="form-group-header-content">
                                    <i class="fas fa-bell icon-inline"></i>
                                    <span class="form-group-title">Notifications</span>
                                </div>
                                <i class="fas fa-chevron-down form-group-arrow"></i>
                            </button>
                            <div class="form-group-content">
                                <p class="form-hint">Gérez les alertes email et Telegram pour suivre l'activité de votre boutique</p>
                                
                                <!-- Alertes email -->
                                <div class="form-group" style="margin-top: 1.5rem;">
                                    <label class="form-label-small">
                                        <i class="fas fa-envelope"></i>
                                        Alertes email
                                    </label>
                                    <div class="checkbox-item" style="margin-top: 0.5rem;">
                                        <input type="checkbox" 
                                               id="email_alerts_enabled" 
                                               name="settings[email_alerts_enabled]" 
                                               value="1" 
                                               {{ old('settings.email_alerts_enabled', $store->settings['email_alerts_enabled'] ?? true) ? 'checked' : '' }}>
                                        <label for="email_alerts_enabled" style="margin-left: 0.5rem; cursor: pointer;">
                                            Activer les alertes email pour suivre les nouvelles commandes et l'activité de la boutique
                                        </label>
                                    </div>
                                    <p class="form-hint">Recevez des notifications par email pour les nouvelles commandes et événements importants</p>
                                </div>

                                <!-- Alertes Telegram -->
                                <div class="form-group">
                                    <label class="form-label-small">
                                        <i class="fab fa-telegram"></i>
                                        Alertes Telegram
                                    </label>
                                    <div class="telegram-setup">
                                        <div class="checkbox-item" style="margin-top: 0.5rem; margin-bottom: 1rem;">
                                            <input type="checkbox" 
                                                   id="telegram_alerts_enabled" 
                                                   name="settings[telegram_alerts_enabled]" 
                                                   value="1" 
                                                   {{ old('settings.telegram_alerts_enabled', $store->settings['telegram_alerts_enabled'] ?? false) ? 'checked' : '' }}
                                                   onchange="toggleTelegramSetup()">
                                            <label for="telegram_alerts_enabled" style="margin-left: 0.5rem; cursor: pointer;">
                                                Activer les alertes Telegram
                                            </label>
                                        </div>
                                        
                                        <div id="telegramSetupContent" style="display: {{ old('settings.telegram_alerts_enabled', $store->settings['telegram_alerts_enabled'] ?? false) ? 'block' : 'none' }};">
                                            <div class="telegram-info-box">
                                                <h4>Configuration Telegram</h4>
                                                <p>Pour activer les alertes Telegram, suivez ces étapes :</p>
                                                <ol style="margin: 1rem 0; padding-left: 1.5rem;">
                                                    <li>Téléchargez l'application Telegram sur votre téléphone</li>
                                                    <li>Ouvrez Telegram et recherchez le bot : <strong>@VotreBotDropshipping</strong></li>
                                                    <li>Scannez le QR code ci-dessous avec l'application Telegram</li>
                                                    <li>Ou cliquez sur le lien pour ouvrir Telegram directement</li>
                                                </ol>
                                                
                                                <div class="telegram-qr-section">
                                                    <div class="qr-code-container">
                                                        <div class="qr-code-placeholder" id="telegramQRCode">
                                                            <i class="fas fa-qrcode" style="font-size: 3rem; color: #0ea5e9;"></i>
                                                            <p>QR Code Telegram</p>
                                                            <p style="font-size: 0.875rem; color: #6b7280;">Le QR code sera généré après activation</p>
                                                        </div>
                                                    </div>
                                                    <div class="telegram-link-section">
                                                        <a href="https://t.me/VotreBotDropshipping" target="_blank" class="btn-telegram-link">
                                                            <i class="fab fa-telegram"></i>
                                                            Ouvrir Telegram
                                                        </a>
                                                        <button type="button" class="btn-refresh-qr" onclick="refreshTelegramQR()">
                                                            <i class="fas fa-sync-alt"></i>
                                                            Actualiser le QR code
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEO et référencement -->
                        <div class="form-group-collapsible" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                            <button type="button" class="form-group-header-btn" onclick="toggleFormGroup(this)">
                                <div class="form-group-header-content">
                                    <i class="fas fa-search icon-inline"></i>
                                    <span class="form-group-title">SEO et référencement</span>
                                </div>
                                <i class="fas fa-chevron-down form-group-arrow"></i>
                            </button>
                            <div class="form-group-content">
                                <p class="form-hint">Optimisez le référencement de votre boutique pour améliorer sa visibilité sur les moteurs de recherche</p>
                                
                                <!-- Aperçu SEO -->
                                <div class="seo-preview-box" style="margin-top: 1.5rem;">
                                    <h4 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 600; color: #374151;">Aperçu du résultat de recherche</h4>
                                    <div class="seo-preview-card">
                                        <div class="seo-preview-url" id="seoPreviewUrl">
                                            https://{{ $store->slug }}.{{ config('app.platform_domain', 'dropshipping.com') }}
                                        </div>
                                        <div class="seo-preview-title" id="seoPreviewTitle">
                                            {{ $store->name }} - Boutique en ligne
                                        </div>
                                        <div class="seo-preview-description" id="seoPreviewDescription">
                                            {{ Str::limit($store->description ?? 'Découvrez nos produits de qualité', 160) }}
                                        </div>
                                    </div>
                                    <p class="form-hint" style="margin-top: 0.5rem;">Aperçu de l'apparence de votre boutique dans les résultats de recherche Google</p>
                                </div>

                                <!-- Titre SEO -->
                                <div class="form-group" style="margin-top: 1.5rem;">
                                    <label for="seo_title" class="form-label-small">
                                        <i class="fas fa-heading"></i>
                                        Titre SEO (Meta Title)
                                    </label>
                                    <input type="text" 
                                           id="seo_title" 
                                           name="settings[seo_title]" 
                                           class="form-input" 
                                           value="{{ old('settings.seo_title', $store->settings['seo_title'] ?? $store->name) }}"
                                           placeholder="Titre de votre boutique (50-60 caractères recommandés)"
                                           maxlength="60"
                                           oninput="updateSEOPreview()">
                                    <div class="char-counter">
                                        <span id="seoTitleCounter">0</span>/60 caractères
                                    </div>
                                    <p class="form-hint">Titre qui apparaîtra dans les résultats de recherche. Idéalement entre 50 et 60 caractères.</p>
                                </div>

                                <!-- Métadescription -->
                                <div class="form-group">
                                    <label for="seo_description" class="form-label-small">
                                        <i class="fas fa-align-left"></i>
                                        Métadescription
                                    </label>
                                    <textarea id="seo_description" 
                                              name="settings[seo_description]" 
                                              class="form-textarea" 
                                              rows="4"
                                              placeholder="Description de votre boutique (150-160 caractères recommandés)"
                                              maxlength="160"
                                              oninput="updateSEOPreview()">{{ old('settings.seo_description', $store->settings['seo_description'] ?? Str::limit($store->description ?? '', 160)) }}</textarea>
                                    <div class="char-counter">
                                        <span id="seoDescriptionCounter">0</span>/160 caractères
                                    </div>
                                    <p class="form-hint">Description qui apparaîtra sous le titre dans les résultats de recherche. Idéalement entre 150 et 160 caractères.</p>
                                </div>

                                <!-- Mots-clés -->
                                <div class="form-group">
                                    <label for="seo_keywords" class="form-label-small">
                                        <i class="fas fa-tags"></i>
                                        Mots-clés SEO
                                    </label>
                                    <input type="text" 
                                           id="seo_keywords" 
                                           name="settings[seo_keywords]" 
                                           class="form-input" 
                                           value="{{ old('settings.seo_keywords', $store->settings['seo_keywords'] ?? '') }}"
                                           placeholder="mot-clé1, mot-clé2, mot-clé3">
                                    <p class="form-hint">Mots-clés séparés par des virgules pour améliorer le référencement (ex: dropshipping, e-commerce, boutique en ligne)</p>
                                </div>

                                <!-- Image Open Graph (Miniature) -->
                                <div class="form-group">
                                    <label for="seo_og_image" class="form-label-small">
                                        <i class="fas fa-image"></i>
                                        Image Open Graph (Miniature)
                                    </label>
                                    <div class="og-image-upload">
                                        <input type="url" 
                                               id="seo_og_image" 
                                               name="settings[seo_og_image]" 
                                               class="form-input" 
                                               value="{{ old('settings.seo_og_image', $store->settings['seo_og_image'] ?? $store->banner) }}"
                                               placeholder="https://exemple.com/image-og.jpg">
                                        <button type="button" class="btn-secondary btn-upload" onclick="document.getElementById('seo_og_image_file').click()">
                                            <i class="fas fa-upload"></i>
                                            Uploader
                                        </button>
                                        <input type="file" 
                                               id="seo_og_image_file" 
                                               name="seo_og_image_file" 
                                               accept="image/*" 
                                               style="display: none;"
                                               onchange="handleOGImageUpload(this)">
                                    </div>
                                    <div id="og_image_preview" class="og-image-preview" style="margin-top: 1rem; {{ $store->settings['seo_og_image'] ?? $store->banner ? '' : 'display: none;' }}">
                                        <img src="{{ $store->settings['seo_og_image'] ?? $store->banner ?? '' }}" alt="Aperçu OG Image" style="max-width: 100%; max-height: 200px; border-radius: 8px; border: 1px solid #e5e7eb;">
                                    </div>
                                    <p class="form-hint">Image qui apparaîtra lors du partage sur les réseaux sociaux (Facebook, Twitter, etc.). Dimensions recommandées : 1200x630px</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 6: Gestion du compte -->
                <section id="account" class="customize-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-user-cog icon-inline"></i>
                            Gestion du compte
                        </h2>
                        <p class="section-description">Gérez votre profil, votre équipe et votre facturation</p>
                    </div>

                    <div class="section-content">
                        <!-- Gérer mon profil -->
                        <div class="form-group-collapsible" style="padding: 2rem;">
                            <button type="button" class="form-group-header-btn" onclick="toggleFormGroup(this)">
                                <div class="form-group-header-content">
                                    <i class="fas fa-user icon-inline"></i>
                                    <span class="form-group-title">Gérer mon profil</span>
                                </div>
                                <i class="fas fa-chevron-down form-group-arrow"></i>
                            </button>
                            <div class="form-group-content">
                                <!-- Menu horizontal avec onglets -->
                                <div class="profile-tabs">
                                    <button type="button" class="profile-tab active" onclick="switchProfileTab('personal')">
                                        <i class="fas fa-user"></i>
                                        Infos personnelles
                                    </button>
                                    <button type="button" class="profile-tab" onclick="switchProfileTab('email')">
                                        <i class="fas fa-envelope"></i>
                                        Email
                                    </button>
                                    <button type="button" class="profile-tab" onclick="switchProfileTab('language')">
                                        <i class="fas fa-globe"></i>
                                        Langue
                                    </button>
                                </div>

                                <!-- Onglet Infos personnelles -->
                                <div id="profileTabPersonal" class="profile-tab-content active">
                                    <div class="profile-image-section">
                                        <label class="form-label-small">Image de profil</label>
                                        <div class="profile-image-upload">
                                            <div class="profile-image-preview" id="profileImagePreview">
                                                @if(auth()->user()->profile_image ?? false)
                                                    <img src="{{ auth()->user()->profile_image }}" alt="Photo de profil">
                                                @else
                                                    <div class="profile-image-placeholder">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="profile-image-actions">
                                                <button type="button" class="btn-secondary" onclick="document.getElementById('profile_image_file').click()">
                                                    <i class="fas fa-upload"></i>
                                                    Modifier
                                                </button>
                                                @if(auth()->user()->profile_image ?? false)
                                                    <button type="button" class="btn-secondary btn-danger" onclick="removeProfileImage()">
                                                        <i class="fas fa-trash"></i>
                                                        Supprimer
                                                    </button>
                                                @endif
                                            </div>
                                            <input type="file" 
                                                   id="profile_image_file" 
                                                   name="profile_image_file" 
                                                   accept="image/*" 
                                                   style="display: none;"
                                                   onchange="handleProfileImageUpload(this)">
                                            <input type="hidden" id="profile_image" name="profile_image" value="{{ auth()->user()->profile_image ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="form-group" style="margin-top: 1.5rem;">
                                        <label for="user_first_name" class="form-label-small">Prénom</label>
                                        <input type="text" 
                                               id="user_first_name" 
                                               class="form-input" 
                                               value="{{ auth()->user()->first_name ?? '' }}"
                                               disabled
                                               style="background: #f3f4f6; cursor: not-allowed;">
                                        <p class="form-hint">Le prénom ne peut pas être modifié (défini lors de la création du compte)</p>
                                    </div>

                                    <div class="form-group">
                                        <label for="user_last_name" class="form-label-small">Nom</label>
                                        <input type="text" 
                                               id="user_last_name" 
                                               class="form-input" 
                                               value="{{ auth()->user()->last_name ?? '' }}"
                                               disabled
                                               style="background: #f3f4f6; cursor: not-allowed;">
                                        <p class="form-hint">Le nom ne peut pas être modifié (défini lors de la création du compte)</p>
                                    </div>

                                    <div class="form-group">
                                        <label for="user_phone" class="form-label-small">
                                            <i class="fas fa-phone"></i>
                                            Numéro de téléphone
                                        </label>
                                        <input type="tel" 
                                               id="user_phone" 
                                               name="user_phone" 
                                               class="form-input" 
                                               value="{{ old('user_phone', auth()->user()->phone ?? '') }}"
                                               placeholder="+33 6 12 34 56 78">
                                        <p class="form-hint">Numéro de téléphone pour les notifications et le support</p>
                                    </div>

                                    <div class="form-actions-inline" style="margin-top: 1.5rem;">
                                        <button type="button" class="btn-primary" onclick="updateProfileInfo()">
                                            <i class="fas fa-save"></i>
                                            Mettre à jour
                                        </button>
                                    </div>
                                </div>

                                <!-- Onglet Email -->
                                <div id="profileTabEmail" class="profile-tab-content">
                                    <div class="form-group">
                                        <label class="form-label-small">Email actuel</label>
                                        <div class="current-email-display">
                                            <span class="email-text">{{ auth()->user()->email }}</span>
                                            @if(auth()->user()->email_verified_at)
                                                <span class="email-status verified">
                                                    <i class="fas fa-check-circle"></i>
                                                    Vérifié
                                                </span>
                                            @else
                                                <span class="email-status unverified">
                                                    <i class="fas fa-exclamation-circle"></i>
                                                    Non vérifié
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @php
                                        $emailChangeRequest = auth()->user()->email_change_request ?? null;
                                    @endphp

                                    @if($emailChangeRequest)
                                        <div class="email-change-pending">
                                            <div class="alert-info">
                                                <i class="fas fa-info-circle"></i>
                                                <div>
                                                    <strong>Demande de changement d'email en cours</strong>
                                                    <p>Nouvel email demandé : <strong>{{ $emailChangeRequest['new_email'] }}</strong></p>
                                                    <p style="font-size: 0.875rem; color: #6b7280;">Un email de confirmation a été envoyé à cette adresse.</p>
                                                </div>
                                            </div>
                                            <div class="email-change-actions">
                                                <button type="button" class="btn-secondary" onclick="resendEmailConfirmation()">
                                                    <i class="fas fa-paper-plane"></i>
                                                    Renvoyer le mail de confirmation
                                                </button>
                                                <button type="button" class="btn-secondary btn-danger" onclick="cancelEmailChange()">
                                                    <i class="fas fa-times"></i>
                                                    Annuler la demande
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="form-group" style="margin-top: 1.5rem;">
                                            <label for="new_email" class="form-label-small">Nouvel email</label>
                                            <input type="email" 
                                                   id="new_email" 
                                                   name="new_email" 
                                                   class="form-input" 
                                                   placeholder="nouveau@email.com">
                                            <p class="form-hint">Entrez votre nouvel email. Un email de confirmation sera envoyé.</p>
                                        </div>

                                        <div class="form-actions-inline">
                                            <button type="button" class="btn-primary" onclick="requestEmailChange()">
                                                <i class="fas fa-envelope"></i>
                                                Demander le changement d'email
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <!-- Onglet Langue -->
                                <div id="profileTabLanguage" class="profile-tab-content">
                                    <div class="form-group">
                                        <label for="user_language" class="form-label-small">
                                            <i class="fas fa-globe"></i>
                                            Langue de l'interface
                                        </label>
                                        <select id="user_language" 
                                                name="user_language" 
                                                class="form-select"
                                                onchange="updateLanguage()">
                                            <option value="fr" {{ (auth()->user()->language ?? 'fr') === 'fr' ? 'selected' : '' }}>
                                                <i class="fas fa-flag"></i> Français
                                            </option>
                                            <option value="en" {{ (auth()->user()->language ?? 'fr') === 'en' ? 'selected' : '' }}>
                                                <i class="fas fa-flag"></i> English
                                            </option>
                                        </select>
                                        <p class="form-hint">Sélectionnez votre langue préférée pour l'interface de la plateforme</p>
                                    </div>

                                    <div class="form-actions-inline">
                                        <button type="button" class="btn-primary" onclick="saveLanguage()">
                                            <i class="fas fa-save"></i>
                                            Enregistrer la langue
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Équipe et collaborateurs -->
                        <div class="form-group-collapsible" style="margin-top: 0; padding: 2rem; border-top: 1px solid #e5e7eb;">
                            <button type="button" class="form-group-header-btn" onclick="toggleFormGroup(this)">
                                <div class="form-group-header-content">
                                    <i class="fas fa-users icon-inline"></i>
                                    <span class="form-group-title">Équipe et collaborateurs</span>
                                </div>
                                <i class="fas fa-chevron-down form-group-arrow"></i>
                            </button>
                            <div class="form-group-content">
                                <p class="form-hint">Gérez les membres de votre équipe et invitez des collaborateurs</p>
                                
                                <!-- Liste des membres -->
                                <div class="team-members-list" style="margin-top: 1.5rem;">
                                    <h4 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 600; color: #374151;">Membres de l'équipe</h4>
                                    
                                    <!-- Propriétaire (utilisateur actuel) -->
                                    <div class="team-member-card">
                                        <div class="team-member-info">
                                            <div class="team-member-avatar">
                                                @if(auth()->user()->profile_image ?? false)
                                                    <img src="{{ auth()->user()->profile_image }}" alt="{{ auth()->user()->name }}">
                                                @else
                                                    <div class="avatar-placeholder">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="team-member-details">
                                                <div class="member-name">{{ auth()->user()->name }}</div>
                                                <div class="member-email">{{ auth()->user()->email }}</div>
                                                <div class="member-meta">
                                                    <span class="member-role owner">Propriétaire</span>
                                                    <span class="member-status active">Actif</span>
                                                    <span class="member-date">Ajouté le {{ auth()->user()->created_at->format('d/m/Y') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="team-member-actions">
                                            <div class="dropdown-menu">
                                                <button type="button" class="dropdown-toggle" onclick="toggleDropdown(this)">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <div class="dropdown-content">
                                                    <a href="#" onclick="viewMember({{ auth()->user()->id }})">
                                                        <i class="fas fa-eye"></i>
                                                        Voir
                                                    </a>
                                                    <a href="#" onclick="editMember({{ auth()->user()->id }})">
                                                        <i class="fas fa-edit"></i>
                                                        Modifier
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Autres membres (à implémenter avec la base de données) -->
                                    @php
                                        // Pour l'instant, on affiche juste le propriétaire
                                        // Plus tard, on pourra ajouter d'autres membres depuis la base de données
                                    @endphp
                                </div>

                                <!-- Section Inviter -->
                                <div class="invite-section" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                                    <h4 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 600; color: #374151;">Inviter des membres à votre équipe</h4>
                                    <p class="form-hint">Invitez des collaborateurs à rejoindre votre équipe et gérer votre boutique</p>
                                    
                                    <div class="form-group" style="margin-top: 1rem;">
                                        <label for="invite_email" class="form-label-small">Email de l'invité</label>
                                        <input type="email" 
                                               id="invite_email" 
                                               name="invite_email" 
                                               class="form-input" 
                                               placeholder="collaborateur@email.com">
                                        <p class="form-hint">L'invité recevra un email avec un lien pour rejoindre votre équipe</p>
                                    </div>

                                    <div class="form-group">
                                        <label for="invite_role" class="form-label-small">Rôle</label>
                                        <select id="invite_role" 
                                                name="invite_role" 
                                                class="form-select">
                                            <option value="editor">Éditeur</option>
                                            <option value="viewer">Visualiseur</option>
                                        </select>
                                        <p class="form-hint">Définissez les permissions de l'invité</p>
                                    </div>

                                    <div class="form-actions-inline">
                                        <button type="button" class="btn-primary" onclick="sendInvitation()">
                                            <i class="fas fa-paper-plane"></i>
                                            Envoyer l'invitation
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Facturation -->
                        <div class="form-group-collapsible" style="margin-top: 0; padding: 2rem; border-top: 1px solid #e5e7eb;">
                            <button type="button" class="form-group-header-btn" onclick="toggleFormGroup(this)">
                                <div class="form-group-header-content">
                                    <i class="fas fa-credit-card icon-inline"></i>
                                    <span class="form-group-title">Facturation</span>
                                </div>
                                <i class="fas fa-chevron-down form-group-arrow"></i>
                            </button>
                            <div class="form-group-content">
                                <p class="form-hint">Consultez votre plan tarifaire actuel et gérez vos informations de paiement</p>
                                
                                @php
                                    $user = auth()->user();
                                    $activeSubscription = $user->activeSubscription();
                                    $currentPlan = $user->currentPlan();
                                @endphp

                                <!-- Plan actuel -->
                                <div class="current-plan-card" style="margin-top: 1.5rem;">
                                    <div class="plan-header">
                                        <div>
                                            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.25rem; font-weight: 700; color: #1f2937;">
                                                Plan actuel
                                            </h3>
                                            @if($currentPlan)
                                                <div class="plan-name-badge">
                                                    <i class="fas fa-crown"></i>
                                                    {{ $currentPlan->name ?? 'Plan Pro' }}
                                                </div>
                                            @else
                                                <div class="plan-name-badge free">
                                                    <i class="fas fa-gift"></i>
                                                    Plan Gratuit
                                                </div>
                                            @endif
                                        </div>
                                        @if($activeSubscription)
                                            <div class="plan-status active">
                                                <i class="fas fa-check-circle"></i>
                                                Actif
                                            </div>
                                        @endif
                                    </div>

                                    @if($activeSubscription)
                                        <div class="plan-details">
                                            <div class="plan-detail-row">
                                                <span class="detail-label">Date de début :</span>
                                                <span class="detail-value">{{ $activeSubscription->created_at->format('d/m/Y') }}</span>
                                            </div>
                                            <div class="plan-detail-row">
                                                <span class="detail-label">Date d'expiration :</span>
                                                <span class="detail-value">{{ $activeSubscription->ends_at ? $activeSubscription->ends_at->format('d/m/Y') : 'Illimité' }}</span>
                                            </div>
                                            @if($currentPlan)
                                                <div class="plan-detail-row">
                                                    <span class="detail-label">Prix :</span>
                                                    <span class="detail-value">{{ number_format($currentPlan->price ?? 0, 2) }} {{ $currentPlan->currency ?? 'EUR' }}/mois</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Fonctionnalités du plan -->
                                    @if($currentPlan)
                                        <div class="plan-features" style="margin-top: 1.5rem;">
                                            <h4 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 600; color: #374151;">Fonctionnalités incluses</h4>
                                            <div class="features-list">
                                                @php
                                                    $features = $currentPlan->features ?? [];
                                                    if (empty($features) && $currentPlan->slug === 'pro') {
                                                        $features = [
                                                            'Boutiques illimitées',
                                                            'Produits illimités',
                                                            'Support prioritaire',
                                                            'Analytiques avancées',
                                                            'Domaines personnalisés',
                                                            'Thèmes premium'
                                                        ];
                                                    }
                                                @endphp
                                                @if(!empty($features))
                                                    @foreach($features as $feature)
                                                        <div class="feature-item">
                                                            <i class="fas fa-check-circle"></i>
                                                            <span>{{ $feature }}</span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="feature-item">
                                                        <i class="fas fa-check-circle"></i>
                                                        <span>Toutes les fonctionnalités de base</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="plan-features" style="margin-top: 1.5rem;">
                                            <h4 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 600; color: #374151;">Fonctionnalités du plan gratuit</h4>
                                            <div class="features-list">
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>1 boutique</span>
                                                </div>
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Jusqu'à 10 produits</span>
                                                </div>
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Support par email</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Actions -->
                                    <div class="plan-actions" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                                        <a href="{{ route('merchant.subscription') }}" class="btn-primary">
                                            <i class="fas fa-arrow-right"></i>
                                            Voir les plans disponibles
                                        </a>
                                        @if($activeSubscription)
                                            <button type="button" class="btn-secondary" onclick="managePayment()">
                                                <i class="fas fa-credit-card"></i>
                                                Gérer les informations de paiement
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
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
    width: 100%;
    margin: 0;
    padding: 0;
    background: #f9fafb;
    position: relative;
    min-height: 100vh;
    overflow: hidden; /* Empêcher le scroll sur le body */
}

/* Cacher le footer de la plateforme sur la page de personnalisation */
.customize-page-wrapper ~ .footer,
body:has(.customize-page-wrapper) .footer {
    display: none !important;
}

/* Empêcher le scroll du body sur la page de personnalisation */
body:has(.customize-page-wrapper) {
    overflow: hidden !important;
    height: 100vh !important;
}

.customize-header {
    background: white;
    border-bottom: 1px solid #e5e7eb;
    padding: 1.5rem 0;
    position: fixed;
    top: 72px;
    left: 0;
    right: 0;
    width: 100%;
    z-index: 100;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    height: 72px; /* Hauteur fixe pour calculer correctement */
    display: flex;
    align-items: center;
}

.header-content {
    max-width: 100%;
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

.customize-title .icon-inline {
    color: #0ea5e9;
    font-size: 1.25rem;
}

.header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.btn-preview {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: background-color 0.2s;
    border: none;
    cursor: pointer;
    background: #0ea5e9;
    color: white;
}

.btn-preview:hover {
    background: #0284c7;
}

.btn-preview i {
    font-size: 0.9rem;
}

.btn-open-new-tab {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
    text-decoration: none;
    background: white;
    color: #374151;
    border: 1px solid #d1d5db;
}

.btn-open-new-tab:hover {
    background: #f9fafb;
    border-color: #9ca3af;
    color: #1f2937;
}

.btn-open-new-tab i {
    font-size: 0.9rem;
}

.customize-layout {
    width: 100%;
    margin: 0;
    padding: 0;
    display: flex;
    margin-top: 100px; /* Hauteur du header (72px plateforme + ~28px header personnalisation) */
    min-height: calc(100vh - 172px);
}

.customize-sidebar {
    background: #ffffff;
    width: 280px;
    padding: 0;
    height: calc(100vh - 172px - 20px);
    max-height: calc(100vh - 172px - 20px);
    position: fixed;
    left: 0;
    top: 172px;
    bottom: 20px;
    border-right: 1px solid #e5e7eb;
    box-shadow: 2px 0 8px rgba(0,0,0,0.05);
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 10;
}

.sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 0;
    padding: 1.5rem 0;
}

.nav-group {
    margin-bottom: 0;
}

.nav-group-header {
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    color: #374151;
    cursor: pointer;
    transition: background-color 0.2s;
    font-weight: 600;
    font-size: 0.95rem;
    background: transparent;
    border: none;
    border-bottom: 1px solid #e5e7eb;
}

.nav-group-header:hover {
    background: #f9fafb;
}

.nav-group-header.active {
    background: #f3f4f6;
    color: #0ea5e9;
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
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: #6b7280;
    text-decoration: none;
    transition: background-color 0.2s, color 0.2s;
    font-weight: 500;
    font-size: 0.9rem;
    position: relative;
    border-radius: 0;
    margin: 0;
}

.nav-item:hover {
    background: #f9fafb;
    color: #374151;
}

.nav-item.active {
    background: #eff6ff;
    color: #0ea5e9;
    font-weight: 600;
}

.nav-item.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 60%;
    background: #0ea5e9;
    border-radius: 0 2px 2px 0;
}

.customize-main {
    background: #f9fafb;
    flex: 1;
    padding: 0;
    margin: 0;
    width: calc(100% - 280px);
    overflow-y: auto;
    overflow-x: hidden;
    position: fixed;
    top: 144px; /* 72px (header plateforme) + 72px (header personnalisation) */
    left: 280px;
    right: 0;
    bottom: 0;
}

.customize-section {
    display: none;
    background: white;
    border-radius: 0;
    padding: 0;
    margin: 0;
    box-shadow: none;
    border: none;
    width: 100%;
    min-height: 100%;
}

.customize-section.active {
    display: block;
}

.section-header {
    margin: 0;
    padding: 2rem 2rem 1.5rem 2rem;
    border-bottom: 1px solid #e5e7eb;
    width: 100%;
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

.section-title .icon-inline {
    color: #0ea5e9;
    font-size: 1.25rem;
}

.section-description {
    color: #6b7280;
    margin: 0;
    font-size: 0.95rem;
    line-height: 1.5;
}

.section-content {
    display: flex;
    flex-direction: column;
    padding: 0;
    gap: 0;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group-collapsible {
    margin-bottom: 1.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: white;
    overflow: hidden;
}

.form-group-header-btn {
    width: 100%;
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f9fafb;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s;
    text-align: left;
}

.form-group-header-btn:hover {
    background: #f3f4f6;
}

.form-group-header-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.form-group-title {
    font-weight: 600;
    font-size: 1rem;
    color: #374151;
}

.form-group-header-btn .icon-inline {
    color: #0ea5e9;
    font-size: 1.1rem;
}

.form-group-arrow {
    transition: transform 0.3s;
    color: #6b7280;
    font-size: 0.875rem;
}

.form-group-content {
    padding: 1.5rem;
    display: block;
    border-top: 1px solid #e5e7eb;
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

.featured-product-select {
    max-width: 100%;
    width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.featured-product-select option {
    padding: 0.5rem;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Bannière (Thème Vibrant) */
.banner-images-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 0.75rem;
}

.banner-image-item {
    position: relative;
    width: 110px;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.25rem;
}

.banner-image-item input[type="checkbox"] {
    margin-bottom: 0.25rem;
}

.banner-image-thumb {
    width: 100%;
    aspect-ratio: 16 / 9;
    border-radius: 6px;
    overflow: hidden;
    background: #e5e7eb;
}

.banner-image-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.banner-custom-images {
    margin-top: 1rem;
}

.btn-remove-banner-image {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 22px;
    height: 22px;
    border-radius: 999px;
    border: none;
    background: rgba(15,23,42,0.85);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    cursor: pointer;
    padding: 0;
}

.btn-remove-banner-image:hover {
    background: rgba(220,38,38,0.95);
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
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur HTTP: ' + response.status);
            }
            return response.json();
        })
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
            } else if (data.error) {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de l\'upload de l\'image. Veuillez réessayer.');
        });
    }
}

// Upload d'images personnalisées pour la bannière (Thème Vibrant)
function handleBannerImageUpload(input) {
    if (!input.files || input.files.length === 0) {
        return;
    }

    const csrfToken = document.querySelector('meta[name=csrf-token]').content;

    Array.from(input.files).forEach(file => {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('_token', csrfToken);

        fetch('{{ route("merchant.products.upload-image") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur HTTP: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.url) {
                addBannerCustomImage(data.url);
            } else if (data.error) {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de l\'upload de l\'image de bannière. Veuillez réessayer.');
        });
    });

    // Permettre de réuploader le même fichier plus tard
    input.value = '';
}

function addBannerCustomImage(url) {
    const list = document.getElementById('bannerCustomImagesList');
    if (!list) return;

    const wrapper = document.createElement('div');
    wrapper.className = 'banner-image-item';
    wrapper.innerHTML = `
        <input type="hidden" name="settings[vibrant_banner_custom_images][]" value="${url}">
        <span class="banner-image-thumb">
            <img src="${url}" alt="Image bannière personnalisée">
        </span>
        <button type="button" class="btn-remove-banner-image" onclick="removeBannerCustomImage(this)">
            <i class="fas fa-times"></i>
        </button>
    `;

    list.appendChild(wrapper);
}

function removeBannerCustomImage(button) {
    const item = button.closest('.banner-image-item');
    if (item) {
        item.remove();
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

function toggleFormGroup(button) {
    const group = button.closest('.form-group-collapsible');
    const content = group.querySelector('.form-group-content');
    const arrow = button.querySelector('.form-group-arrow');
    
    const isHidden = content.style.display === 'none' || window.getComputedStyle(content).display === 'none';
    
    if (isHidden) {
        content.style.display = 'block';
        arrow.style.transform = 'rotate(180deg)';
    } else {
        content.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
    }
}

// Fonctions pour la section Nom de domaine
function copyStoreUrl() {
    const urlText = document.getElementById('currentStoreUrl').textContent;
    navigator.clipboard.writeText(urlText).then(() => {
        const btn = event.target.closest('.btn-copy-url');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i><span>Copié!</span>';
        btn.style.background = '#10b981';
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.style.background = '#0ea5e9';
        }, 2000);
    }).catch(err => {
        alert('Erreur lors de la copie');
    });
}

function updateUrlPreview() {
    const slugInput = document.getElementById('custom_store_slug');
    const previewSlug = document.getElementById('previewSlug');
    const platformDomain = document.getElementById('platform-domain').textContent;
    const previewDomain = document.getElementById('previewDomain');
    
    if (slugInput && previewSlug) {
        let slug = slugInput.value.toLowerCase().replace(/[^a-z0-9-]/g, '').replace(/-+/g, '-').replace(/^-|-$/g, '');
        slugInput.value = slug;
        previewSlug.textContent = slug || 'nom-de-votre-boutique';
        if (previewDomain) {
            previewDomain.textContent = platformDomain;
        }
    }
}

function cancelUrlCustomization() {
    const slugInput = document.getElementById('custom_store_slug');
    if (slugInput) {
        slugInput.value = '{{ $store->slug }}';
        updateUrlPreview();
    }
    // Fermer le groupe dépliable
    const group = document.querySelector('[data-section="domain"]').closest('.customize-section').querySelector('.form-group-collapsible');
    if (group) {
        const content = group.querySelector('.form-group-content');
        const arrow = group.querySelector('.form-group-arrow');
        content.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
    }
}

function saveUrlCustomization() {
    const slugInput = document.getElementById('custom_store_slug');
    const newSlug = slugInput.value.trim();
    
    if (!newSlug) {
        alert('Veuillez entrer un nom pour votre boutique');
        return;
    }
    
    if (newSlug === '{{ $store->slug }}') {
        alert('Ce nom est déjà utilisé. Veuillez en choisir un autre.');
        return;
    }
    
    // Ici, vous pouvez ajouter une requête AJAX pour sauvegarder le nouveau slug
    if (confirm('Êtes-vous sûr de vouloir changer l\'URL de votre boutique ? Cette action est irréversible.')) {
        // Soumettre le formulaire ou faire une requête AJAX
        document.getElementById('customizeForm').submit();
    }
}

function openGetDomainModal() {
    alert('Fonctionnalité "Obtenir un domaine" à venir. Cette fonctionnalité permettra d\'acheter un domaine directement depuis la plateforme.');
}

function openConnectDomainModal() {
    const form = document.getElementById('connectDomainForm');
    if (form) {
        form.style.display = 'block';
    }
}

function cancelDomainConnection() {
    const form = document.getElementById('connectDomainForm');
    if (form) {
        form.style.display = 'none';
        document.getElementById('custom_domain').value = '';
    }
}

function saveDomainConnection() {
    const domainInput = document.getElementById('custom_domain');
    const domain = domainInput.value.trim();
    
    if (!domain) {
        alert('Veuillez entrer un nom de domaine');
        return;
    }
    
    // Validation basique du domaine
    const domainPattern = /^[a-z0-9.-]+\.[a-z]{2,}$/i;
    if (!domainPattern.test(domain)) {
        alert('Format de domaine invalide. Exemple: monboutique.com');
        return;
    }
    
    // Ici, vous pouvez ajouter une requête AJAX pour sauvegarder le domaine
    if (confirm('Voulez-vous connecter le domaine ' + domain + ' à votre boutique ?')) {
        // Soumettre le formulaire ou faire une requête AJAX
        document.getElementById('customizeForm').submit();
    }
}

// Initialiser les groupes dépliables - tous fermés par défaut
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.form-group-collapsible').forEach(group => {
        const content = group.querySelector('.form-group-content');
        const arrow = group.querySelector('.form-group-arrow');
        if (content && arrow) {
            content.style.display = 'none';
            arrow.style.transform = 'rotate(0deg)';
        }
    });
    
    // Cacher le footer de la plateforme sur la page de personnalisation
    const footer = document.querySelector('.footer');
    if (footer) {
        footer.style.display = 'none';
    }
    
    // Ajuster la hauteur du sidebar pour qu'il reste fixe avec un espace en bas
    function adjustSidebarHeight() {
        const sidebar = document.querySelector('.customize-sidebar');
        if (sidebar) {
            const headerHeight = 172; // Header plateforme (72px) + Header personnalisation (~100px)
            const bottomMargin = 20; // Espace en bas pour ne pas toucher le footer
            const availableHeight = window.innerHeight - headerHeight - bottomMargin;
            
            sidebar.style.height = availableHeight + 'px';
            sidebar.style.maxHeight = availableHeight + 'px';
        }
    }
    
    // Ajuster au chargement
    adjustSidebarHeight();
    
    // Ajuster lors du redimensionnement de la fenêtre
    window.addEventListener('resize', adjustSidebarHeight);
    
    // Initialiser l'aperçu de l'URL
    updateUrlPreview();
});

// Fonctions pour la section Nom de domaine
function copyStoreUrl() {
    const urlText = document.getElementById('currentStoreUrl').textContent;
    navigator.clipboard.writeText(urlText).then(() => {
        const btn = event.target.closest('.btn-copy-url');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i><span>Copié!</span>';
        btn.style.background = '#10b981';
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.style.background = '#0ea5e9';
        }, 2000);
    }).catch(err => {
        alert('Erreur lors de la copie');
    });
}

function updateUrlPreview() {
    const slugInput = document.getElementById('custom_store_slug');
    const previewSlug = document.getElementById('previewSlug');
    const platformDomain = document.getElementById('platform-domain');
    const previewDomain = document.getElementById('previewDomain');
    
    if (slugInput && previewSlug && platformDomain) {
        let slug = slugInput.value.toLowerCase().replace(/[^a-z0-9-]/g, '').replace(/-+/g, '-').replace(/^-|-$/g, '');
        slugInput.value = slug;
        previewSlug.textContent = slug || 'nom-de-votre-boutique';
        if (previewDomain) {
            previewDomain.textContent = platformDomain.textContent;
        }
    }
}

function cancelUrlCustomization() {
    const slugInput = document.getElementById('custom_store_slug');
    if (slugInput) {
        slugInput.value = '{{ $store->slug }}';
        updateUrlPreview();
    }
    // Fermer le groupe dépliable
    const group = slugInput.closest('.form-group-collapsible');
    if (group) {
        const content = group.querySelector('.form-group-content');
        const arrow = group.querySelector('.form-group-arrow');
        if (content && arrow) {
            content.style.display = 'none';
            arrow.style.transform = 'rotate(0deg)';
        }
    }
}

function saveUrlCustomization() {
    const slugInput = document.getElementById('custom_store_slug');
    const newSlug = slugInput.value.trim();
    
    if (!newSlug) {
        alert('Veuillez entrer un nom pour votre boutique');
        return;
    }
    
    if (newSlug === '{{ $store->slug }}') {
        alert('Ce nom est déjà utilisé. Veuillez en choisir un autre.');
        return;
    }
    
    // Ici, vous pouvez ajouter une requête AJAX pour sauvegarder le nouveau slug
    if (confirm('Êtes-vous sûr de vouloir changer l\'URL de votre boutique ? Cette action est irréversible.')) {
        // Soumettre le formulaire ou faire une requête AJAX
        document.getElementById('customizeForm').submit();
    }
}

function openGetDomainModal() {
    alert('Fonctionnalité "Obtenir un domaine" à venir. Cette fonctionnalité permettra d\'acheter un domaine directement depuis la plateforme.');
}

function openConnectDomainModal() {
    const form = document.getElementById('connectDomainForm');
    if (form) {
        form.style.display = 'block';
    }
}

function cancelDomainConnection() {
    const form = document.getElementById('connectDomainForm');
    if (form) {
        form.style.display = 'none';
        const domainInput = document.getElementById('custom_domain');
        if (domainInput) {
            domainInput.value = '';
        }
    }
}

function saveDomainConnection() {
    const domainInput = document.getElementById('custom_domain');
    const domain = domainInput.value.trim();
    
    if (!domain) {
        alert('Veuillez entrer un nom de domaine');
        return;
    }
    
    // Validation basique du domaine
    const domainPattern = /^[a-z0-9.-]+\.[a-z]{2,}$/i;
    if (!domainPattern.test(domain)) {
        alert('Format de domaine invalide. Exemple: monboutique.com');
        return;
    }
    
    // Ici, vous pouvez ajouter une requête AJAX pour sauvegarder le domaine
    if (confirm('Voulez-vous connecter le domaine ' + domain + ' à votre boutique ?')) {
        // Soumettre le formulaire ou faire une requête AJAX
        document.getElementById('customizeForm').submit();
    }
}

// Mettre à jour la prévisualisation automatiquement
document.addEventListener('DOMContentLoaded', function() {
    // Écouter tous les changements de couleurs, thèmes, animations, etc.
    document.querySelectorAll('.color-picker, input[name="template_id"], select[name*="animation"], select[name*="font"], input[name*="color_text"], input[name*="button_text"], input[name*="banner_button_text"]').forEach(element => {
        element.addEventListener('change', function() {
            if (this.classList.contains('color-picker')) {
                // Mettre à jour l'input texte correspondant
                const textInput = document.getElementById(this.id + '_text');
                if (textInput) {
                    textInput.value = this.value;
                }
                // Marquer comme modifié manuellement pour éviter l'écrasement automatique
                if (this.id.includes('banner_button_bg') || this.id.includes('header_name')) {
                    this.dataset.manual = 'true';
                }
                updateColorPreview();
            }
            if (this.name === 'template_id') {
                updatePreview();
            }
            // Mettre à jour la prévisualisation si elle est ouverte
            const modal = document.getElementById('previewModal');
            if (modal && modal.style.display === 'flex') {
                setTimeout(updatePreviewIframe, 300);
            }
        });
    });
    
    // Marquer les champs comme non modifiés manuellement au départ
    const bannerButtonBg = document.getElementById('banner_button_bg_color');
    if (bannerButtonBg) {
        bannerButtonBg.dataset.manual = 'false';
    }
    const headerNameColor = document.getElementById('header_name_color');
    if (headerNameColor) {
        headerNameColor.dataset.manual = 'false';
    }
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
    // Mettre à jour les couleurs principales
    document.getElementById('primary_color').value = primary;
    document.getElementById('primary_color_text').value = primary;
    document.getElementById('secondary_color').value = secondary;
    document.getElementById('secondary_color_text').value = secondary;
    document.getElementById('accent_color').value = accent;
    document.getElementById('accent_color_text').value = accent;
    
    // Mettre à jour automatiquement les couleurs du header
    // Fond header : utiliser la couleur secondaire ou blanc selon la palette
    const headerBg = secondary === '#ffffff' ? '#f9fafb' : '#ffffff';
    if (document.getElementById('header_bg_color')) {
        document.getElementById('header_bg_color').value = headerBg;
        document.getElementById('header_bg_color_text').value = headerBg;
    }
    
    // Texte header (menus) : utiliser la couleur principale pour un meilleur contraste
    // Si la couleur principale est très claire, utiliser une couleur sombre
    const headerText = (primary === '#ffffff' || primary === '#f9fafb' || primary === '#f3f4f6') ? '#1f2937' : primary;
    if (document.getElementById('header_text_color')) {
        document.getElementById('header_text_color').value = headerText;
        document.getElementById('header_text_color_text').value = headerText;
    }
    
    // Nom boutique header : utiliser la couleur principale
    if (document.getElementById('header_name_color')) {
        document.getElementById('header_name_color').value = primary;
        document.getElementById('header_name_color_text').value = primary;
    }

    // Couleur hover des menus : utiliser la couleur principale (ou accent)
    if (document.getElementById('header_nav_hover_color')) {
        document.getElementById('header_nav_hover_color').value = primary;
        document.getElementById('header_nav_hover_color_text').value = primary;
    }
    
    // Bouton bannière : utiliser la couleur principale
    if (document.getElementById('banner_button_bg_color')) {
        document.getElementById('banner_button_bg_color').value = primary;
        document.getElementById('banner_button_bg_color_text').value = primary;
    }
    
    // Texte bouton bannière : blanc par défaut
    if (document.getElementById('banner_button_text_color')) {
        document.getElementById('banner_button_text_color').value = '#ffffff';
        document.getElementById('banner_button_text_color_text').value = '#ffffff';
    }
    
    // Titre bannière : blanc par défaut
    if (document.getElementById('banner_title_color')) {
        document.getElementById('banner_title_color').value = '#ffffff';
        document.getElementById('banner_title_color_text').value = '#ffffff';
    }
    
    // Footer : utiliser la couleur secondaire ou une couleur sombre selon la palette
    let footerBg, footerText, footerLink, footerTitle;
    
    // Si la couleur secondaire est claire (blanc ou très clair), utiliser une couleur sombre pour le footer
    if (secondary === '#ffffff' || secondary === '#f9fafb' || secondary === '#f3f4f6') {
        footerBg = secondary === '#ffffff' ? '#1f2937' : '#374151';
        footerText = '#9ca3af';
        footerLink = '#ffffff';
        footerTitle = '#ffffff';
    } else {
        // Si la couleur secondaire est sombre, l'utiliser pour le footer
        footerBg = secondary;
        footerText = '#e5e7eb';
        footerLink = '#ffffff';
        footerTitle = '#ffffff';
    }
    
    if (document.getElementById('footer_bg_color')) {
        document.getElementById('footer_bg_color').value = footerBg;
        document.getElementById('footer_bg_color_text').value = footerBg;
    }
    if (document.getElementById('footer_text_color')) {
        document.getElementById('footer_text_color').value = footerText;
        document.getElementById('footer_text_color_text').value = footerText;
    }
    if (document.getElementById('footer_link_color')) {
        document.getElementById('footer_link_color').value = footerLink;
        document.getElementById('footer_link_color_text').value = footerLink;
    }
    if (document.getElementById('footer_title_color')) {
        document.getElementById('footer_title_color').value = footerTitle;
        document.getElementById('footer_title_color_text').value = footerTitle;
    }

    // Fond de page de la boutique
    if (document.getElementById('page_bg_color')) {
        let pageBg;
        if (secondary === '#ffffff' || secondary === '#f9fafb' || secondary === '#f3f4f6') {
            pageBg = '#f9fafb';
        } else {
            pageBg = '#f3f4f6';
        }
        document.getElementById('page_bg_color').value = pageBg;
        document.getElementById('page_bg_color_text').value = pageBg;
    }
    
    // Bouton d'action : utiliser la couleur principale
    if (document.getElementById('button_bg_color')) {
        document.getElementById('button_bg_color').value = primary;
        document.getElementById('button_bg_color_text').value = primary;
    }
    
    if (document.getElementById('button_text_color')) {
        document.getElementById('button_text_color').value = '#ffffff';
        document.getElementById('button_text_color_text').value = '#ffffff';
    }
    
    // Réinitialiser les flags de modification manuelle pour permettre la mise à jour automatique
    const bannerButtonBg = document.getElementById('banner_button_bg_color');
    if (bannerButtonBg) {
        bannerButtonBg.dataset.manual = 'false';
    }
    const headerNameColor = document.getElementById('header_name_color');
    if (headerNameColor) {
        headerNameColor.dataset.manual = 'false';
    }
    
    // Mettre à jour l'aperçu avec toutes les nouvelles couleurs
    updateColorPreview();
    
    // Mettre à jour la prévisualisation si elle est ouverte
    const modal = document.getElementById('previewModal');
    if (modal && modal.style.display === 'flex') {
        setTimeout(updatePreviewIframe, 300);
    }
}

function updateColorPreview() {
    const primary = document.getElementById('primary_color').value;
    const secondary = document.getElementById('secondary_color').value;
    const accent = document.getElementById('accent_color').value;
    
    // Mettre à jour les inputs texte
    if (document.getElementById('primary_color_text')) {
        document.getElementById('primary_color_text').value = primary;
    }
    if (document.getElementById('secondary_color_text')) {
        document.getElementById('secondary_color_text').value = secondary;
    }
    if (document.getElementById('accent_color_text')) {
        document.getElementById('accent_color_text').value = accent;
    }
    
    // Mettre à jour l'aperçu de la bannière
    const previewHeader = document.getElementById('colorPreviewHeader');
    if (previewHeader) {
        previewHeader.style.background = `linear-gradient(135deg, ${primary}, ${secondary})`;
    }
    
    // Mettre à jour l'aperçu de la bannière mini
    const previewBannerMini = document.getElementById('previewBannerMini');
    if (previewBannerMini) {
        previewBannerMini.style.background = `linear-gradient(135deg, ${primary}, ${secondary})`;
    }
    
    // Mettre à jour l'aperçu du header
    const headerBgColor = document.getElementById('header_bg_color') ? document.getElementById('header_bg_color').value : '#ffffff';
    const headerTextColor = document.getElementById('header_text_color') ? document.getElementById('header_text_color').value : '#4b5563';
    const headerNameColor = document.getElementById('header_name_color') ? document.getElementById('header_name_color').value : primary;
    
    const previewHeaderMini = document.getElementById('previewHeaderMini');
    if (previewHeaderMini) {
        previewHeaderMini.style.background = headerBgColor;
    }
    const previewHeaderBrand = document.getElementById('previewHeaderBrand');
    if (previewHeaderBrand) {
        previewHeaderBrand.style.color = headerNameColor;
    }
    const previewHeaderNav = document.getElementById('previewHeaderNav');
    if (previewHeaderNav) {
        previewHeaderNav.querySelectorAll('span').forEach(span => {
            span.style.color = headerTextColor;
        });
    }
    
    // Mettre à jour l'aperçu du bouton bannière
    const bannerButtonBgColor = document.getElementById('banner_button_bg_color') ? document.getElementById('banner_button_bg_color').value : primary;
    const bannerButtonTextColor = document.getElementById('banner_button_text_color') ? document.getElementById('banner_button_text_color').value : '#ffffff';
    const previewBannerButton = document.getElementById('previewBannerButton');
    if (previewBannerButton) {
        previewBannerButton.style.background = bannerButtonBgColor;
        previewBannerButton.style.color = bannerButtonTextColor;
    }
    
    // Mettre à jour l'aperçu du footer
    const footerBgColor = document.getElementById('footer_bg_color') ? document.getElementById('footer_bg_color').value : '#1f2937';
    const footerTextColor = document.getElementById('footer_text_color') ? document.getElementById('footer_text_color').value : '#9ca3af';
    const previewFooterMini = document.getElementById('previewFooterMini');
    if (previewFooterMini) {
        previewFooterMini.style.background = footerBgColor;
        previewFooterMini.style.color = footerTextColor;
    }
    
    // Mettre à jour automatiquement les couleurs dérivées si elles n'ont pas été modifiées manuellement
    // Bouton bannière : utiliser la couleur principale si pas encore définie
    const bannerButtonBg = document.getElementById('banner_button_bg_color');
    if (bannerButtonBg && (!bannerButtonBg.dataset.manual || bannerButtonBg.dataset.manual === 'false')) {
        bannerButtonBg.value = primary;
        if (document.getElementById('banner_button_bg_color_text')) {
            document.getElementById('banner_button_bg_color_text').value = primary;
        }
    }
    
    // Nom boutique header : utiliser la couleur principale si pas encore définie
    const headerNameColorEl = document.getElementById('header_name_color');
    if (headerNameColorEl && (!headerNameColorEl.dataset.manual || headerNameColorEl.dataset.manual === 'false')) {
        headerNameColorEl.value = primary;
        if (document.getElementById('header_name_color_text')) {
            document.getElementById('header_name_color_text').value = primary;
        }
    }
    
    // Mettre à jour la prévisualisation si elle est ouverte
    const modal = document.getElementById('previewModal');
    if (modal && modal.style.display === 'flex') {
        setTimeout(updatePreviewIframe, 300);
    }
}

function addBannerTextRow() {
    const container = document.getElementById('bannerTextsContainer');
    if (!container) return;

    // Calculer le prochain index disponible
    let maxIndex = 0;
    container.querySelectorAll('.banner-text-row').forEach(row => {
        const idx = parseInt(row.getAttribute('data-index')) || 0;
        if (idx > maxIndex) maxIndex = idx;
    });
    const nextIndex = maxIndex + 1;

    const effectsOptions = `
        <option value="scroll">Défilement vertical</option>
        <option value="typewriter">Machine à écrire</option>
        <option value="fade">Fondu</option>
        <option value="bounce">Rebond</option>
        <option value="slide">Glissement</option>
    `;

    const row = document.createElement('div');
    row.className = 'banner-text-row';
    row.setAttribute('data-index', String(nextIndex));
    row.style.display = 'flex';
    row.style.gap = '0.5rem';
    row.style.marginBottom = '0.5rem';
    row.innerHTML = `
        <input type="text"
               name="settings[banner_texts][${nextIndex}][text]"
               class="form-input"
               placeholder="Texte de bannière ${nextIndex}"
               style="flex: 1 1 auto;">
        <select name="settings[banner_texts][${nextIndex}][effect]"
                class="form-select"
                style="width: 180px;">
            ${effectsOptions}
        </select>
    `;

    container.appendChild(row);
}

function updateColorFromText(colorId, value) {
    // Valider le format hex
    if (/^#[0-9A-F]{6}$/i.test(value)) {
        document.getElementById(colorId).value = value;
        // Mettre à jour l'input texte correspondant
        const textInput = document.getElementById(colorId + '_text');
        if (textInput) {
            textInput.value = value;
        }
        updateColorPreview();
        // Mettre à jour la prévisualisation si elle est ouverte
        const modal = document.getElementById('previewModal');
        if (modal && modal.style.display === 'flex') {
            setTimeout(updatePreviewIframe, 300);
        }
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
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-top: 1.5rem;
}

@media (max-width: 1400px) {
    .templates-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 1000px) {
    .templates-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .templates-grid {
        grid-template-columns: 1fr;
    }
    
    .customize-layout {
        grid-template-columns: 1fr;
        padding: 1.5rem;
        gap: 1.5rem;
    }
    
    .customize-sidebar {
        position: relative;
        top: 0;
        order: 2;
    }
    
    .customize-main {
        order: 1;
    }
    
    .header-content {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
        padding: 0 1.5rem;
    }
    
    .header-actions {
        width: 100%;
        flex-direction: column;
    }
    
    .btn-preview,
    .btn-open-new-tab {
        width: 100%;
        justify-content: center;
    }
    
    .customize-title {
        font-size: 1.5rem;
    }
}

.template-card {
    position: relative;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    cursor: pointer;
    transition: border-color 0.2s;
    background: white;
}

.template-card:hover {
    border-color: #0ea5e9;
}

.template-card.selected {
    border-color: #0ea5e9;
    background: #eff6ff;
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
    aspect-ratio: 16 / 10;
    display: flex;
    flex-direction: column;
}

.template-preview-header {
    height: 50px;
    padding: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.template-preview-dots {
    display: flex;
    gap: 0.375rem;
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
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.template-preview-bar {
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
}

.template-preview-bar.short {
    width: 65%;
}

.template-info {
    text-align: center;
    padding-top: 0.5rem;
}

.template-name {
    font-weight: 600;
    font-size: 1rem;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.template-card.selected .template-name {
    color: #0ea5e9;
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

.preview-card-body {
    padding: 1rem;
    background: #f9fafb;
}

.preview-header-mini {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    margin-bottom: 0.5rem;
    background: #ffffff;
}

.preview-header-brand {
    font-weight: 600;
    font-size: 0.875rem;
}

.preview-header-nav {
    display: flex;
    gap: 0.75rem;
    font-size: 0.75rem;
}

.preview-header-nav span {
    color: #4b5563;
}

.preview-banner-mini {
    padding: 1.5rem;
    border-radius: 6px;
    margin-bottom: 0.5rem;
    text-align: center;
    background: linear-gradient(135deg, #0ea5e9, #a855f7);
    min-height: 80px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}

.preview-banner-title {
    color: white;
    font-weight: 700;
    font-size: 1rem;
}

.preview-banner-button {
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}

.preview-footer-mini {
    padding: 0.75rem 1rem;
    border-radius: 6px;
    background: #1f2937;
    color: white;
    text-align: center;
    font-size: 0.75rem;
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
    padding: 0.875rem 1.75rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.95rem;
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid #e5e7eb;
    background: white;
    color: #374151;
    text-decoration: none;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    position: relative;
    overflow: hidden;
}

.btn-secondary::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 0;
    height: 100%;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    transition: width 0.3s;
    z-index: 0;
}

.btn-secondary:hover::before {
    width: 100%;
}

.btn-secondary:hover {
    background: #f8fafc;
    border-color: #0ea5e9;
    color: #0ea5e9;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

.btn-secondary span,
.btn-secondary i {
    position: relative;
    z-index: 1;
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

/* Section Nom de domaine */
.current-url-display {
    margin-top: 0.5rem;
}

.url-display-box {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}

.url-text {
    flex: 1;
    font-family: 'Courier New', monospace;
    font-size: 0.95rem;
    color: #1f2937;
    word-break: break-all;
}

.btn-copy-url {
    padding: 0.5rem 1rem;
    background: #0ea5e9;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: background-color 0.2s;
    white-space: nowrap;
}

.btn-copy-url:hover {
    background: #0284c7;
}

.custom-url-warning {
    display: flex;
    gap: 0.75rem;
    padding: 1rem;
    background: #fef3c7;
    border: 1px solid #fbbf24;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.custom-url-warning i {
    color: #f59e0b;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.custom-url-warning p {
    margin: 0;
    color: #92400e;
    font-size: 0.9rem;
    line-height: 1.5;
}

.custom-url-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.url-input-wrapper {
    display: flex;
    align-items: center;
    gap: 0;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    overflow: hidden;
    background: white;
}

.url-input {
    flex: 1;
    border: none;
    border-radius: 0;
    padding: 0.75rem 1rem;
}

.url-input:focus {
    outline: none;
    box-shadow: none;
}

.url-domain-suffix {
    padding: 0.75rem 1rem;
    background: #f3f4f6;
    color: #6b7280;
    font-size: 0.95rem;
    border-left: 1px solid #e5e7eb;
    white-space: nowrap;
}

.url-preview-box {
    padding: 1rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.url-preview-label {
    font-weight: 600;
    color: #374151;
    font-size: 0.9rem;
}

.url-preview-text {
    font-family: 'Courier New', monospace;
    color: #0ea5e9;
    font-size: 0.95rem;
}

.url-form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 1rem;
}

.custom-domain-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.info-card {
    display: flex;
    gap: 1rem;
    padding: 1.5rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}

.info-card i {
    font-size: 1.5rem;
    color: #0ea5e9;
    flex-shrink: 0;
}

.info-card h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
}

.info-card p {
    margin: 0;
    font-size: 0.875rem;
    color: #6b7280;
    line-height: 1.5;
}

.current-domain-display {
    margin-bottom: 2rem;
}

.domain-display-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background: #f0fdf4;
    border: 1px solid #86efac;
    border-radius: 8px;
    margin-top: 0.5rem;
}

.domain-text {
    font-family: 'Courier New', monospace;
    font-size: 1rem;
    color: #1f2937;
    font-weight: 500;
}

.domain-status {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
}

.domain-status.active {
    background: #d1fae5;
    color: #065f46;
}

.custom-domain-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.domain-option-card {
    padding: 2rem;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    transition: border-color 0.2s;
}

.domain-option-card:hover {
    border-color: #0ea5e9;
}

.domain-option-header {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.domain-option-header i {
    font-size: 2rem;
    color: #0ea5e9;
}

.domain-option-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
}

.domain-option-card p {
    margin: 0;
    color: #6b7280;
    line-height: 1.6;
}

.btn-domain-action {
    padding: 0.875rem 1.5rem;
    background: #0ea5e9;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-weight: 600;
    transition: background-color 0.2s;
    margin-top: auto;
}

.btn-domain-action:hover {
    background: #0284c7;
}

.custom-domain-form {
    padding: 1.5rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-top: 1.5rem;
}

.domain-form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 1.5rem;
}

/* Section Gestion et opération */
.telegram-setup {
    margin-top: 1rem;
}

.telegram-info-box {
    padding: 1.5rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-top: 1rem;
}

.telegram-info-box h4 {
    margin: 0 0 0.75rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
}

.telegram-info-box p {
    margin: 0 0 0.5rem 0;
    color: #6b7280;
    font-size: 0.9rem;
}

.telegram-info-box ol {
    color: #374151;
    font-size: 0.9rem;
    line-height: 1.8;
}

.telegram-qr-section {
    display: flex;
    gap: 2rem;
    align-items: flex-start;
    margin-top: 1.5rem;
    flex-wrap: wrap;
}

.qr-code-container {
    flex: 0 0 auto;
}

.qr-code-placeholder {
    width: 200px;
    height: 200px;
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background: white;
    text-align: center;
    padding: 1rem;
}

.qr-code-placeholder p {
    margin: 0;
    font-size: 0.875rem;
    color: #6b7280;
}

.telegram-link-section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    flex: 1;
    min-width: 200px;
}

.btn-telegram-link {
    padding: 0.875rem 1.5rem;
    background: #0088cc;
    color: white;
    border: none;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-weight: 600;
    transition: background-color 0.2s;
    cursor: pointer;
}

.btn-telegram-link:hover {
    background: #006ba3;
}

.btn-refresh-qr {
    padding: 0.75rem 1.5rem;
    background: white;
    color: #374151;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-refresh-qr:hover {
    background: #f9fafb;
    border-color: #9ca3af;
}

/* SEO Preview */
.seo-preview-box {
    margin-bottom: 2rem;
}

.seo-preview-card {
    padding: 1rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    max-width: 600px;
}

.seo-preview-url {
    font-size: 0.875rem;
    color: #0ea5e9;
    margin-bottom: 0.25rem;
    font-family: 'Courier New', monospace;
}

.seo-preview-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a0dab;
    margin-bottom: 0.25rem;
    line-height: 1.3;
}

.seo-preview-title:hover {
    text-decoration: underline;
}

.seo-preview-description {
    font-size: 0.875rem;
    color: #545454;
    line-height: 1.5;
}

.char-counter {
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #6b7280;
    text-align: right;
}

.char-counter span {
    font-weight: 600;
}

.og-image-upload {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.og-image-upload .form-input {
    flex: 1;
}

.og-image-preview {
    padding: 1rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}

.og-image-preview img {
    display: block;
    margin: 0 auto;
}

/* Section Gestion du compte */
.profile-tabs {
    display: flex;
    gap: 0;
    border-bottom: 2px solid #e5e7eb;
    margin-bottom: 2rem;
}

.profile-tab {
    padding: 1rem 1.5rem;
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    color: #6b7280;
    transition: all 0.2s;
    margin-bottom: -2px;
}

.profile-tab:hover {
    color: #374151;
    background: #f9fafb;
}

.profile-tab.active {
    color: #0ea5e9;
    border-bottom-color: #0ea5e9;
    font-weight: 600;
}

.profile-tab-content {
    display: none;
}

.profile-tab-content.active {
    display: block;
}

.profile-image-section {
    margin-bottom: 1.5rem;
}

.profile-image-upload {
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
    margin-top: 0.5rem;
}

.profile-image-preview {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid #e5e7eb;
    flex-shrink: 0;
}

.profile-image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-image-placeholder {
    width: 100%;
    height: 100%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 3rem;
}

.profile-image-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    flex: 1;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
}

.current-email-display {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-top: 0.5rem;
}

.email-text {
    font-family: 'Courier New', monospace;
    font-size: 0.95rem;
    color: #1f2937;
}

.email-status {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
}

.email-status.verified {
    background: #d1fae5;
    color: #065f46;
}

.email-status.unverified {
    background: #fef3c7;
    color: #92400e;
}

.email-change-pending {
    margin-top: 1.5rem;
    padding: 1.5rem;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
}

.alert-info {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.alert-info i {
    color: #0ea5e9;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.alert-info strong {
    display: block;
    margin-bottom: 0.5rem;
    color: #1f2937;
}

.email-change-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.form-actions-inline {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

/* Équipe et collaborateurs */
.team-members-list {
    margin-bottom: 2rem;
}

.team-member-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.team-member-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 1;
}

.team-member-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.team-member-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
}

.team-member-details {
    flex: 1;
}

.member-name {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.member-email {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.member-meta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.member-role,
.member-status,
.member-date {
    font-size: 0.875rem;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
}

.member-role.owner {
    background: #fef3c7;
    color: #92400e;
    font-weight: 600;
}

.member-status.active {
    background: #d1fae5;
    color: #065f46;
}

.member-date {
    color: #6b7280;
    background: #f9fafb;
}

.team-member-actions {
    position: relative;
}

.dropdown-menu {
    position: relative;
}

.dropdown-toggle {
    padding: 0.5rem;
    background: transparent;
    border: none;
    cursor: pointer;
    color: #6b7280;
    border-radius: 6px;
    transition: background-color 0.2s;
}

.dropdown-toggle:hover {
    background: #f3f4f6;
}

.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    min-width: 180px;
    z-index: 100;
    margin-top: 0.5rem;
}

.dropdown-content.show {
    display: block;
}

.dropdown-content a {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    color: #374151;
    text-decoration: none;
    transition: background-color 0.2s;
}

.dropdown-content a:hover {
    background: #f9fafb;
}

.dropdown-content a i {
    width: 20px;
    color: #6b7280;
}

.invite-section {
    background: #f9fafb;
    padding: 1.5rem;
    border-radius: 8px;
}

/* Facturation */
.current-plan-card {
    padding: 2rem;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
}

.plan-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.plan-name-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #0ea5e9, #8b5cf6);
    color: white;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
}

.plan-name-badge.free {
    background: linear-gradient(135deg, #6b7280, #9ca3af);
}

.plan-status {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
}

.plan-status.active {
    background: #d1fae5;
    color: #065f46;
}

.plan-details {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.plan-detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.plan-detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    color: #6b7280;
    font-size: 0.9rem;
}

.detail-value {
    color: #1f2937;
    font-weight: 600;
}

.plan-features h4 {
    margin: 0 0 1rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
}

.features-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 0.75rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #f9fafb;
    border-radius: 6px;
}

.feature-item i {
    color: #10b981;
    font-size: 1rem;
}

.feature-item span {
    color: #374151;
    font-size: 0.9rem;
}

.plan-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.plan-actions .btn-primary,
.plan-actions .btn-secondary {
    flex: 1;
    min-width: 200px;
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

<script>
function toggleMultipleProductsMode(enabled) {
    const featuredProductGroup = document.getElementById('featuredProductGroup');
    const multipleProductsHint = document.getElementById('multipleProductsHint');
    
    if (enabled) {
        // Mode multi-produits activé : masquer la sélection du produit
        if (featuredProductGroup) {
            featuredProductGroup.style.display = 'none';
        }
        if (multipleProductsHint) {
            multipleProductsHint.style.display = 'block';
        }
    } else {
        // Mode monoproduit : afficher la sélection du produit
        if (featuredProductGroup) {
            featuredProductGroup.style.display = 'block';
        }
        if (multipleProductsHint) {
            multipleProductsHint.style.display = 'none';
        }
    }
}

// Fonctions pour la section Gestion et opération
function toggleTelegramSetup() {
    const checkbox = document.getElementById('telegram_alerts_enabled');
    const setupContent = document.getElementById('telegramSetupContent');
    
    if (checkbox && setupContent) {
        if (checkbox.checked) {
            setupContent.style.display = 'block';
            refreshTelegramQR();
        } else {
            setupContent.style.display = 'none';
        }
    }
}

function refreshTelegramQR() {
    // Ici, vous pouvez ajouter une requête pour générer un nouveau QR code
    const qrContainer = document.getElementById('telegramQRCode');
    if (qrContainer) {
        // Pour l'instant, on affiche juste un message
        // Dans une vraie implémentation, vous feriez une requête AJAX pour générer le QR code
        qrContainer.innerHTML = `
            <i class="fas fa-qrcode" style="font-size: 3rem; color: #0ea5e9;"></i>
            <p>QR Code Telegram</p>
            <p style="font-size: 0.875rem; color: #6b7280;">Génération du QR code en cours...</p>
        `;
        
        // Simuler la génération du QR code
        setTimeout(() => {
            qrContainer.innerHTML = `
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=https://t.me/VotreBotDropshipping?start={{ $store->id }}" alt="QR Code Telegram" style="width: 200px; height: 200px;">
                <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.5rem;">Scannez avec Telegram</p>
            `;
        }, 1000);
    }
}

function updateSEOPreview() {
    const titleInput = document.getElementById('seo_title');
    const descriptionInput = document.getElementById('seo_description');
    const previewTitle = document.getElementById('seoPreviewTitle');
    const previewDescription = document.getElementById('seoPreviewDescription');
    const titleCounter = document.getElementById('seoTitleCounter');
    const descriptionCounter = document.getElementById('seoDescriptionCounter');
    
    if (titleInput && previewTitle) {
        const title = titleInput.value || '{{ $store->name }} - Boutique en ligne';
        previewTitle.textContent = title;
        if (titleCounter) {
            titleCounter.textContent = title.length;
            titleCounter.style.color = title.length > 60 ? '#ef4444' : title.length < 50 ? '#f59e0b' : '#10b981';
        }
    }
    
    if (descriptionInput && previewDescription) {
        const description = descriptionInput.value || '{{ Str::limit($store->description ?? "Découvrez nos produits de qualité", 160) }}';
        previewDescription.textContent = description;
        if (descriptionCounter) {
            descriptionCounter.textContent = description.length;
            descriptionCounter.style.color = description.length > 160 ? '#ef4444' : description.length < 150 ? '#f59e0b' : '#10b981';
        }
    }
}

function handleOGImageUpload(input) {
    if (input.files && input.files[0]) {
        const formData = new FormData();
        formData.append('image', input.files[0]);
        formData.append('_token', document.querySelector('meta[name=csrf-token]').content);

        fetch('{{ route("merchant.products.upload-image") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur HTTP: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.url) {
                document.getElementById('seo_og_image').value = data.url;
                const preview = document.getElementById('og_image_preview');
                preview.innerHTML = `<img src="${data.url}" alt="Aperçu OG Image" style="max-width: 100%; max-height: 200px; border-radius: 8px; border: 1px solid #e5e7eb;">`;
                preview.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de l\'upload de l\'image');
        });
    }
}

// Initialiser les compteurs SEO au chargement
document.addEventListener('DOMContentLoaded', function() {
    updateSEOPreview();
});

// Fonctions pour la section Gestion du compte
function switchProfileTab(tabName) {
    // Masquer tous les onglets
    document.querySelectorAll('.profile-tab-content').forEach(content => {
        content.classList.remove('active');
    });
    document.querySelectorAll('.profile-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Afficher l'onglet sélectionné
    const targetContent = document.getElementById('profileTab' + tabName.charAt(0).toUpperCase() + tabName.slice(1));
    const targetTab = event.target.closest('.profile-tab');
    
    if (targetContent) {
        targetContent.classList.add('active');
    }
    if (targetTab) {
        targetTab.classList.add('active');
    }
}

function handleProfileImageUpload(input) {
    if (input.files && input.files[0]) {
        const formData = new FormData();
        formData.append('image', input.files[0]);
        formData.append('_token', document.querySelector('meta[name=csrf-token]').content);

        fetch('{{ route("merchant.products.upload-image") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur HTTP: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.url) {
                document.getElementById('profile_image').value = data.url;
                const preview = document.getElementById('profileImagePreview');
                preview.innerHTML = `<img src="${data.url}" alt="Photo de profil">`;
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de l\'upload de l\'image');
        });
    }
}

function removeProfileImage() {
    if (confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?')) {
        document.getElementById('profile_image').value = '';
        const preview = document.getElementById('profileImagePreview');
        preview.innerHTML = `
            <div class="profile-image-placeholder">
                <i class="fas fa-user"></i>
            </div>
        `;
    }
}

function updateProfileInfo() {
    const phone = document.getElementById('user_phone');
    const profileImage = document.getElementById('profile_image');
    
    if (!phone || !profileImage) return;
    
    // Ici, vous pouvez ajouter une requête AJAX pour sauvegarder
    if (confirm('Voulez-vous mettre à jour vos informations personnelles ?')) {
        document.getElementById('customizeForm').submit();
    }
}

function requestEmailChange() {
    const newEmail = document.getElementById('new_email');
    if (!newEmail) return;
    
    const email = newEmail.value;
    
    if (!email) {
        alert('Veuillez entrer un nouvel email');
        return;
    }
    
    if (confirm('Un email de confirmation sera envoyé à ' + email + '. Voulez-vous continuer ?')) {
        // Ici, vous pouvez ajouter une requête AJAX pour demander le changement d'email
        document.getElementById('customizeForm').submit();
    }
}

function resendEmailConfirmation() {
    if (confirm('Renvoyer l\'email de confirmation ?')) {
        // Ici, vous pouvez ajouter une requête AJAX pour renvoyer l'email
        alert('Email de confirmation renvoyé !');
    }
}

function cancelEmailChange() {
    if (confirm('Êtes-vous sûr de vouloir annuler la demande de changement d\'email ?')) {
        // Ici, vous pouvez ajouter une requête AJAX pour annuler
        location.reload();
    }
}

function updateLanguage() {
    // La langue peut être mise à jour automatiquement ou avec un bouton
}

function saveLanguage() {
    const languageInput = document.getElementById('user_language');
    if (!languageInput) return;
    
    const language = languageInput.value;
    
    if (confirm('Voulez-vous changer la langue de l\'interface ?')) {
        // Ici, vous pouvez ajouter une requête AJAX pour sauvegarder la langue
        document.getElementById('customizeForm').submit();
    }
}

function toggleDropdown(button) {
    const dropdown = button.nextElementSibling;
    const isOpen = dropdown.classList.contains('show');
    
    // Fermer tous les autres dropdowns
    document.querySelectorAll('.dropdown-content').forEach(d => {
        d.classList.remove('show');
    });
    
    // Toggle le dropdown actuel
    if (!isOpen) {
        dropdown.classList.add('show');
    }
}

// Fermer les dropdowns en cliquant ailleurs
document.addEventListener('click', function(event) {
    if (!event.target.closest('.dropdown-menu')) {
        document.querySelectorAll('.dropdown-content').forEach(d => {
            d.classList.remove('show');
        });
    }
});

function viewMember(memberId) {
    alert('Fonctionnalité "Voir le membre" à venir. ID: ' + memberId);
}

function editMember(memberId) {
    alert('Fonctionnalité "Modifier le membre" à venir. ID: ' + memberId);
}

function sendInvitation() {
    const emailInput = document.getElementById('invite_email');
    const roleInput = document.getElementById('invite_role');
    
    if (!emailInput || !roleInput) return;
    
    const email = emailInput.value;
    const role = roleInput.value;
    
    if (!email) {
        alert('Veuillez entrer un email');
        return;
    }
    
    if (confirm('Envoyer une invitation à ' + email + ' avec le rôle ' + role + ' ?')) {
        // Ici, vous pouvez ajouter une requête AJAX pour envoyer l'invitation
        alert('Invitation envoyée !');
        emailInput.value = '';
    }
}

function managePayment() {
    alert('Fonctionnalité "Gérer les informations de paiement" à venir.');
}
</script>
@endsection
