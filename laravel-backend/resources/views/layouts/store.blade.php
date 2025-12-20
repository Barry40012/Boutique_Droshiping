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
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    @stack('styles')
</head>
<body class="page store-page">
    <div class="store-page-wrapper">
        @yield('content')
        
        @if(isset($store) && $store)
            @php
                $settings = $store->settings ?? [];
                $footerEmail = $settings['footer_email'] ?? ($store->user->email ?? 'contact@example.com');
                $footerBgColor = $store->footer_bg_color ?? '#1f2937';
                $footerTextColor = $store->footer_text_color ?? '#9ca3af';
                $footerLinkColor = $store->footer_link_color ?? '#ffffff';
                $footerTitleColor = $store->footer_title_color ?? '#ffffff';
            @endphp
            <!-- Footer de la boutique -->
            <footer class="store-footer" style="background: {{ $footerBgColor }}; color: {{ $footerTextColor }};">
                <div class="container">
                    <div class="footer-content">
                        <div class="footer-section">
                            <h3 style="color: {{ $footerTitleColor }};">{{ $store->name }}</h3>
                        </div>
                        <div class="footer-section">
                            <h4 style="color: {{ $footerTitleColor }};">Liens rapides</h4>
                            <ul>
                                <li><a href="{{ route('store.public', $store->slug) }}" style="color: {{ $footerLinkColor }};">Accueil</a></li>
                                <li><a href="{{ route('cart') }}" style="color: {{ $footerLinkColor }};">Panier</a></li>
                                <li><a href="{{ route('track.order') }}" style="color: {{ $footerLinkColor }};">Suivre ma commande</a></li>
                            </ul>
                        </div>
                        <div class="footer-section">
                            <h4 style="color: {{ $footerTitleColor }};">Contact</h4>
                            <p style="color: {{ $footerTextColor }};">Email: {{ $footerEmail }}</p>
                        </div>
                    </div>
                    
                    <!-- Moyens de paiement -->
                    <div class="payment-methods">
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
                        <p>&copy; {{ date('Y') }} {{ $store->name }}. Tous droits réservés.</p>
                    </div>
                </div>
            </footer>
        @endif
    </div>
    
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    @stack('scripts')
    
    <style>
        .store-page-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .store-page-wrapper > *:first-child {
            flex: 1;
        }
        
        .store-footer {
            margin-top: auto;
            padding: 3rem 0 1.5rem;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .footer-section h3,
        .footer-section h4 {
            margin: 0 0 1rem 0;
            font-size: 1.125rem;
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
            text-decoration: none;
            transition: opacity 0.2s;
        }
        
        .footer-section ul li a:hover {
            opacity: 0.8;
        }
        
        .payment-methods {
            text-align: center;
            padding: 2rem 0;
            border-top: 1px solid rgba(255,255,255,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin: 2rem 0;
        }
        
        .payment-icons {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
        }
        
        .payment-icon {
            font-size: 2rem;
            color: rgba(255,255,255,0.7);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 1.5rem;
            font-size: 0.875rem;
        }
        
        @media (max-width: 768px) {
            .footer-content {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
        }
    </style>
</body>
</html>

