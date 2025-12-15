<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Merchant - Dropshipping Platform')</title>
    
    <!-- Tailwind CSS -->
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="merchant-layout">
    <div class="merchant-wrapper">
        <!-- Sidebar -->
        <aside class="merchant-sidebar" id="merchantSidebar">
            <div class="sidebar-header">
                @php
                    $store = auth()->user()->store ?? null;
                @endphp
                @if($store)
                    <div class="store-name-sidebar">
                        <i class="fas fa-store icon-inline"></i>
                        <span>{{ $store->name }}</span>
                    </div>
                @else
                    <div class="store-name-sidebar">
                        <i class="fas fa-store icon-inline"></i>
                        <span>Ma Boutique</span>
                    </div>
                @endif
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('merchant.dashboard') }}" class="nav-item {{ request()->routeIs('merchant.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Accueil</span>
                </a>
                <a href="{{ route('merchant.sales') }}" class="nav-item {{ request()->routeIs('merchant.sales') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Ventes</span>
                </a>
                <a href="{{ route('merchant.products') }}" class="nav-item {{ request()->routeIs('merchant.products*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>Produits</span>
                </a>
                <a href="{{ route('merchant.store.customize') }}" class="nav-item {{ request()->routeIs('merchant.store.customize') ? 'active' : '' }}">
                    <i class="fas fa-palette"></i>
                    <span>Personnalisation</span>
                </a>
                <a href="{{ route('merchant.revenue') }}" class="nav-item {{ request()->routeIs('merchant.revenue') ? 'active' : '' }}">
                    <i class="fas fa-dollar-sign"></i>
                    <span>Revenu</span>
                </a>
                <a href="{{ route('merchant.orders') }}" class="nav-item {{ request()->routeIs('merchant.orders*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Commandes</span>
                </a>
                <a href="{{ route('merchant.margin') }}" class="nav-item {{ request()->routeIs('merchant.margin') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i>
                    <span>Marge</span>
                </a>
                <a href="{{ route('merchant.analytics') }}" class="nav-item {{ request()->routeIs('merchant.analytics') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Analytique</span>
                </a>
                <a href="{{ route('merchant.marketing') }}" class="nav-item {{ request()->routeIs('merchant.marketing') ? 'active' : '' }}">
                    <i class="fas fa-bullhorn"></i>
                    <span>Marketing</span>
                </a>
                <a href="{{ route('merchant.marketing') }}" class="nav-item {{ request()->routeIs('merchant.marketing') ? 'active' : '' }}">
                    <i class="fas fa-bullhorn"></i>
                    <span>Marketing</span>
                </a>
                <a href="{{ route('merchant.suppliers') }}" class="nav-item {{ request()->routeIs('merchant.suppliers*') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i>
                    <span>Fournisseurs</span>
                </a>
                <a href="{{ route('merchant.wallets') }}" class="nav-item {{ request()->routeIs('merchant.wallets*') ? 'active' : '' }}">
                    <i class="fas fa-wallet"></i>
                    <span>Wallets</span>
                </a>
                <a href="{{ route('merchant.settings') }}" class="nav-item {{ request()->routeIs('merchant.settings') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="plan-badge">
                    @php
                        $currentPlan = auth()->user()->currentPlan();
                    @endphp
                    <i class="fas fa-{{ $currentPlan && $currentPlan->slug === 'free' ? 'gift' : 'crown' }}"></i>
                    <span>{{ $currentPlan ? $currentPlan->name : 'Free' }}</span>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="merchant-main">
            <!-- Top Bar -->
            <header class="merchant-topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <div class="topbar-right">
                    @php
                        $store = auth()->user()->store ?? null;
                    @endphp
                    @if($store)
                        <a href="{{ $store->publicUrl }}" target="_blank" class="btn-view-store">
                            <i class="fas fa-external-link-alt icon-inline"></i>
                            Visualiser ma boutique
                        </a>
                    @endif
                    <div class="user-menu">
                        <span class="user-name">{{ auth()->user()->name }}</span>
                        <form action="{{ url('/logout') }}" method="POST" class="logout-form">
                            @csrf
                            <button type="submit" class="btn-logout">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="merchant-content">
                @if(session('success'))
                    <div class="alert alert-success" data-aos="fade-down">
                        <i class="fas fa-check-circle icon-inline"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error" data-aos="fade-down">
                        <i class="fas fa-exclamation-circle icon-inline"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
        
        // Toggle sidebar
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('merchantSidebar').classList.toggle('collapsed');
        });
    </script>
    @stack('scripts')
</body>
</html>

