@extends('layouts.app')

@section('title', 'Détail Commande - Dashboard Marchand')

@section('content')
<div class="merchant-order-detail-page">
    <div class="page-header" data-aos="fade-down">
        <div>
            <a href="{{ route('merchant.orders') }}" class="back-link">
                <i class="fas fa-arrow-left icon-inline"></i>
                Retour aux commandes
            </a>
            <h1 class="page-title">
                <i class="fas fa-file-invoice icon-inline"></i>
                Commande #{{ substr($order->id, 0, 8) }}
            </h1>
            <p class="page-subtitle">Détails complets de la commande</p>
        </div>
        <div class="header-actions">
            <span class="status-badge status-{{ $order->status }}">
                {{ ucfirst($order->status) }}
            </span>
            <span class="payment-badge payment-{{ $order->payment_status }}">
                {{ ucfirst($order->payment_status) }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" data-aos="fade-up">
            <i class="fas fa-check-circle icon-inline"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="order-detail-content">
        <!-- Informations générales -->
        <div class="order-section" data-aos="fade-up">
            <h2 class="section-title">
                <i class="fas fa-info-circle icon-inline"></i>
                Informations générales
            </h2>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">ID Commande</span>
                    <span class="info-value">{{ $order->id }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Date de commande</span>
                    <span class="info-value">{{ $order->created_at->format('d/m/Y à H:i') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Statut</span>
                    <span class="status-badge status-{{ $order->status }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Statut paiement</span>
                    <span class="payment-badge payment-{{ $order->payment_status }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
                @if($order->tracking_number)
                <div class="info-item">
                    <span class="info-label">Numéro de suivi</span>
                    <span class="info-value tracking-number">{{ $order->tracking_number }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Adresse de livraison -->
        <div class="order-section" data-aos="fade-up" data-aos-delay="100">
            <h2 class="section-title">
                <i class="fas fa-map-marker-alt icon-inline"></i>
                Adresse de livraison
            </h2>
            <div class="shipping-address">
                @if($order->shipping_address)
                    <div class="address-block">
                        <p class="address-name">
                            <i class="fas fa-user icon-inline"></i>
                            {{ $order->shipping_address['name'] ?? 'N/A' }}
                        </p>
                        <p class="address-line">
                            <i class="fas fa-map-marker-alt icon-inline"></i>
                            {{ $order->shipping_address['street'] ?? '' }}
                        </p>
                        <p class="address-line">
                            {{ ($order->shipping_address['postalCode'] ?? '') . ' ' . ($order->shipping_address['city'] ?? '') }}
                        </p>
                        <p class="address-line">{{ $order->shipping_address['country'] ?? '' }}</p>
                        @if(isset($order->shipping_address['phone']))
                        <p class="address-phone">
                            <i class="fas fa-phone icon-inline"></i>
                            {{ $order->shipping_address['phone'] }}
                        </p>
                        @endif
                    </div>
                @else
                    <p class="text-muted">Aucune adresse disponible</p>
                @endif
            </div>
        </div>

        <!-- Produits commandés -->
        <div class="order-section" data-aos="fade-up" data-aos-delay="200">
            <h2 class="section-title">
                <i class="fas fa-shopping-bag icon-inline"></i>
                Produits commandés
            </h2>
            <div class="order-items-table">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Prix unitaire</th>
                            <th>Quantité</th>
                            <th>Prix fournisseur</th>
                            <th>Marge</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="product-info">
                                        @if($item->product && $item->product->images && count($item->product->images) > 0)
                                            <img src="{{ $item->product->images[0] }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 class="product-thumb">
                                        @else
                                            <div class="product-thumb-placeholder">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <span class="product-name">
                                                {{ $item->product->name ?? 'Produit supprimé' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>${{ number_format($item->unit_price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($item->supplier_price, 2) }}</td>
                                <td class="margin-positive">
                                    ${{ number_format(($item->unit_price - $item->supplier_price) * $item->quantity, 2) }}
                                </td>
                                <td class="total-amount">
                                    ${{ number_format($item->unit_price * $item->quantity, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Résumé financier -->
        <div class="order-section" data-aos="fade-up" data-aos-delay="300">
            <h2 class="section-title">
                <i class="fas fa-calculator icon-inline"></i>
                Résumé financier
            </h2>
            <div class="financial-summary">
                <div class="summary-row">
                    <span class="summary-label">Montant total</span>
                    <span class="summary-value">${{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Coût fournisseur</span>
                    <span class="summary-value cost">${{ number_format($order->supplier_cost, 2) }}</span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-row summary-total">
                    <span class="summary-label">Marge réalisée</span>
                    <span class="summary-value margin-positive">${{ number_format($order->margin, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init();
</script>
@endsection

