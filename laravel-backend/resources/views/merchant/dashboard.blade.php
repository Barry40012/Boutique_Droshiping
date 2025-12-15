@extends('layouts.merchant')

@section('title', 'Dashboard Merchant - Dropshipping Platform')

@section('content')
<div class="merchant-dashboard-container">
    <!-- Message plan Free -->
    @if(isset($currentPlan) && $currentPlan && $currentPlan->slug === 'free')
    <div class="free-plan-notice" data-aos="fade-down">
        <div class="notice-content">
            <i class="fas fa-gift notice-icon"></i>
            <div>
                <h3>Vous êtes actuellement sur la version Free</h3>
                <p>Découvrez toutes les fonctionnalités de la plateforme. Vous pouvez migrer vers la version Pro pour bénéficier de plus de fonctionnalités.</p>
            </div>
            <a href="#" class="btn-primary btn-sm">
                <i class="fas fa-arrow-up icon-inline"></i>
                Passer au Pro
            </a>
        </div>
    </div>
    @endif

    <div class="merchant-dashboard-header" data-aos="fade-down">
        <div>
            <h1 class="merchant-dashboard-title">Accueil</h1>
            <p class="merchant-dashboard-subtitle">Bienvenue dans votre espace merchant</p>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="merchant-stats-grid" data-aos="fade-up">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary), var(--secondary));">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['products']['total'] }}</div>
                <div class="stat-label">Produits</div>
                <div class="stat-sublabel">{{ $stats['products']['active'] }} actifs</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--accent), #ea580c);">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['orders']['total'] }}</div>
                <div class="stat-label">Commandes</div>
                <div class="stat-sublabel">{{ $stats['orders']['paid'] }} payées</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">${{ number_format($stats['revenue']['total'], 2) }}</div>
                <div class="stat-label">Revenus</div>
                <div class="stat-sublabel">Total généré</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--secondary), #9333ea);">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">${{ number_format($stats['revenue']['margin'], 2) }}</div>
                <div class="stat-label">Marge</div>
                <div class="stat-sublabel">Bénéfice net</div>
            </div>
        </div>
    </div>

    <!-- Suggestion d'ajouter un produit si aucun produit -->
    @if($store && $stats['products']['total'] === 0)
    <div class="add-first-product-card" data-aos="fade-up">
        <div class="card-content">
            <div class="card-icon">
                <i class="fas fa-box-open"></i>
            </div>
            <div class="card-text">
                <h3>Ajoutez votre premier produit</h3>
                <p>Commencez à vendre en ajoutant votre premier produit à votre boutique.</p>
            </div>
            <a href="{{ route('merchant.products.create') }}" class="btn-primary btn-large">
                <i class="fas fa-plus icon-inline"></i>
                Ajouter un produit
            </a>
        </div>
    </div>
    @endif

    <!-- Partager la boutique -->
    @if($store)
    <div class="share-store-card" data-aos="fade-up" data-aos-delay="50">
        <div class="card-content">
            <div class="card-icon">
                <i class="fas fa-share-alt"></i>
            </div>
            <div class="card-text">
                <h3>Partager votre boutique</h3>
                <p>Partagez le lien de votre boutique avec vos clients</p>
            </div>
            <div class="share-link-input">
                <input type="text" 
                       id="storeLink" 
                       value="{{ $store->publicUrl }}" 
                       readonly 
                       class="form-input">
                <button type="button" 
                        class="btn-secondary" 
                        onclick="copyStoreLink()">
                    <i class="fas fa-copy icon-inline"></i>
                    Copier
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Actions rapides -->
    <div class="merchant-actions-grid" data-aos="fade-up" data-aos-delay="100">

        <a href="{{ route('merchant.store.customize') }}" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, var(--primary), var(--secondary));">
                <i class="fas fa-palette"></i>
            </div>
            <h3 class="action-title">Personnaliser</h3>
            <p class="action-text">Logo, couleurs, nom de la boutique</p>
        </a>

        <a href="{{ route('merchant.products') }}" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, var(--accent), #ea580c);">
                <i class="fas fa-box-open"></i>
            </div>
            <h3 class="action-title">Produits</h3>
            <p class="action-text">Gérer votre catalogue</p>
        </a>

        <a href="{{ route('merchant.orders') }}" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h3 class="action-title">Commandes</h3>
            <p class="action-text">Suivre les commandes</p>
        </a>

        <a href="{{ route('merchant.payment.settings') }}" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, var(--secondary), #9333ea);">
                <i class="fas fa-credit-card"></i>
            </div>
            <h3 class="action-title">Paiement</h3>
            <p class="action-text">Configurer le paiement</p>
        </a>
    </div>

    <!-- Commandes récentes -->
    @if($recentOrders->count() > 0)
    <div class="merchant-recent-orders" data-aos="fade-up" data-aos-delay="200">
        <div class="section-header">
            <h2 class="section-title">Commandes récentes</h2>
            <a href="{{ route('merchant.orders') }}" class="section-link">
                Voir tout <i class="fas fa-arrow-right icon-inline"></i>
            </a>
        </div>
        <div class="orders-list">
            @foreach($recentOrders as $order)
            <div class="order-item">
                <div class="order-info">
                    <div class="order-id">#{{ substr($order->id, 0, 8) }}</div>
                    <div class="order-date">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="order-amount">${{ number_format($order->total_amount, 2) }}</div>
                <div class="order-status">
                    <span class="status-badge status-{{ $order->payment_status }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init();
</script>
@endsection

