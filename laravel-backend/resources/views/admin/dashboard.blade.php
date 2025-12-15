@extends('layouts.app')

@section('title', 'Admin - Dashboard')

@section('content')
<div class="merchant-dashboard-container">
    <!-- Header -->
    <div class="merchant-dashboard-header" data-aos="fade-down">
        <div>
            <h1 class="merchant-dashboard-title">Dashboard Admin</h1>
            <p class="merchant-dashboard-subtitle">Vue globale de la plateforme</p>
        </div>
        <div class="merchant-store-info">
            <div class="store-badge">
                <i class="fas fa-shield-alt icon-inline"></i>
                <span>Admin plateforme</span>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="merchant-stats-grid" data-aos="fade-up">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary), var(--secondary));">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">${{ number_format($stats['revenue']['total'] ?? 0, 2) }}</div>
                <div class="stat-label">Revenus total</div>
                <div class="stat-sublabel">Paiements reçus</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--accent), #ea580c);">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">${{ number_format($stats['revenue']['margin'] ?? 0, 2) }}</div>
                <div class="stat-label">Marge</div>
                <div class="stat-sublabel">Bénéfice net</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['orders']['total'] ?? 0 }}</div>
                <div class="stat-label">Commandes</div>
                <div class="stat-sublabel">{{ $stats['orders']['paid'] ?? 0 }} payées</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--secondary), #9333ea);">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['products']['active'] ?? 0 }}</div>
                <div class="stat-label">Produits actifs</div>
                <div class="stat-sublabel">Catalogue</div>
            </div>
        </div>
    </div>

    <!-- Actions Admin -->
    <div class="merchant-actions-grid" data-aos="fade-up" data-aos-delay="100">
        <a href="{{ url('/admin/products') }}" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, var(--primary), var(--secondary));">
                <i class="fas fa-box-open"></i>
            </div>
            <h3 class="action-title">Produits</h3>
            <p class="action-text">Gérer le catalogue</p>
        </a>

        <a href="{{ url('/admin/orders') }}" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, var(--accent), #ea580c);">
                <i class="fas fa-receipt"></i>
            </div>
            <h3 class="action-title">Commandes</h3>
            <p class="action-text">Suivre les commandes</p>
        </a>

        <a href="{{ url('/admin/suppliers') }}" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="fas fa-truck-loading"></i>
            </div>
            <h3 class="action-title">Fournisseurs</h3>
            <p class="action-text">Gérer les fournisseurs</p>
        </a>

        @if($store)
        <a href="{{ route('merchant.dashboard') }}" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, var(--secondary), #9333ea);">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="action-title">Dashboard merchant</h3>
            <p class="action-text">Voir ma boutique</p>
        </a>
        <a href="{{ route('merchant.store.customize') }}" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, var(--primary), var(--accent));">
                <i class="fas fa-palette"></i>
            </div>
            <h3 class="action-title">Personnaliser</h3>
            <p class="action-text">Logo, couleurs, template</p>
        </a>
        @else
        <a href="{{ route('merchant.store.create') }}" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, var(--primary), var(--accent));">
                <i class="fas fa-store"></i>
            </div>
            <h3 class="action-title">Créer ma boutique</h3>
            <p class="action-text">Démarrer en 1 minute</p>
        </a>
        @endif
    </div>

    @if(!$store)
    <div class="merchant-recent-orders" data-aos="fade-up" data-aos-delay="200">
        <div class="section-header">
            <h2 class="section-title">Étape 1 : Créer votre boutique</h2>
        </div>
        <p class="text-slate-600 text-sm">Cliquez sur “Créer ma boutique” pour commencer. Ensuite, choisissez un template, personnalisez le logo et ajoutez vos produits.</p>
    </div>
    @endif
</div>
@endsection

