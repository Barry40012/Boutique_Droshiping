@extends('layouts.app')

@section('title', 'Commandes - Dashboard Marchand')

@section('content')
<div class="merchant-orders-page">
    <div class="page-header" data-aos="fade-down">
        <div>
            <h1 class="page-title">
                <i class="fas fa-shopping-bag icon-inline"></i>
                Gestion des commandes
            </h1>
            <p class="page-subtitle">Suivez et gérez toutes les commandes de votre boutique</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('merchant.dashboard') }}" class="btn-secondary">
                <i class="fas fa-arrow-left icon-inline"></i>
                Retour au dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" data-aos="fade-up">
            <i class="fas fa-check-circle icon-inline"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error" data-aos="fade-up">
            <i class="fas fa-exclamation-circle icon-inline"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Filtres -->
    <div class="orders-filters" data-aos="fade-up">
        <form method="GET" action="{{ route('merchant.orders') }}" class="filters-form">
            <div class="filter-group">
                <label for="status" class="filter-label">
                    <i class="fas fa-filter icon-inline"></i>
                    Statut
                </label>
                <select name="status" id="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Payée</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>En traitement</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Expédiée</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Livrée</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulée</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="payment_status" class="filter-label">
                    <i class="fas fa-credit-card icon-inline"></i>
                    Paiement
                </label>
                <select name="payment_status" id="payment_status" class="filter-select" onchange="this.form.submit()">
                    <option value="">Tous les paiements</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Payé</option>
                    <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Échoué</option>
                    <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Remboursé</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="search" class="filter-label">
                    <i class="fas fa-search icon-inline"></i>
                    Recherche
                </label>
                <input type="text" name="search" id="search" class="filter-input" 
                       placeholder="ID commande, nom client..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn-primary">
                <i class="fas fa-search icon-inline"></i>
                Filtrer
            </button>
            @if(request()->hasAny(['status', 'payment_status', 'search']))
                <a href="{{ route('merchant.orders') }}" class="btn-secondary">
                    <i class="fas fa-times icon-inline"></i>
                    Réinitialiser
                </a>
            @endif
        </form>
    </div>

    <!-- Liste des commandes -->
    <div class="orders-list" data-aos="fade-up" data-aos-delay="100">
        @if($orders->count() > 0)
            <div class="orders-table-container">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>ID Commande</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Marge</th>
                            <th>Statut</th>
                            <th>Paiement</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                                <td>
                                    <span class="order-id">#{{ substr($order->id, 0, 8) }}</span>
                                </td>
                                <td>
                                    <div class="customer-info">
                                        <i class="fas fa-user icon-inline"></i>
                                        <span>{{ $order->shipping_address['name'] ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="date-info">
                                        <i class="fas fa-calendar icon-inline"></i>
                                        <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="amount">${{ number_format($order->total_amount, 2) }}</span>
                                </td>
                                <td>
                                    <span class="margin">${{ number_format($order->margin, 2) }}</span>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $order->status }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="payment-badge payment-{{ $order->payment_status }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('merchant.orders.show', $order->id) }}" 
                                           class="btn-icon btn-view" title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-container" data-aos="fade-up">
                {{ $orders->links() }}
            </div>
        @else
            <div class="empty-state" data-aos="fade-up">
                <i class="fas fa-inbox empty-icon"></i>
                <h2 class="empty-title">Aucune commande trouvée</h2>
                <p class="empty-text">
                    @if(request()->hasAny(['status', 'payment_status', 'search']))
                        Aucune commande ne correspond à vos critères de recherche.
                    @else
                        Vous n'avez pas encore de commandes. Les commandes apparaîtront ici une fois que vos clients commenceront à passer des commandes.
                    @endif
                </p>
                @if(request()->hasAny(['status', 'payment_status', 'search']))
                    <a href="{{ route('merchant.orders') }}" class="btn-primary">
                        <i class="fas fa-arrow-left icon-inline"></i>
                        Voir toutes les commandes
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init();
</script>
@endsection

