<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Boutique Dropshipping')</title>
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Century Gothic alternative - Utilisation de polices système similaires -->
    <style>
        /* Century Gothic n'est pas disponible sur Google Fonts, utilisation de polices système */
        @font-face {
            font-family: 'Century Gothic';
            font-style: normal;
            font-weight: 400;
            src: local('Century Gothic'), local('CenturyGothic'), local('AppleGothic');
            font-display: swap;
        }
        @font-face {
            font-family: 'Century Gothic';
            font-style: normal;
            font-weight: 700;
            src: local('Century Gothic Bold'), local('CenturyGothic-Bold');
            font-display: swap;
        }
    </style>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
</head>
@php
    // Définir $isPublicStore avant l'utilisation dans le body
    $isPublicStore = request()->is('store/*');
@endphp
<body class="page" @if(request()->routeIs('merchant.store.create') && session('success')) data-show-success-toast="true" @endif>
    @if(!$isPublicStore)
    <header class="topbar">
        <div class="container topbar-inner">
            <a href="{{ url('/') }}" class="brand">
                <i class="fas fa-store brand-icon-fa"></i>
                <span class="brand-text">Dropshipping</span>
            </a>
            <nav class="nav">
                @php
                    $isHomePage = request()->is('/');
                    $isStorePage = request()->is('store/*'); // Pages de boutique publique uniquement
                    $isPublicStore = request()->is('store/*'); // Détecter si on est sur une boutique publique
                    $hasStore = auth()->check() && isset(auth()->user()->store) ? auth()->user()->store : null;
                @endphp

                {{-- Menu pour page d'accueil publique --}}
                @if($isHomePage)
                    {{-- Menu déroulant "Nos produits" --}}
                    <div class="nav-dropdown">
                        <a href="#" class="nav-link nav-link-dropdown">
                            <i class="fas fa-cube"></i>
                            <span>Nos produits</span>
                            <i class="fas fa-chevron-down dropdown-icon"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a href="{{ route('merchant.store.create') }}" class="dropdown-item">
                                <i class="fas fa-store"></i>
                                <div>
                                    <div class="dropdown-item-title">Créer votre boutique</div>
                                    <div class="dropdown-item-desc">Créez votre boutique en ligne en quelques clics</div>
                                </div>
                            </a>
                            <div class="dropdown-item dropdown-item-parent">
                                <i class="fas fa-globe"></i>
                                <div>
                                    <div class="dropdown-item-title">Vendez partout</div>
                                    <div class="dropdown-item-desc">Multi-canal de vente</div>
                                </div>
                                <i class="fas fa-chevron-right dropdown-arrow"></i>
                                <div class="dropdown-submenu">
                                    <a href="#" class="dropdown-subitem">
                                        <i class="fas fa-laptop"></i>
                                        <span>En ligne</span>
                                    </a>
                                    <a href="#" class="dropdown-subitem">
                                        <i class="fas fa-cash-register"></i>
                                        <span>Point de vente</span>
                                    </a>
                                    <a href="#" class="dropdown-subitem">
                                        <i class="fas fa-mobile-alt"></i>
                                        <span>Application</span>
                                    </a>
                                </div>
                            </div>
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-shopping-bag"></i>
                                <div>
                                    <div class="dropdown-item-title">Commandes de boutiques</div>
                                    <div class="dropdown-item-desc">Gérez toutes vos commandes</div>
                                </div>
                            </a>
                        </div>
                    </div>
                    
                    <a href="#pricing" class="nav-link" onclick="event.preventDefault(); const pricingEl = document.getElementById('pricing'); if(pricingEl) pricingEl.scrollIntoView({behavior: 'smooth'});">
                        <i class="fas fa-tags"></i>
                        <span>Tarif</span>
                    </a>
                @endif

                {{-- Menu pour pages de boutique : retiré car la boutique a son propre header --}}

                @auth
                    @if($hasStore)
                        <a href="{{ route('merchant.dashboard') }}" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    @endif
                    <form action="{{ url('/logout') }}" method="POST" class="nav-inline-form">
                        @csrf
                        <button type="submit" class="nav-link danger">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Déconnexion</span>
                        </button>
                    </form>
                @else
                    <a href="{{ url('/login') }}" class="nav-link">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Connexion</span>
                    </a>
                    <a href="{{ url('/register') }}" class="nav-link nav-link-primary">
                        <i class="fas fa-user-plus"></i>
                        <span>Créer un compte</span>
                    </a>
                @endauth
            </nav>
        </div>
    </header>
    @endif

    <main class="container main" @if($isPublicStore) style="padding-top: 0;" @endif>
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <h3 class="footer-col-title">
                        <i class="fas fa-store"></i>
                        Dropshipping Platform
                    </h3>
                    <p style="color: rgba(255,255,255,0.7); font-size: 14px; line-height: 1.6; margin: 0;">
                        La plateforme de dropshipping automatisée pour lancer et scaler ton business en toute simplicité.
                    </p>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h3 class="footer-col-title">
                        <i class="fas fa-link"></i>
                        Liens rapides
                    </h3>
                    <ul class="footer-links">
                        <li><a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a></li>
                        <li><a href="{{ url('/products') }}"><i class="fas fa-box"></i> Produits</a></li>
                        <li><a href="#pricing"><i class="fas fa-tags"></i> Tarifs</a></li>
                        <li><a href="{{ url('/register') }}"><i class="fas fa-user-plus"></i> Créer un compte</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3 class="footer-col-title">
                        <i class="fas fa-info-circle"></i>
                        Support
                    </h3>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-question-circle"></i> FAQ</a></li>
                        <li><a href="#"><i class="fas fa-book"></i> Documentation</a></li>
                        <li><a href="#"><i class="fas fa-headset"></i> Support</a></li>
                        <li><a href="#"><i class="fas fa-envelope"></i> Contact</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3 class="footer-col-title">
                        <i class="fas fa-shield-alt"></i>
                        Légal
                    </h3>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-file-contract"></i> Conditions d'utilisation</a></li>
                        <li><a href="#"><i class="fas fa-lock"></i> Politique de confidentialité</a></li>
                        <li><a href="#"><i class="fas fa-cookie"></i> Politique des cookies</a></li>
                        <li><a href="#"><i class="fas fa-gavel"></i> Mentions légales</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© {{ date('Y') }} Dropshipping Platform — Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- AOS Animation Library JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                AOS.init({
                    duration: 800,
                    easing: 'ease-in-out',
                    once: true,
                    offset: 100
                });
            });
        } else {
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true,
                offset: 100
            });
        }
    </script>
</body>
</html>

